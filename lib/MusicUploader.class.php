<?php

class MusicUploader {
	
	private $uploaddir;
	private $good_types;
	private $extractdir;
	
	public function __construct($uploaddir, $extractdir, $good_types) {
		if (!is_dir($uploaddir)) {
			throw Exception("No upload directory!");
		}
		if (!is_dir($extractdir)) {
			throw Exception("No extract directory!");
		}
		$this->uploaddir = $uploaddir;
		$this->extractdir = $extractdir;
		$this->good_types = ($good_types) ? $good_types : array("audio/mpeg", "audio/ogg", "audio/wav", "audio/mp4");
	}
	
	public function ProcessSingles(PDO &$pdo, $files, User $user) {
		$path .= "/".$user->username."/";
		
		$skiped = array();
		foreach ($files as $file) {
			if (!in_array($file['type'],$this->good_types)) {
				$skipped[] = $file['name'];
				continue;
			}
			if ($file['error'] > 0) {
				$skipped[] = $file['name'];
			}
			
			$tag = id3_get_tag($file['tmp_name'], ID3_V2_3);
			$artistdir = (!empty($tag['artist'])) ? $tag['artist'] : "Unknown";
			$path .= $artistdir;
			$path .= "/";
			if (!is_dir($path)) {
				mkdir($path);
			}
			$albumdir = (!empty($tag['album'])) ? $tag['album'] : "Unkown";
			$path .= $albumdir;
			$path .= "/";
			if (!is_dir($path)) {
				mkdir($path);
			}
			
			$path .= $file['name'];
			
			if (file_exists($path)) {
				$path = substr($path, 0, strrpos("$path", ".")) . "_" . substr($path, -3);
			}
			if (!is_uploaded_file($file['tmp_name']) || strpos($path, "..")) {
				return false;
			}
			if (!move_uploaded_file($file['tmp_name'], $path)) {
				$skipped[] = $file['name'];
			}
			$song = new Song($pdo,NULL,$path,$tag);
			$song->Update();
		}
		if ($skipped) return $skipped;
		return true;
	}
	
	public function ProcessZips(PDO &$pdo, $files, User $user) {
		$skipped = array();
		foreach ($files as $file) {
			if ($file['type'] != "application/zip") {
				$skipped[] = $file['name'];
				continue;
			}
			$zip = new ZipArchive;
			if ($zip->open($file['tmp_name']) === TRUE) {
				$to = $this->extractdir. "/" . $user->username;
				if (!is_dir($to)) {
					mkdir($to);
				}
				$zip->extractTo($to);
				$zip->close();
			} else {
				$skipped[] = $file['name'];
				continue;
			}
		}
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->extractdir . "/" . $user->username));
		while ($it->isValid()) {
			if (!$it->isDot()) {
				$tmppath = $it->key();
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				if (!in_array(finfo_file($finfo,$tmppath),$this->good_types)) {
					unlink($tmppath);
				}
				$path = $this->uploaddir . "/";
				$tag = id3_get_tag($tmppath, ID3_V2_3);
				$artistdir = (!empty($tag['artist'])) ? $tag['artist'] : "Unknown";
				$path .= $artistdir;
				$path .= "/";
				if (!is_dir($path)) {
					mkdir($path);
				}
				$albumdir = (!empty($tag['album'])) ? $tag['album'] : "Unkown";
				$path .= $albumdir;
				$path .= "/";
				if (!is_dir($path)) {
					mkdir($path);
				}

				$path .= substr($it->key(), strrpos($key, "/"+1));

				if (file_exists($path)) {
					$path = substr($path, 0, strrpos("$path", ".")) . "_" . substr($path, -3);
				}
				rename($tmppath, $path);

				$song = new Song($pdo,NULL,$path,$tag);
				$song->Update();
			}
		}
		if ($skipped) return $skipped;
		return true;
	}
	
}

?>