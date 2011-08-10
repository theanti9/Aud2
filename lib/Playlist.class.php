<?php

class Playlist {
	public $name;
	private $playlistid;
	private $userid;
	private $songlist;
	private $_pdoConn;
	
	public function __construct(PDO &$pdo, $userid, $name, $id=NULL) {
		$this->_pdoConn = $pdo;
		$this->userid = $userid;
		$this->name = $name;
		$this->playlistid = $id;
	}
	
	public function __set($var, $val) {
		$this->$var = $val;
		$this->Update();
	}
	
	public function Update() {
		try{
			if ($this->playlistid == NULL) {
				$sth = $this->_pdoConn->prepare("INSERT INTO playlists VALUES(NULL, :userid, :name)");
				$sth->bindValue(":userid", $this->_pdoConn->quote($this->userid));
				$sth->bindValue(":name", (!empty($this->name)) ? $this->name : "Untitled");
				if (!$sth->execute()) {
					return false;
				}
				return true;
			} else {
				if ($this->name == NULL) {
					$sth = $this->_pdoConn->prepare("SELECT * FROM playlists WHERE userid = :userid AND playlistid = :playlistid");
					$sth->bindValue(":userid", $this->_pdoConn->quote($this->userid));
					$sth->bindValue(":playlistid", $this->_pdoConn->quote($this->playlistid));
					if (!$sth->execute()) {
						return false;
					}
					if ($sth->rowCount() == 0) {
						return false;
					}
					$arr = $sth->fetch();
					$this->name = $arr['name'];
					return true;
				} else {
					$sth = $this->_pdoConn->prepare("UPDATE palylists SET name = :name WHERE playlistid = :playlistid");
					$sth->bindValue(":name", $this->_pdoConn->quote($this->name));
					$sth->bindValue(":playlistid", $this->_pdoConn->quote($this->playlistid));
					if (!$sth->execute()) {
						return false;
					}
					return true;
				}
			}
		} catch (PDOException $e) {
			return $e;
		}
	}
	
	public function GetPlaylistJson() {
		if (!$this->_pdoConn) {
			return false;
		}
		try {
			include_once("Song.class.php");
			$sth = $this->_pdoConn->prepare("SELECT * FROM playlistsongs WHERE playlistid = :playlistid");
			$sth->bindValue(":playlistid", $this->playlistid);
			if (!$sth->execute()) {
				return false;
			}
			if ($sth->rowCount() == 0) {
				return json_encode(array());
			}
			$this->songlist = $sth->fetchAll(PDO::FETCH_CLASS, "Song");
			return json_encode($this->songlist);
		} catch (PDOException $e) {
			return $e;
		}
	}
	
	public function AddToPlaylist($songs) {
		
		if (!$this->_pdoConn) {
			return false;
		}
		try{
			if (is_array($songs)) {
				include_once("StringBuilder.class.php");
				$q = new StringBuilder();
				$q->append("INSERT INTO playlistsongs (playlistid, songid) VALUES");
				foreach ($songs as $song) {
					$q->append("(")->append($this->playlistid)->append(",")->append($song->songid)->append("),");
				}
				if ($this->_pdoConn->exec(substr($q->tostring(), 0, -1)) == 0) {
					return false;
				}
				return true;
			} else {
				if ($q->exec("INSERT INTO playlistsongs (playlistid, songid) VALUES (".$this->playlistid.", ".$songs->songid.")") == 0) {
					return false;
				}
				return true;
			}
			
		} catch (PDOException $e) {
			return $e;
		}
	}
	
	public static function GetUsersPlaylists(PDO &$pdo, $userid) {
		if (!$pdo || !$userid) {
			return false;
		}
		try {
			$sth = $pdo->prepare("SELECT * FROM playlists WHERE userid = :userid");
			$sth->bindValue(":userid", $pdo->quote($userid));
			if (!$sth->execute()) {
				return false;
			}
			return json_encode($sth->fetchAll(PDO::FETCH_CLASS, "Playlist"));
		} catch (PDOException $e) {
			return $e;
		}
	}
	
}

?>