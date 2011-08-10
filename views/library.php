<?php

if (isset($_POST['action']) && isset($_POST['userid'])) {
	if ($_POST['action'] != 'getLibrary') {
		exit();
	}
} else {
	exit();
}


include_once "../settings.php";
include_once $audBasePath . "lib/User.class.php";

$pdo = new PDO("mysql:host={$audDbHost};dbname={$audDbName}", $audDbUser, $audDbPass);

$user = new User($pdo, $_POST['userid']);

echo $user->GetLibraryJson();
exit();

?>