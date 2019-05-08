<?php

error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once("class/SmartCallRestHandler.php");



		$smartCallRestHandler = new SmartCallRestHandler();
		
		if(isset($_GET['method']) && $_GET['method']=="GetRegisteredContact" && isset($_GET['user_id']) && $_GET['user_id']!=""){
			$userObj =  new stdClass();
			$userObj->id=$_GET['user_id'];
		$output = $smartCallRestHandler->GetRegisteredContact($userObj);
		print_r($output);
		}elseif(isset($_GET['method']) && $_GET['method']=="addMultiContactsDemo" && isset($_GET['user_id']) && $_GET['user_id']!=""){
//Adding demo content to test
			$contacts['user_id']=$_GET['user_id'];
			$list[0]['name']='tosi';
			$list[0]['phone']='9827260117';
			$list[1]['name']='tanveer';
			$list[1]['phone']='9713429613';
			$contacts['contacts'] = array_values($list);
			$contacts=json_encode($contacts);
			
			$smartCallRestHandler->addMultiContacts($contacts);
		}else{
			echo 'Invalid Request Method is Missing';
			
		}
		
	
		
		
		
	
?>
