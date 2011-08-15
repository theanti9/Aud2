<?php
session_start();
if (isset($_POST['action']) && isset($_SESSION['username'])) {
	if ($_POST['action'] != 'getLibrary') {
		exit();
	}
} else {
	exit();
}


include_once "../settings.php";
include_once $SETTINGS->BasePath . "/lib/User.class.php";

$pdo = new PDO("mysql:host={$SETTINGS->DbHost};dbname={$SETTINGS->DbName}", $SETTINGS->DbUser, $SETTINGS->DbPass);

if (!$pdo) {
	echo json_encode(array("error"=>"Invalid database object"));
}
try {
	$user = new User(&$pdo, $_SESSION['username'], $SETTINGS);

	$library = $user->GetLibraryJson();
	if (gettype($library) == "object") {
		if (get_class($library) == "PDOException") {
			echo json_encode(array("error"=>$library->getMessage()));
			exit();
		} else {
			echo json_encode(array("error"=>"Expecting JSON, got '".get_class($library)."'"));
			exit();
		}
	}
} catch (Exception $e) {
	echo json_encode(array("error"=>$e->getMessage()));
	exit();
}
echo $library;

?>