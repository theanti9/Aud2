<?php
if (isset($_POST['action']) && isset($_POST['username'])) {
	if ($_POST['action'] != 'getLibrary') {
		exit();
	}
} else {
	exit();
}


include_once "../settings.php";
include_once $SETTINGS->BasePath . "/lib/User.class.php";

$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);

$user = new User(&$pdo, $_POST['username']);

echo $user->GetLibraryJson();

?>