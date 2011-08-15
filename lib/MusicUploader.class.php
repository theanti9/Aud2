<?php

class MusicUploader {
	
	private $uploaddir;
	private $good_types;
	private $extractdir;
	private $settings;
	public function __construct($uploaddir, $extractdir, $good_types, Settings $settings) {
		if (!is_dir($uploaddir)) {
			throw new Exception("No upload directory!");
		}
		if (!is_dir($extractdir)) {
			throw new Exception("No extract directory!");
		}
		$this->uploaddir = $uploaddir;
		$this->extractdir = $extractdir;
		$this->good_types = ($good_types) ? $good_types : array("audio/mpeg", "audio/ogg", "audio/wav", "audio/mp4", "audio/mp3");
		$this->settings = $settings;
	}
	
	public function ProcessSingles(PDO &$pdo, $files, User $user) {
		$skipped = array();
		$endfiles = array();
		foreach ($files as $file) {
			$path = $user->username."/";
			if (!is_dir($this->uploaddir."/".$path)) {
				mkdir($this->uploaddir."/".$path);
			}
			if (!in_array($file['type'],$this->good_types)) {
				echo "type error";
				$skipped[] = $file['name'];
				continue;
			}
			if ($file['error'] > 0) {
				echo "file error";
				$skipped[] = $file['name'];
				continue;
			}
			
			//$tag['comments_html'] = id3_get_tag($file['tmp_name'], ID3_V2_3);
			include_once "getid3/getid3.php";
			$getID3 = new getID3;
			$tag = $getID3->analyze($file['tmp_name']);
			getid3_lib::CopyTagsToComments($tag);
			//print_r($tag);
			$artistdir = (!empty($tag['comments_html']['artist'][0])) ? $tag['comments_html']['artist'][0] : "Unknown";
			$path = html_entity_decode($path);
			$path = preg_replace('/[^(\x20-\x7F)\&\(\);]*/','', $path);
			$path .= $artistdir;
			$path .= "/";
			if (!is_dir($this->uploaddir."/".$path)) {
				mkdir($this->uploaddir."/".$path);
			}
			$albumdir = (!empty($tag['comments_html']['album'][0])) ? $tag['comments_html']['album'][0] : "Unkown";
			$path = html_entity_decode($path);
			$path = preg_replace('/[^(\x20-\x7F)\&\(\);]*/','', $path);
			$path .= $albumdir;
			$path .= "/";
			if (!is_dir($this->uploaddir."/".$path)) {
				mkdir($this->uploaddir."/".$path);
			}
			
			$path .= $file['name'];
			//echo $path;
			
			while (file_exists($this->uploaddir."/".$path)) {
				$path = substr($path, 0, strrpos("$path", ".")) . "_" . substr($path, -4);
			}
			if (!is_uploaded_file($file['tmp_name']) || strpos($path, "..")) {
				return false;
			}
			$path = html_entity_decode($path);
			$path = preg_replace('/[^(\x20-\x7F)]*/','', $path);
			if (!move_uploaded_file($file['tmp_name'], "{$this->uploaddir}/".$path)) {
				echo "move to {$this->uploaddir}/{$path} failed";
				$skipped[] = $file['name'];
				//continue;
			}
			$endfiles[] = array("name"=>$file['name'], "size"=>$file['size'], "url"=>"http://localhost:8888/upload/".$path, "thumbnail_url"=>"http://aux4.iconpedia.net/uploads/8421418941663827560.png", "delete_type"=>"DELETE");
			$song = new Song($pdo,$this->settings,NULL,"{$this->uploaddir}/".$path,$tag['comments_html'], $user->getID());
			//$song->Update();
		}
		//print_r($endfiles);
		return $endfiles;
		// echo "skipped:";
		//print_r($skipped);
		//if ($skipped) return $skipped;
		//return true;
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
				$tag['comments_html'] = id3_get_tag($tmppath, ID3_V2_3);
				$artistdir = (!empty($tag['comments_html']['artist'])) ? $tag['comments_html']['artist'] : "Unknown";
				$path .= $artistdir;
				$path .= "/";
				if (!is_dir($path)) {
					mkdir($path);
				}
				$albumdir = (!empty($tag['comments_html']['album'])) ? $tag['comments_html']['album'] : "Unkown";
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

				$song = new Song($pdo,$this->settings,NULL,$path,$tag['comments_html']);
				//$song->Update();
			}
		}
		if ($skipped) return $skipped;
		return true;
	}
	
}

?>