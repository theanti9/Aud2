<?php

// Make sure we have what we need
if (count($_FILES) == 0 || !isset($_POST['upload_type']) || !isset($_POST['username'])) {
	print_r($_FILES);
	echo "Missing arguments";
	exit();
}
// Dump non-error files into a new array
$files = array();
for($i=0;$i<count($_FILES['file']['error']);$i++) {
	$file = $_FILES['file'];
	if ($file['error'][$i] > 0) {
		continue;
	}
	$files[] = array("name"=>$file["name"][$i], "tmp_name"=>$file["tmp_name"][$i], "type"=>$file["type"][$i], "error"=>$file["error"][$i]);
}
print_r($files);
// Include everything
include_once "../settings.php";
include_once "{$SETTINGS->BasePath}/lib/User.class.php";
include_once "{$SETTINGS->BasePath}/lib/Song.class.php";
include_once "{$SETTINGS->BasePath}/lib/MusicUploader.class.php";

// Init our objects
$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);
$user = new User(&$pdo, $_POST['username']);
$mu = new MusicUploader($SETTINGS->UploadPath, $SETTINGS->ExtractPath, null, $SETTINGS);

$error = false;
// Handle upload
if ($_POST['upload_type'] == "direct") {
	if (is_array($mu->ProcessSingles(&$pdo, $files, $user))) {
		$error = true;
	}
} else if ($_POST['upload_type'] == "zip") {
	if (is_arra($mu->ProcessZips(&$pdo, $files, $user))) {
		$error = true;
	}
}

if ($error) {
	echo "Upload error!";
}