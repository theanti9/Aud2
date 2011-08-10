<?php
include 'unit_tester.php';
include 'autorun.php';
include '../lib/User.class.php';
class TestOfUserClass extends UnitTestCase {
	function testConstructor() {
		$pdo = new PDO("mysql:host=localhost;dbname=aud2", "root", "");
		$user = new User($pdo, "wite");
		$this->assertTrue($user->getID() != NULL);
		$sth = $pdo->prepare("SELECT * FROM users WHERE username = 'wite'");
		$sth->execute();
		$this->assertTrue($sth->rowCount() > 0);
	}
	
	function testPasswordFuncs() {
		$pdo = new PDO("mysql:host=localhost;dbname=aud2", "root", "");
		$user = new User($pdo, "wite");
		$user->SetPassword("password");
		$this->assertTrue($user->ValidatePassword("password"));
		$this->assertFalse($user->ValidatePassword("something"));
	}
	
	function testLibrary() {
		$pdo = new PDO("mysql:host=localhost;dbname=aud2", "root", "");
		$user = new User($pdo, "wite");
		$sth = $pdo->query("SELECT * FROM songs WHERE userid = ".$user->getID());
		if ($sth->rowCount() == 0) {
			$this->assertTrue($user->GetLibraryJson() == json_encode(array()));
		} else {
			$this->assertTrue(json_encode($sth->fetchAll()) == $user->GetLibraryJson());
		}
	}
}

class TestOfSongClass extends UnitTestCase {
	function testSongConstructor() {
		$pdo = new PDO("mysql:host=localhost;dbname=aud2", "root", "");
		$data_arr = array(
			"artist"=>"The Luna Sequence",
			"title"=>"The Collective Voice",
			"album"=>"They Follow You Home",
			"track"=>"10",
			"genre"=>"Electronic",
			"year"=>"2009",
			"userid"=>"1",
			"songpath"=>"D:\\Music\\The Luna Sequence\\They Follow You Home\\10 The Collective Voice.mp3"
		);
		$song = new Song($pdo, null, null, $data_arr);
		foreach($data_arr as $k=>$v) {
			if ($k != "userid" && $k != "songpath") {
				$this->assertTrue($song->$k == $v);
			}
		}
		$this->assertTrue(isset($song->songid));
		
	}
	
	function testSongSetandUpdate() {
		$pdo = new PDO("mysql:host=localhost;dbname=aud2", "root", "");
		$data_arr = array(
					"artist"=>"The Luna Sequence",
					"title"=>"The Collective Voice",
					"album"=>"They Follow You Home",
					"track"=>"10",
					"genre"=>"Electronic",
					"userid"=>"1",
					"songpath"=>"D:\\Music\\The Luna Sequence\\They Follow You Home\\10 The Collective Voice.mp3"
		);
		$song = new Song($pdo, null, null, $data_arr);
		foreach($data_arr as $k=>$v) {
			if ($k != "userid" && $k != "songpath") {
				$this->assertTrue($song->$k == $v);
			}
		}
		$song->year = "2010";
		$song->Update();
		$this->assertTrue($song->year == "2010");
		$sth = $pdo->query("SELECT * FROM songs WHERE songid = ".$song->songid);
		$f = $sth->fetch();
		$this->assertTrue($f["year"] == $song->year);
		
		$song2 = new Song($pdo, null, $data_arr['songpath'], null, 1);
		$this->assertTrue(isset($song2->album) && isset($song2->artist) && isset($song2->genre) && isset($song2->songid) && isset($song2->title) && isset($song2->track) && isset($song2->year));
	}
}

?>