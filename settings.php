<?php

$audBasePath = "/Users/wil/git/Aud2/";
//$audBasePath = "/Users/ryan/Dropbox/github/Aud2";

include_once "{$audBasePath}/lib/Settings.class.php";

$SETTINGS = new Settings("Aud2");

$SETTINGS->BasePath = $audBasePath;
$SETTINGS->DbHost = "localhost";
$SETTINGS->DbUser = "root";
$SETTINGS->DbPass = "root";
$SETTINGS->DbName = "aud2";
$SETTINGS->UploadPath = $SETTINGS->BasePath . "/upload/";
$SETTINGS->ExtractPath = $SETTINGS->BasePath . "/extract/";



?>