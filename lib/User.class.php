<?php

class User {
	
	private $_id;
	private $_phash;
	private $_pdoConn;
 	protected $username;
	
	public function __construct(PDO &$pdo, $username) {
		$this->username = $username;
		$this->_pdoConn = &$pdo;
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
					//echo "no user...adding<br />";
					return $this->Add();
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
		include_once('BCrypt.class.php');
		$bc = new Bcrypt(15);
		$this->_phash = $bc->hash($pass);
		return $this->Update();
	}
	
	public function ValidatePassword($pass) {
		include_once('BCrypt.class.php');
		$bc = new Bcrypt(15);
		return $bc->verify($pass, $this->_phash);
		
	}
	
	public function GetLibraryJson() {
		if (!$this->_pdoConn) {
			return false;
		}
		try {
			include_once("Song.class.php");
			$sth = $this->_pdoConn->prepare("SELECT * FROM songs WHERE userid = :userid");
			$sth->bindValue(":userid", $this->_id);
			if (!$sth->execute()) {
				return false;
			}
			if ($sth->rowCount() == 0) {
				return json_encode(array());
			}
			return json_encode($sth->fetchAll(PDO::FETCH_CLASS, "Song"));
		} catch (PDOException $e) {
			return $e;
		}
	}

}

?>