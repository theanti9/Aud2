<?php
session_start();
if (!isset($_POST['username']) || !isset($_POST['password'])) {
	echo "Missing Arguments";
	exit();
}

include_once '../settings.php';
include_once "{$SETTINGS->BasePath}/lib/User.class.php";

$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);
if (!$pdo) {
	echo json_encode(array("error"=>"Invalid database object"));
}
try {
	$user = new User(&$pdo, $_POST['username']);
	if ($user->ValidatePassword($_POST['password'])) {
		$_SESSION['userid'] = $user->getID();
		$_SESSION['username'] = $user->username;
		echo json_encode(array("error"=>NULL));
		exit();
	} else {
		echo json_encode(array("error"=>"bad password"));
		exit();
	}
} catch(Exception $e) {
	echo json_encode(array("error"=>$e->getMessage()));
}
?>