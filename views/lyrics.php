<?php
	$title = str_replace(" ", "-", strtolower(str_replace(array("'", '"', ":", ";", ".", ",", "!", "@", "#", "$", "(", ")", "[", "]", "{", "}", "<", ">", "?", "/", "\\", "|", "+", "=", "*", "&", "^", "%"), "", $_POST["title"])));
	$artist = str_replace(" ", "-", strtolower(str_replace(array("'", '"', ":", ";", ".", ",", "!", "@", "#", "$", "(", ")", "[", "]", "{", "}", "<", ">", "?", "/", "\\", "|", "+", "=", "*", "&", "^", "%"), "", $_POST["artist"])));

	$url = "http://www.lyrics.com/" . $title . "-lyrics-" . $artist . ".html";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);       
	curl_close($ch);
	$re = '#\<div id="lyric_space"\>(.+?)\<\/div\>#s';
	preg_match_all($re, $response, $matches, PREG_PATTERN_ORDER);
	
	$lyrics = $matches[0][0];
	echo $lyrics;
?>