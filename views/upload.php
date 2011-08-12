<?php

// Make sure we have what we need
if (count($_FILES) == 0 || !isset($_POST['upload_type']) || !isset($_POST['userid']})) {
	exit();
}

// Dump non-error files into a new array
$files = [];
foreach ($_FILES as $file) {
	if ($file['error'] > 0) {
		continue
	}
	$files[] = $file;
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