<?php

class Song {
	public $songid;
	public $artist;
	public $title;
	public $album;
	public $track;
	public $genre;
	public $year;
	public $songpath;
	
	private $userid;
	private $_pdoConn;
	private $given_data;
	
	public function __construct(PDO &$pdo, $id=NULL, $songpath=NULL, $dataArr=NULL, $userid=NULL) {
		$this->_pdoConn = $pdo;
		$this->songid = $id;
		$this->songpath = $songpath;
		$this->userid = $userid;
		if ($dataArr) {
			foreach($dataArr as $key=>$value) {
				$this->$key = $value;
			}
			$this->given_data = true;
		}
		$this->Update();
	}
	
	public function __get($name) {
	    return isset($this->$name) ? $this->$name : null;
	}
	
	public function __set($var, $val) {
		$this->Update();
	}
	
	
	public function Add() {
		try {
			if (!$this->_pdoConn) {
				return false;
			}
			if (!$this->given_data) {
				include 'getid3/getid3.php';
				$getID3 = new getID3;
				$tag = $getID3->analyze($this->songpath);
				getid3_lib::CopyTagsToComments($tag);
				$this->artist = $this->first($tag['comments_html']['artist']);
				$this->title = ($this->first($tag['comments_html']['title'])) ? $this->first($tag['comments_html']['title']) : substr($this->songpath, strrpos($this->songpath,"/")+1);
				$this->album = $this->first($tag['comments_html']['album']);
				$this->track = "0";
				$this->genre = $this->first($tag['comments_html']['genre']);
				$this->year = $this->first($tag['comments_html']['year']);
			}
			if ($this->userid == NULL) {
				return false;
			}
			$sth = $this->_pdoConn->prepare("INSERT INTO songs VALUES (NULL, :userid, :path, :artist, :title, :album, :track, :genre, :year)");
			$sth->bindValue(":userid",$this->userid);
			$sth->bindValue(":path", $this->songpath);
			$sth->bindValue(":artist", $this->artist);
			$sth->bindValue(":title", $this->title);
			$sth->bindValue(":album", $this->album);
			$sth->bindValue(":track", $this->track);
			$sth->bindValue(":genre", $this->genre);
			$sth->bindValue(":year", $this->year);
			
			if (!$sth->execute()) {
				throw new Exception("Add failed..<br />".print_r($sth->errorInfo()));
			}
			$this->songid = $this->_pdoConn->lastInsertId();
			return true;
			
		} catch (PDOException $e) {
			return $e;
		}		
	}
	
	public function Update() {
		try{
			if ($this->songid == NULL) {
				if ($this->songpath == NULL) {
					return false;
				}
				$sth = $this->_pdoConn->query("SELECT * FROM songs WHERE songpath = '" . $this->songpath . "'");
				if ($sth->rowCount() == 0) {
					return $this->Add();
				} else {
					$arr = $sth->fetch();
					$this->songid = $arr['songid'];
					$this->userid = $arr['userid'];
					$this->songpath = $arr['songpath'];
					$this->artist = $arr['artist'];
					$this->title = $arr['title'];
					$this->album = $arr['album'];
					$this->track = $arr['track'];
					$this->genre = $arr['genre'];
					$this->year = $arr['year'];
					return true;
				}
			} else {
				if ($this->songpath == NULL) {
					$sth = $this->_pdoConn->prepare("SELECT * FROM songs WHERE songid = :songid");
					$sth->bindValue(":songid", $this->songid);
					if (!$sth->execute()) {
						throw new Exception("Select failed..<br />".print_r($sth->errorInfo()));
					}
					if ($sth->rowCount() == 0) {
						return false;
					} else {
						$arr = $sth->fetch();
						$this->userid = $arr['userid'];
						$this->songpath = $arr['songpath'];
						$this->artist = $arr['artist'];
						$this->title = $arr['title'];
						$this->album = $arr['album'];
						$this->track = $arr['track'];
						$this->genre = $arr['genre'];
						$this->year = $arr['year'];
						return true;
					}
				} else {
					echo "updating values";
					$sth = $this->_pdoConn->prepare("UPDATE songs SET songpath = :path, artist = :artist, title = :title, album = :album, track = :track, genre = :genre, year = :year WHERE songid = :songid");
					$sth->bindValue(":path", $this->songpath);
					$sth->bindValue(":artist", $this->artist);
					$sth->bindValue(":title", $this->title);
					$sth->bindValue(":album", $this->album);
					$sth->bindValue(":track", $this->track);
					$sth->bindValue(":genre", $this->genre);
					$sth->bindValue(":year", $this->year);
					$sth->bindValue(":songid", $this->songid);
					if (!$sth->execute()) {
						throw new Exception("Value update failed..<br />".print_r($sth->errorInfo()));
					}
					return true;
					
				}
			}
		} catch (PDOException $e) {
			return $e;
		}
	}
	
	public function jsonify() {
		return json_encode(array("id"=>$this->songid,
								 "path"=>$this->songpath,
								 "artist"=>$this->artist,
								 "title"=>$this->title,
								 "album"=>$this->album,
								 "track"=>$this->track,
								 "genre"=>$this->genre,
								 "year"=>$this->year));
	}
	
	private function first($arr) {
		if (is_array($arr)) {
			if (count($arr) > 0) {
				return $arr[0];
			}
			return "";
		}
		return "";
	}
	
}


?>