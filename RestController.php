<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once("class/SmartCallRestHandler.php");
require_once("class/SmartCallLogin.php");

$smartCallRestHandler = new SmartCallRestHandler();
$smartCallLogin = new SmartCallLogin();
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
	
	
$requestObj = json_decode(file_get_contents("php://input"));
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url /mobile/list/
		
		//$mobileRestHandler = new MobileRestHandler();
		//$mobileRestHandler->getAllMobiles();
		break;
		
	case "single":
		// to handle REST Url /mobile/show/<id>/
		$mobileRestHandler = new MobileRestHandler();
		$mobileRestHandler->getMobile($_GET["id"]);
		break;

	case "" :
		//404 - not found;
		break;
		
	case "GET_COUNTRY":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> getCountry();
	break;
	
	case "SIGNUP":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> signUp($requestObj);
	break; 
	
	case "CHECK_OTP":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> checkOtp($requestObj);
	break;
	
	case "UPDATE_ACCOUNT":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> updateAccount($requestObj);
	break;
	
	case "CREATE_SUPPORT":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> createSupport($requestObj);
	break;
	
	case "UPDATE_LAST_SEEN":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> updateLastSeen($requestObj);
	break;
	
/*	case "SIGNUP":
		$smartCallLogin = new SmartCallLogin();
		$smartCallLogin -> signUp($requestObj);
	break; */
	
	case "GET_PROFILE":
		$smartCallRestHandler = new smartCallRestHandler();
		$smartCallRestHandler -> getProfile($requestObj);
	break;
	
	case "SEND_NOTIFY":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> sendNotification($requestObj);
	break; 
	
	case "GET_MAXID":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> getMaxid($requestObj);
	break;
	
	case "GET_NOTIFYAFTER":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> getNotification($requestObj);
	break;

	case "SAVE_CONTACT":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> addContact($requestObj);
	break;

	case "FCM_UPDATE":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> updateFCM($requestObj);
	break;

	case "SEND_RAN_HEART":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> sendRandomHeart($requestObj);
	break;

	case "SEND_HEART":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> sendHeartNotification($requestObj);
	break;

	case "GET_REGISTERED_CONTACTS":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> GetRegisteredContact($requestObj);
	break;

	case "MULTIPLE_CONTACTS":
		$smartCallRestHandler = new SmartCallRestHandler();
		$smartCallRestHandler -> addMultiContacts($requestObj);
	break;

}
?>
