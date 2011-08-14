<?php
session_start();
if (!isset($_POST['username']) || !isset($_POST['password'])) {
	echo "Missing Arguments";
	exit();
}

include_once '../settings.php';
include_once "{$SETTINGS->BasePath}/lib/User.class.php";

$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);

$user = new User(&$pdo, $_POST['username']);
if ($user->ValidatePassword($_POST['password'])) {
	$_SESSION['userid'] = $user->getID();
	$_SESSION['username'] = $user->username;
	echo "success";
	exit();
} else {
	echo "bad password";
	exit();
}
?>