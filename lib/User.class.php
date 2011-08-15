<?php

class User {
	
	public $username;

	private $_id;
	private $_phash;
	private $_pdoConn;
 	private $settings;

	
	public function __construct(PDO &$pdo, $username, $settings=NULL) {
		$this->username = $username;
		$this->_pdoConn = &$pdo;
		$this->settings = $settings;
		$this->Update();
	}
	
	public function __get($name) {
	    return isset($this->$name) ? $this->$name : null;
	}
	
	public function getID() {
		return (isset($this->_id)) ? $this->_id : NULL;
	}
	
	public function Add() {
		if (!$this->_pdoConn) {
			throw new Exception("Add failed");
		}
		try {
			//echo "Adding<br />";
			$sth = $this->_pdoConn->prepare("INSERT INTO users VALUES(NULL, :username, NULL)");
			$sth->bindValue(":username", $this->username);
			if (!$sth->execute()) {
				throw new Exception("Add query failed..<br />" . print_r($sth->errorInfo()));
			}
			$this->_id = $this->_pdoConn->lastInsertId();
			//echo $this->_id . "<br />";
			return true;
		} catch (PDOException $e) {
			return $e;
		}
	}
	
	public function Update() {
		if (!$this->_pdoConn) {
			//return false;
			throw new Exception("Update failed");
		}
		try {
			if ($this->_id == NULL){
				//echo "grabbing username<br />";
				$sth = $this->_pdoConn->prepare("SELECT * FROM users WHERE username = :username" );
				$sth->bindValue(":username", $this->username);
				$sth->execute();
				if ($sth->rowCount() == 0) {
					// No user
					if (isset($this->_phash)) {
						return $this->Add();
					}
					return false;
				} else {
					$arr = $sth->fetch();
					$this->_id = $arr['userid'];
					$this->_phash = $arr['password'];
					return true;
				}
			} else {
				$sth = $this->_pdoConn->prepare("UPDATE users SET password = :password WHERE userid = :userid");
				$sth->bindValue(":password", $this->_phash);
				$sth->bindValue(":userid", $this->_id);
				if (!$sth->execute()) {
					return false;
				}
				return true;
			}
			
		} catch (PDOException $e) {
			return $e;
		}
		
	}
	
	public function SetPassword($pass) {
		include_once("{$this->settings->BasePath}/lib/BCrypt.class.php");
		$bc = new Bcrypt(15);
		$this->_phash = $bc->hash($pass);
		return $this->Update();
	}
	
	public function ValidatePassword($pass) {
		include_once("{$this->settings->BasePath}/lib/BCrypt.class.php");
		$bc = new Bcrypt(15);
		return $bc->verify($pass, $this->_phash);
		
	}
	
	public function GetLibraryJson() {
		if (!$this->_pdoConn) {
			throw new Exception("Invalid database object");
		}
		try {
			include_once("{$this->settings->BasePath}/lib/Song.class.php");
			$sth = $this->_pdoConn->prepare("SELECT * FROM songs WHERE userid = :userid");
			$sth->bindValue(":userid", $this->_id);
			if (!$sth->execute()) {
				$info = $sth->errorInfo();
				throw new Exception($info[2]);
			}
			if ($sth->rowCount() == 0) {
				return json_encode(array());
			}
			$all = $sth->fetchAll();
			$ret = array();
			foreach($all as $song) {
				$ret[] = array( "songid"=>$song['songid'],
								"title"=>$song['title'],
								"artist"=>$song['artist'],
								"album"=>$song['album'],
								"url"=>substr($song['songpath'],strlen($this->settings->BasePath)));
			}
			return json_encode($ret);
		} catch (PDOException $e) {
			return $e;
		}
	}

}

?>