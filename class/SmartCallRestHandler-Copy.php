<?php
require_once("SimpleRest.php");
require_once("Mobile.php");

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__)  . $ds . '..') . $ds;
require_once("{$base_dir}conf{$ds}dbSettings.php");

include_once 'MySQL5.php';
include_once 'MailClient.php';
class SmartCallRestHandler extends SimpleRest 
{
	function getCountry()
	{
		$arr = json_decode(file_get_contents("CountryCodes.json"));
		$this -> output($arr);
	}
		
	function signUp($signUpObj)
	{
		$development = 0;
		if(!$signUpObj)
		{
			
			if($development == 1)
			{
				echo "Incorrect Parameter ---> Signup";
				return;
			}

			echo "Setting Parameter";
			$signUpObj = new stdClass();
			$signUpObj -> mobile = "9894695843";
			$signUpObj -> country_code = "+91";
		}

		
		$m = new MySQL5();
		$resultSignUpObj = new stdClass();
		$resultSignUpObj -> status = 0;
		
		$query = "select id from tbl_users where mobile = '{$signUpObj -> mobile}' and country_code = '{$signUpObj -> country_code}'";
		$row = $m -> executeQuery($query,'select');


		if(count($row) > 0)
		{
			$resultSignUpObj -> status = "1";
			$resultSignUpObj -> user_id = $row[0]['id'];
			$resultSignUpObj -> message = "Existing User";
		}
		else
		{
			$query = "insert into tbl_users (country_code, mobile, created_date) values ('{$signUpObj -> country_code}', '{$signUpObj -> mobile}', '".date('Y-m-d')."')";

			$inserted_id = $m -> executeQuery($query,'insert');
			echo "Row ID".json_encode($inserted_id);
			
			$resultSignUpObj -> status = "1";
			$resultSignUpObj -> user_id = $inserted_id;
			$resultSignUpObj -> message = "Sign Up Success";
		}
		
		$query = "select count(id) from tbl_verification_code where user_id = '{$resultSignUpObj -> user_id}' ";
		$isExists = $m -> executeScalar($query);
		
		$otpQuery = "select FLOOR(RAND() * 8997.00+ 100000)";
		$otp = $m -> executeScalar($otpQuery);
		$resultSignUpObj -> otp = $otp;
		if($isExists == 0)
		{
			$query = "insert into tbl_verification_code ( 	user_id, verification_code, created_date	 ) values ( 	'{$resultSignUpObj -> user_id}', '$otp', '".date('Y-m-d H:i:s')."'	 )";
			$m -> executeQuery($query,'insert');
		}
		else
		{
			$query = "update tbl_verification_code set verification_code = '$otp', created_date = '".date('Y-m-d H:i:s')."', status = '1' where user_id = '{$resultSignUpObj -> user_id}' ";
			$m -> executeQuery($query,'update');
		}
		
		$myJSON = json_encode($resultSignUpObj);
		echo $myJSON;

		//$this -> output($resultSignUpObj);

	}
	
	function checkOtp($checkOtpObj)
	{
		if(!$checkOtpObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL5();
		$resultCheckOtpObj = new stdClass();
		
		$query = "select count(id) from tbl_verification_code where user_id = '{$checkOtpObj -> user_id}' and verification_code = '{$checkOtpObj -> verification_code}' ";
		$isExists = $m -> executeScalar($query);
		
		if($isExists != 0)
		{
			$query = "update tbl_verification_code set status = '0' where user_id = '{$checkOtpObj -> user_id}' ";
			$m -> executeQuery($query,'update');
		
			$resultCheckOtpObj -> status = "1";
			$resultCheckOtpObj -> message = "Verification Code is Success..";
		}
		else
		{
			$resultCheckOtpObj -> status = "0";
			$resultCheckOtpObj -> message = "Verification Code is Wrong..";
		}
		
		//$this -> output($resultCheckOtpObj);
		$myJSON = json_encode($resultCheckOtpObj);
		echo $myJSON;

	}
	
	function updateAccount($updateAccObj)
	{
		if(!$updateAccObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL5();
		$resultUpdateAccObj = new stdClass();

		$query = "select count(name) from tbl_users where email = '{$updateAccObj -> email}' and id <> '{$updateAccObj -> id}'";
		$isExists = $m -> executeScalar($query);


		if($isExists == 0)
		{
			$query = "update tbl_users set name = '{$updateAccObj -> name}', device_id = '{$updateAccObj -> device_id}', android_id = '{$updateAccObj -> android_id}', email = '{$updateAccObj -> email}' where id = '{$updateAccObj -> id}' ";
			$m -> executeQuery($query,'update');

		
			$resultUpdateAccObj -> message = "Update Account Success..";
			$resultUpdateAccObj -> error = "0";
		}
		else
		{
			$resultUpdateAccObj -> error = "1";
			$resultUpdateAccObj -> message = "Email Address already exist !";
		}

		$this -> output($resultUpdateAccObj);
	}
	
	function addContact($userContactsObj)
	{
		$development = 0;

		if(!$userContactsObj)
		{
			
			if($development == 1)
			{
				echo "Incorrect Parameter";
				return;
			}

			echo "Setting Parameter";
			$userContactsObj = new stdClass();
			$userContactsObj -> phone = "919443023965";
			$userContactsObj -> user_id = "210";
			$userContactsObj -> name = "Daddy";

		}
		
		$m = new MySQL5();
		$resultSupportObj = new stdClass();

		$query = "select concat(country_code,mobile) from tbl_users where concat(country_code,mobile) = '+{$userContactsObj -> phone}' and length(fcm_token) > 10";
		$phone_number = $m -> executeScalar($query);

		echo "Registered = ". $phone_number. " Query = ".$query;

		$registered = 0;
		if(strlen($phone_number) > 8)
		{
			$registered = 1;
		}

		$query = "select count(id) from tbl_user_contacts where user_id='{$userContactsObj -> user_id}' and contact_number ='{$userContactsObj -> phone}'";
		$isAlreadyExist = $m -> executeScalar($query);

		echo $query;
		echo "Already Exist".$isAlreadyExist;

		if($isAlreadyExist == 0)
		{
			echo "Already";
		}
		else
		{
			echo "Not Reached";
		}

		if($isAlreadyExist == 0)
		{

			$query = "insert into tbl_user_contacts (user_id, contact_name,contact_number,isRegistered) values ('{$userContactsObj -> user_id}', '{$userContactsObj -> name}', '{$userContactsObj -> phone}', ".((int) $registered).")";

			$inserted_id = $m -> executeQuery($query,'insert');
			$id = $inserted_id;

			echo $query;
			echo $inserted_id;

			if(strlen($phone_number) > 8)
			{
				$resultSupportObj -> PartnerTrueByPhone = $registered;	
			}
			else
			{
				$resultSupportObj -> NotAMember = $registered;	
			}
			$resultSupportObj -> message = $id;
		}

		$resultSupportObj -> PartnerTrueByPhone = $registered;	
		$resultSupportObj -> message = "Already Exist as ".$isAlreadyExist;

		$this -> output($resultSupportObj);

		//$myJSON = json_encode($resultSupportObj);
		//echo $myJSON;
	}

	function createSupport($createSupportObj)
	{
		if(!$createSupportObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL5();
		$resultSupportObj = new stdClass();
		
		$query = "insert into tbl_support ( 	user_id, message, msg_date	 ) values ( 	'{$createSupportObj -> user_id}', '{$createSupportObj -> message}', '".date('Y-m-d H:i:s')."'	 )";
		$inserted_id = $m -> executeQuery($query,'insert');
		
		$id = $inserted_id;
		
		$this -> sendCreateSupportEmail($id);
		
		$resultSupportObj -> message = "Support Create Success and send email for admin..";
		
		$this -> output($resultSupportObj);
	}
	
	function getAdminEmailIds()
	{
		$m = new MySQL5();
		$query = "select admin_email_ids from tbl_admin_email_ids where 1 ";
		return $m -> executeScalar($query);
	}
	
	function sendCreateSupportEmail($id)
	{

		$m = new MySQL5();
		$mc = new MailClient();
		
		$query = "select t1.*, t2.name, mobile from tbl_support t1 left outer join tbl_users t2 on t1.user_id = t2.id where t1.id = '$id' and t1.status = '0' ";
		$row = $m -> executeQuery($query);
		
		$adminEmailIds = $this -> getAdminEmailIds();
		$toArray = explode(",", $adminEmailIds);
		
		foreach($toArray as $to)
		{
			$message = "Hi Admin, <br><br>Your have received an new support details<br><br>";
				$message .= "<table cellpadding='8' style='border: 1px solid #00FFFF;'>";
					$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Date:</th><td>{$row -> msg_date}</td></tr>";
					$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Name:</th><td>{$row -> name}</td></tr>";
					$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Mobile:</th><td>{$row -> mobile}</td></tr>";
					$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Message:</th><td>{$row -> message}</td></tr>";
			$message .= "</table>";
			
			$mc -> sendEmail($to, "New Support Details on - ". date('Y-m-d'), $message);
		}
		
		return "SUCCESS..";
	}
	
	function updateLastSeen($updateLastObj)
	{
		if(!$updateLastObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL5();
		$resultLastseenObj = new stdClass();
		
		$query = "update tbl_users set last_seen = '".date("Y-m-d H:i:s")."' where id = '{$updateLastObj -> user_id}' ";
		$m -> executeQuery($query,'update');
		
		$resultLastseenObj -> message = "Last Seen Update Success..".date("Y-m-d H:i:s");
		
		$this -> output($resultLastseenObj);
	}
	
	function getProfile($profileObj){

		$development = 0;

		if(!$profileObj)
		{
			
			if($development == 1)
			{
				echo "Incorrect Parameter";
				return;
			}

			echo "Setting Parameter";
			$profileObj = new stdClass();
			$profileObj -> id = 178;
			$profileObj -> android_id = 178;
			$profileObj -> device_id = 178;

		}
		$m = new MySQL5();
		$resultSignIn = new stdClass();
		
		$user = new stdClass();
		$user -> user_exists = 0;
		$user -> message = "user does not exists.";
		$message = "User does not exists";
		
		if(isset($profileObj -> android_id) && isset($profileObj -> device_id)) {
				$query = "select * from tbl_users where id = '{$profileObj -> id}' ";
				$row = $m -> executeQuery($query,'select');
					$user -> user_record = $row;
					$user -> user_exists = 1;
					$user -> message		= "User exists";
				
			}
			else
			{
				$user -> message = "User exists but problem with android and device id missing !";
			}

		$myJSON = json_encode($user);
		echo $myJSON;
}


function sendNotification($sendNotiObj){
		if(!$sendNotiObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL5();
		$resultNotiObj = new stdClass();
		$resultNotiObj -> status = 0;
		$resultNotiObj -> message = "There is no user with Phone Number : ".$sendNotiObj -> phone_number;		
		$query = "select id from tbl_users where concat(country_code,mobile) = '{$sendNotiObj -> phone_number}' ";
		$to_user_id = $m -> executeScalar($query);
		
		
		if($to_user_id){
			$query = "insert into tbl_notifications(user_id,to_user_id,message,created_date) values ('{$sendNotiObj -> user_id}','$to_user_id','{$sendNotiObj -> message}','".date('Y-m-d H:i:s')."')";
			$m -> executeQuery($query,'insert');
			
			$resultNotiObj -> status = "1";
			$resultNotiObj -> message = "Success";
		}
		
		$this -> output($resultNotiObj);
	}
	
function getMaxid($userObj){
	$m = new MySQL5();
	$query = "select max(id) from tbl_notifications where to_user_id = '{$userObj -> user_id}'";
	$maxId = $m -> executeScalar($query);
	$resObj = new stdClass();
	$resObj -> maxId = $maxId;
	$this -> output($resObj);
}

function getNotification($getNotiObj){
	$m = new MySQL5();

	//SELECT n.id,n.created_date,n.message,n.to_user_id,n.user_id,u.name FROM tbl_notifications n ,tbl_users u WHERE n.id > 49 AND n.to_user_id = 193 AND u.id = n.to_user_id ORDER BY n.id DESC LIMIT 0,5
	

	$query = "select * from tbl_notifications where id > '{$getNotiObj -> id}' and to_user_id = '{$getNotiObj -> user_id}' order by id desc limit 0,5";
	$query = "	SELECT n.id,n.created_date,n.message,n.to_user_id,n.user_id,u.name FROM tbl_notifications n ,tbl_users u WHERE n.id > '{$getNotiObj -> id}' and n.to_user_id = '{$getNotiObj -> user_id}' AND u.id = n.to_user_id ORDER BY n.id DESC LIMIT 0,5";

	//echo $query;
	$arr =  $m -> executeQuery($query,'select');
	
	$objRes = new stdClass();
	$objRes -> status = "0";
	$objRes -> message = "Notifications";
	$objRes -> notifications = $arr;
	
	if(count($arr) > 0){
		$objRes -> status = "1";
		$objRes -> message = "";
	}
	
	$this -> output($objRes);
}

function updateFCM($updateAccObj)
{
	if(!$updateAccObj)
	{
		echo "Incorrect Parameter";
		return;
	}
	
	$m = new MySQL5();
	$resultUpdateAccObj = new stdClass();

	$query = "update tbl_users set fcm_token = '{$updateAccObj -> fcmtoken}' where id = '{$updateAccObj -> id}' ";
	$m -> executeQuery($query,'update');

	$resultUpdateAccObj -> message = "FCM Updated !";
	$resultUpdateAccObj -> error = "0";

	$this -> output($resultUpdateAccObj);
}

function sendRandomHeart($userObj){
	$m = new MySQL5();
	// Get only one Random record
	$query = "select concat(country_code, mobile) from tbl_users where id <> '{$userObj -> id}' AND LENGTH(fcm_token) > 10 ORDER BY RAND() LIMIT 1"; 
	$receiver_phone = $m -> executeScalar($query);
	$query = "insert into tbl_hearts ( 	sender_id,receiver_phone, send_date) values ('{$userObj -> id}', '{$receiver_phone}', curdate())";
	$inserted_id = $m -> executeQuery($query,'insert');
	$resObj = new stdClass();
	$resObj -> receiver_phone = $receiver_phone;
	$resObj -> status	= "1";
	$resObj -> heart_id = $inserted_id;
	$resObj -> message  = "Send Heart Successful !";
	$this -> output($resObj);
}

function sendHeart($userObj)
{
	$m = new MySQL5();
	$query = "insert into tbl_hearts ( 	sender_id,receiver_phone, send_date) values ('{$userObj -> id}', '{$userObj -> receiver_phone}', curdate())";
	$inserted_id = $m -> executeQuery($query,'insert');
	$resultSignUpObj = new stdClass();
	$resultSignUpObj -> receiver_phone	 = $userObj -> receiver_phone;
	$resultSignUpObj -> status	 = 1;
	$resultSignUpObj -> heart_id = $inserted_id;
	$resultSignUpObj -> message  = "Send Heart Successful !";
	$this -> output($resultSignUpObj);
}

function sendHeartNotification($senderid,$receiverid)
{
	$m = new MySQL5();
	$title = "Title ";
	$body= "Body ";
	$url = "https://fcm.googleapis.com/fcm/send";
		$resultObj = new stdClass();
		$resultObj -> status = 0;
	//getting fcm tokens of receiver
		$query = "select fcm_token from tbl_users where id='$receiverid' ";
		$receiver_fcm = $m -> executeScalar($query);
	//updating heart of receiver	
		$query = "update tbl_users set heart = heart + 1 where id='$receiverid'";
		$m -> executeQuery($query,'update');
	
	//sending notification

	$ch	= curl_init($url);
	$header = array();
	$header[] = 'Content-type: application/json';
	$authkey="AAAAiMVyMTg:APA91bHR8sFvG509lmNgcvaqdM6G9yTf0IdFtuxlvqY1oBnAqbqtjoOHn2j5Zd8lvTaxap8Gs8j9Zpl3GwAB-qMZ7xmG2X8E988DV8WcG-Kk7FF0G-tu5ksPZwP172MuN_LO6m6PpA5g";
	$header[] = 'Authorization: key=' . $authkey;

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

	$data = [
	  'to' => $receiver_fcm,
	  'notification' => [
		'title' => $title,
		'body' => $body
	  ]
	];

	curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch, CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$res = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);
	if ($err) 
	{
		$resultObj -> status = 0;
		$resultObj -> response = $error;
	}else{
		$resultObj -> status = 1;
    	$resultObj -> response  = $res;
		
	} 
	//entering notification data
	$query = "insert into tbl_messages (sender,receiver, datetime) values ('$senderid', '$receiverid', curdate())";
	//echo $query;
	$inserted_id = $m -> executeQuery($query,'insert');
	$resultObj -> message  = "Send Heart Successful !";
	$this -> output($resultObj);
}



}
?>