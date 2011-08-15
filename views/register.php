<?php
session_start();

if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['confirm'])) {
	echo json_encode(array("error"=>"Please fill out the form completely!"));
	exit();
}

if ($_POST['password'] != $_POST['confirm']) {
	echo json_encode(array("error"=>"Passwords do not match!"));
	exit();
}

include_once "../settings.php";
include_once "{$SETTINGS->BasePath}/lib/User.class.php";

$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);

if (!$pdo) {
	echo json_encode(array("error"=>"Invalid database object"));
	exit();
}

$user = new User(&$pdo, $_POST['username'], $SETTINGS);
if (!$user->SetPassword($_POST['password'])) {
	echo json_encode(array("error"=>"Failed to update user object!"));
	exit();
}

$_SESSION['username'] = $user->username;
$_SESSION['userid'] = $user->getID();

echo json_encode(array("error"=>NULL));

?>