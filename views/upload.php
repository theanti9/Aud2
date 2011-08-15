<?php
session_start();
// Make sure we have what we need

$file_name = isset($_REQUEST['file']) ? basename(stripslashes($_REQUEST['file'])) : null; 
if ($file_name) {
	echo json_encode(array(array("name"=>NULL, "tmp_name"=>NULL, "type"=>NULL, "error"=>NULL)));
	exit();
}

if (count($_FILES) == 0 || !isset($_SESSION['username'])) {
	//print_r($_FILES);
	//echo "Missing arguments";
	exit();
}

// Dump non-error files into a new array
$files = array();
for($i=0;$i<count($_FILES['files']['error']);$i++) {
	$file = $_FILES['files'];
	if ($file['error'][$i] > 0) {
		continue;
	}
	$files[] = array("name"=>$file["name"][$i], "tmp_name"=>$file["tmp_name"][$i], "type"=>$file["type"][$i], "error"=>$file["error"][$i], "size"=>$file["size"][$i]);
}
// Include everything
include_once "../settings.php";
include_once "{$SETTINGS->BasePath}/lib/User.class.php";
include_once "{$SETTINGS->BasePath}/lib/Song.class.php";
include_once "{$SETTINGS->BasePath}/lib/MusicUploader.class.php";

// Init our objects
$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);
$user = new User(&$pdo, $_SESSION['username'], $SETTINGS);
$mu = new MusicUploader($SETTINGS->UploadPath, $SETTINGS->ExtractPath, null, $SETTINGS);

$error = false;
// Handle upload
//if ($_POST['upload_type'] == "direct") {
$ret = $mu->ProcessSingles(&$pdo, $files, $user);
header('Content-type: application/json');
echo json_encode($ret);
exit();
//} 
/* else if ($_POST['upload_type'] == "zip") {
	if (is_array($mu->ProcessZips(&$pdo, $files, $user))) {
		$error = true;
	}
}
*/

if ($error) {
	echo "Upload error!";
}