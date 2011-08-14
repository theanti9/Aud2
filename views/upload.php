<?php

// Make sure we have what we need
if (count($_FILES) == 0 || !isset($_POST['upload_type']) || !isset($_POST['userid'])) {
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
// Include everything
include_once "../settings.php";
include_once "{$audBasePath}/lib/User.class.php";
include_once "{$audBasePath}/lib/Song.class.php";
include_once "{$audBasePath}/lib/MusicUploader.class.php";

// Init our objects
$pdo = new PDO("mysql:host={$audDbHost};dbname={$audDbName}", $audDbUser, $audDbPass);
$user = new User(&$pdo, $_POST['userid']);
$mu = new MusicUploader($audUploadPath, $audExtractPath, null);

$error = false;
// Handle upload
if ($_POST['upload_type'] == "direct") {
	if (!$mu->ProcessSingles(&$pdo, $files, $user)) {
		$error = true;
	}
} else if ($_POST['upload_type'] == "zip") {
	if (!$mu->ProcessZips(&$pdo, $files, $user)) {
		$error = true;
	}
}

if ($error) {
	echo "Upload error!";
}