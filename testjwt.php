<?php

error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once("class/SmartCallRestHandler.php");



		$smartCallRestHandler = new SmartCallRestHandler();
		//$smartCallRestHandler -> jwtencryption();
	
?>
