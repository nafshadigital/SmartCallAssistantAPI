<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once("class/SmartCallRestHandler.php");
require_once("class/SmartCallLogin.php");

		$smartCallRestHandler = new SmartCallRestHandler();
		if(isset($_GET['sender']) && isset($_GET['receiver']))
		$smartCallRestHandler -> sendHeartNotification($_GET['sender'],$_GET['receiver']);
		else
		echo 'Invalid Request';
?>
