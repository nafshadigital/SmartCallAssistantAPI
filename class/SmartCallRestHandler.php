<?php
require_once("SimpleRest.php");
require_once("Mobile.php");

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__)  . $ds . '..') . $ds;
require_once("{$base_dir}conf{$ds}dbSettings.php");

include_once 'MySQL5.php';
include_once 'Util.php';
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

		$resObj = new stdClass();

	
		$util  = new Util();

		if(!$signUpObj)
		{
			$resObj = $util -> missingParam();	
			$this -> output($resObj);			
			return;
		}
		else
		{
			if(!property_exists($signUpObj,'mobile'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
			}
			if(!property_exists($signUpObj,'country_code'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
			}			
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

		$otp = "100786";
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

		$resObj = new stdClass();	

		$util  = new Util();
		$resObj = $util -> missingParam();	

		if(!$checkOtpObj)
		{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
		}
		else
		{
			if(!property_exists($checkOtpObj,'user_id'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($checkOtpObj,'verification_code'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
			}			
		}

	 	$userid = $checkOtpObj -> user_id;
		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();

			$this -> output($resObj);
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

		$resObj = new stdClass();				

		$util  = new Util();
		$resObj = $util -> missingParam();	

		$validateObject = $updateAccObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'id'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'email'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
			}			
			if(!property_exists($validateObject,'name'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
			}			
			if(!property_exists($validateObject,'android_id'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;	

			}
			if(!property_exists($validateObject,'device_id'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;	
			}
		}

		$updateAccObj = $validateObject;
	

	 	$userid = $updateAccObj -> id;
		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();

			$this -> output($resObj);
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


		$resObj = new stdClass();				
		$util  = new Util();
		$resObj = $util -> missingParam();	

		$validateObject = $userContactsObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'user_id'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'phone'))
			{
				$resObj = $util -> missingParam();	
				$this -> output($resObj);	
				return;		
			}			
		}


	 	$userid = $userContactsObj -> user_id;
		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();

			$this -> output($resObj);
			return;
		}				
		
		$m = new MySQL5();
		$resultSupportObj = new stdClass();

		$query = "select concat(country_code,mobile) from tbl_users where concat(country_code,mobile) = '+{$userContactsObj -> phone}' and length(fcm_token) > 10";
		$phone_number = $m -> executeScalar($query);


		$registered = 0;
		if(strlen($phone_number) > 8)
		{
			$registered = 1;
		}

		$query = "select count(id) from tbl_user_contacts where user_id='{$userContactsObj -> user_id}' and contact_number ='{$userContactsObj -> phone}'";
		$isAlreadyExist = $m -> executeScalar($query);


		if($isAlreadyExist == 0)
		{

			$query = "insert into tbl_user_contacts (user_id, contact_name,contact_number,isRegistered) values ('{$userContactsObj -> user_id}', '{$userContactsObj -> name}', '{$userContactsObj -> phone}', ".((int) $registered).")";

			$inserted_id = $m -> executeQuery($query,'insert');
			$id = $inserted_id;

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
		$resultSupportObj -> message = "Already Exist as ";

		return $resultSupportObj;

	}

	function createSupport($createSupportObj)
	{
		$resObj = new stdClass();				

		$util  = new Util();
		$resObj = $util -> missingParam();			

		$validateObject = $createSupportObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'user_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'message'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;		
			}			
		}

	 	$userid = $createSupportObj -> user_id;

		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();
			$this -> output($resObj);
			return;
		}		
		
		$m = new MySQL5();
		$resultSupportObj = new stdClass();
		
		$query = "insert into tbl_support ( 	user_id, message, msg_date	 ) values ( 	'{$createSupportObj -> user_id}', '{$createSupportObj -> message}', '".date('Y-m-d H:i:s')."'	 )";
		$inserted_id = $m -> executeQuery($query,'insert');
		
		$id = $inserted_id;
		
		if($this -> sendCreateSupportEmail($id))
		{
			$resultSupportObj -> message = "Support Create Success and send email for admin..";
			$this -> output($resultSupportObj);
		}
		else
		{
			$resultSupportObj -> message = "Not able to send the message";
			$this -> output($resultSupportObj);
		}
		
	}
	
	function getAdminEmailIds()
	{
		

		$m = new MySQL5();
		$query = "select admin_email_ids from tbl_admin_email_ids where 1 ";

		$admin_email = $m -> executeQuery($query,'select');
		$admin['status']=1;
		$admin['emailaddress']=$admin_email;
		$this -> output($admin);
 	}
	
	function sendCreateSupportEmail($id)
	{

		$m = new MySQL5();
		$mc = new MailClient();
		$util  = new Util();
		
		$query = "select t1.*, t2.name, mobile from tbl_support t1 left outer join tbl_users t2 on t1.user_id = t2.id where t1.id = '$id' and t1.status = '0' ";
		$row = $m -> executeQuery($query);
		
		$adminEmailIds = $this -> getAdminEmailIds();
		$toArray = explode(",", $adminEmailIds);

		if(!$row)
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();
			$this -> output($resObj);
			return false;
	

			if(property_exists($row,'msg_date'))		
			{
				$msg_date 	= $row -> msg_date;
				$name 		= $row -> name;
				$mobile 	= $row -> mobile;
				$message    = $row -> message;

				foreach($toArray as $to)
				{
					$message = "Hi Admin, <br><br>Your have received an new support details<br><br>";
						$message .= "<table cellpadding='8' style='border: 1px solid #00FFFF;'>";
						$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Date:</th><td>{$msg_date}</td></tr>";
						$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Name:</th><td>{$name}</td></tr>";
						$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Mobile:</th><td>{$mobile}</td></tr>";
						$message .= "<tr><th align='left' style='background-color: #00FFFF;'>Message:</th><td>{$message}</td></tr>";
					$message .= "</table>";
			
					$mc -> sendEmail($to, "New Support Details on - ". date('Y-m-d'), $message);
				}		
				return true;
			}
		}	
		return false;
	}
	
	function updateLastSeen($updateLastObj)
	{


		$resObj = new stdClass();				
		$util  = new Util();		

		$validateObject = $updateLastObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'user_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
		}

	 	$userid = $updateLastObj -> user_id;
		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();

			$this -> output($resObj);
			return;
		}		
		
		$m = new MySQL5();
		$resultLastseenObj = new stdClass();
		
		$query = "update tbl_users set last_seen = '".date("Y-m-d H:i:s")."' where id = '{$updateLastObj -> user_id}' ";
		$m -> executeQuery($query,'update');
		
		$resultLastseenObj -> result = 1;		
		$resultLastseenObj -> message = "Last Seen ".date("Y-m-d H:i:s");
		
		$this -> output($resultLastseenObj);
	}
	
	function getProfile($profileObj){



		$resObj = new stdClass();				
		$util  = new Util();

		$validateObject = $profileObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'device_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}	
			if(!property_exists($validateObject,'android_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}						
		}

	 	$userid = $profileObj -> id;
		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();

			$this -> output($resObj);
			return;
		}
 

		$m = new MySQL5();
		$resultSignIn = new stdClass();
		
		$user = new stdClass();
		$user -> user_exists = 0;
		$user -> message = "user does not exists.";
		$message = "User does not exists";
		
		if(isset($profileObj -> android_id) && isset($profileObj -> device_id)) {
				$query = "select * from tbl_users where id = '{$profileObj -> id}' and android_id='{$profileObj -> android_id}' and device_id= '{$profileObj -> device_id}' ";

				$row = $m -> executeQuery($query,'select');

				$user -> user_record = $row;
				$user -> user_exists = 1;
				$user -> message		= "User exists";

				if(empty($row))
				{
					$user -> user_record = $row;
					$user -> user_exists = 0;
					$user -> message = "User exists but problem with android and device id missing !";
				}				

			}
			else
			{
				$user -> message = "User exists but problem with android and device id missing !";
			}

		$myJSON = json_encode($user);
		echo $myJSON;
}


function sendNotification($sendNotiObj){


		$resObj = new stdClass();				
		$util  = new Util();

		$validateObject = $sendNotiObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'phone_number'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'user_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}	
			if(!property_exists($validateObject,'message'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}						
		}
	 	$userid = $sendNotiObj -> user_id;
		if(!$this->isUserExist($userid))
		{
			$resObj = new stdClass();
			$resObj = $util -> inValidUser();

			$this -> output($resObj);
			return;
		}


	 
		
		$m = new MySQL5();
		$resultNotiObj = new stdClass();
		$resultNotiObj -> status = 0;
		$resultNotiObj -> message = "There is no user with Phone Number : ".$sendNotiObj -> phone_number;		
		$query = "select id from tbl_users where concat(country_code,mobile) = '{$sendNotiObj -> phone_number}' ";
		$to_user_id = $m -> executeScalar($query);

		
		if($to_user_id && $to_user_id != $userid){
			$query = "insert into tbl_notifications(user_id,to_user_id,message,created_date) values ('{$sendNotiObj -> user_id}','$to_user_id','{$sendNotiObj -> message}','".date('Y-m-d H:i:s')."')";
			$m -> executeQuery($query,'insert');
			
			$resultNotiObj -> status = "1";
			$resultNotiObj -> message = "Success";
		}
		if($to_user_id == $userid)
		{
			$resultNotiObj -> message = "ha ha You are trying to send message to yourself !";		
		}
		
		$this -> output($resultNotiObj);
	}
	
function getMaxid($userObj){



	$resObj = new stdClass();				
	$util  = new Util();	

	$validateObject = $userObj;
	if(!$validateObject)
	{
			$resObj = $util -> missingParam();			
			$this -> output($resObj);	
			return;	
	}
	else
	{
		if(!property_exists($validateObject,'user_id'))
		{
			$resObj = $util -> missingParam();			
			$this -> output($resObj);	
			return;				
		}
	}

 	$userid = $userObj -> user_id;
	if(!$this->isUserExist($userid))
	{
		$resObj = new stdClass();
		$resObj = $util -> inValidUser();

		$this -> output($resObj);
		return;
	}	
	else
	{
		$m = new MySQL5();
		$query = "select max(id) from tbl_notifications where to_user_id = '{$userObj -> user_id}'";
		$maxId = $m -> executeScalar($query);
		$resObj = new stdClass();
		$resObj -> maxId = $maxId;
		if(empty($maxId))
		{
		$resObj -> maxId = 0;	
		}
		
		$this -> output($resObj);

	}
}

function getNotification($getNotiObj){
	$m = new MySQL5();

	

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
		$resObj = new stdClass();				
		$util  = new Util();

		$validateObject = $updateAccObj;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'fcmtoken'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}	
				
		}



 	$userid = $validateObject -> id;
	if(!$this->isUserExist($userid))
	{
		$resObj = new stdClass();
		$resObj = $util -> inValidUser();

		$this -> output($resObj);
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

//Toseef's Code 
function sendHeartNotification($sendHeartDetails)
{

		$resObj = new stdClass();				
		$util  = new Util();

		$validateObject = $sendHeartDetails;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'sender_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'receiver_phone'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}	
				
		}



 	$userid = $sendHeartDetails -> sender_id;
	if(!$this->isUserExist($userid))
	{
		$resObj = new stdClass();
		$resObj = $util -> inValidUser();

		$this -> output($resObj);
		return;
	}

	$senderid	= $sendHeartDetails -> sender_id;
	$receiver_phone = $sendHeartDetails -> receiver_phone;

	$m = new MySQL5();


	$query = "select name from tbl_users where id=$senderid";
	$sender_name = $m -> executeScalar($query);

	$query = "select concat(country_code,mobile) as mobile from tbl_users where id=$senderid";
	$sender_phone = $m -> executeScalar($query);


	$query = "select id,name from tbl_users where concat(country_code,mobile)='$receiver_phone'";
	$receiver = $m -> executeQuery($query);

	$receiverid  	= $receiver["id"];
	$receivername  = $receiver["name"];


	$userContactsObj = new stdClass();
	$userContactsObj -> phone = $sender_phone;
	$userContactsObj -> name = $sender_name;
	$userContactsObj -> user_id = $receiverid;
	$ignore = $this->addContact($userContactsObj);

	$resultObj = new stdClass();
	$resultObj -> status = 0;
	if($senderid == $receiverid)
	{
		$resultObj -> message = "You cannot send heart to yourself !";
		$this -> output($resultObj);
		return;
	}


	$url = "https://fcm.googleapis.com/fcm/send";


	$resultObj -> status = 0;


	if(strlen($sender_name) < 2)
	{
		$query = "select concat(country_code,mobile) from tbl_users where id='$senderid'";
		$sender_name = $m -> executeScalar($query);
		if(strlen($sender_name) < 2)
		{
		$resultObj -> status   = 0;
		$resultObj -> message  = "Wrong sender !";
		$this -> output($resultObj);
		return;
		}

	}

	$title = "You received Heart";
	$body= $sender_name." sent you heart";

	echo $sender_name;

	//getting fcm tokens of receiver
	$query = "select fcm_token from tbl_users where id='$receiverid' ";
	$receiver_fcm = $m -> executeScalar($query);

	if(strlen($receiver_fcm) < 10)
	{
		$resultObj -> message = "User not registered with Smart Call Assistant !".$receiverid;
		$this -> output($resultObj);
		return;
	}
	
	//updating heart of receiver	
	$query = "update tbl_users set heart = heart + 1 where id='$receiverid'";
	$m -> executeQuery($query,'update');
	
	//sending notification

	$ch	= curl_init($url);
	$header = array();
	$header[] = 'Content-type: application/json';
	$authkey="AAAAiMVyMTg:APA91bF-06ZYTiaOcS_pKQljWFZ3mZUOl9QXTCsDxUKmmqouay86LofGf85DiE6cSutcYw7WR8fR0LzPOUiXDTgwjg8Y7bAp-2fFp1lrZGxDMGG_nH3a_5lMe0YaNMi4v-4SZil4teNY";
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

		$res = json_decode($res, true);


		$resultObj -> status   = 1;
		$resultObj -> success  = $res['success'];
    	$resultObj -> failure  = $res['failure'];
		$resultObj -> multicast_id = $res['multicast_id'];
		$resultObj -> message  = "Heart from ".$sender_name." to ".$receivername." Successfully ! ";
		
	} 
	//entering notification data
	$query = "insert into tbl_messages (sender,receiver, datetime) values ('$senderid', '$receiverid', curdate())";
	//echo $query;
	$inserted_id = $m -> executeQuery($query,'insert');

	$this -> output($resultObj);
}

	function addMultiContacts($contacts)
	{


		$resObj = new stdClass();				
		$util  = new Util();

		$validateObject = $contacts;
		if(!$validateObject)
		{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;	
		}
		else
		{
			if(!property_exists($validateObject,'user_id'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'contact_size'))
			{
				$resObj = $util -> missingParam();			
				$this -> output($resObj);	
				return;				
			}	
				
		}


 	$userid = $validateObject -> user_id;
	if(!$this->isUserExist($userid))
	{
		$resObj = new stdClass();
		$resObj = $util -> inValidUser();

		$this -> output($resObj);
		return;
	}


		$contacts = $validateObject;

		$contactObject = new stdClass();
		$contactObject = $contacts;
		
   	    if(isset( $contacts -> user_id ))		
		{
			$user_id = $contacts -> user_id;
		}

	    if(isset( $contacts -> contact_size ))
		{
			$size    = $contacts -> contact_size;
		}

		$temp = new stdClass();

		$someObject = $contacts -> contacts;

		
		for ($x = 0; $x <= sizeof($someObject); $x++)
		{

			  $userContactsObj = new stdClass();
		      $user_id = $contacts -> user_id;

			  if(isset( $someObject[$x]->name ))
			  {
				$name    = $someObject[$x]->name;
			  }
  			  if(isset( $someObject[$x]->phone ))
			  {
				$phone   = $someObject[$x]->phone;
			  }

			  $userContactsObj -> user_id = $user_id;
			  $userContactsObj -> name = $name;
			  $userContactsObj -> phone = $phone;


			  $temp -> message =  $this->addContact($userContactsObj);
		} 
 
		$this->GetRegisteredContact($contacts);

}

// getting resgistered from contact list of given user
	function GetRegisteredContact($userObj)
	{

		$validateObject = $userObj;
	
		$util  = new Util();

	
		if(!$validateObject)
		{
			$resObj = $util -> missingParam();
			$this -> output($resObj);
			return;
		}
		else
		{
			if(!property_exists($validateObject,'user_id'))
			{
				$resObj = $util -> missingParam();
				$this -> output($resObj);	
				return;				
			}
		}

		$m = new MySQL5();

		$query = "select tbl_users.id as user_id,tbl_users.name,concat(tbl_users.country_code,tbl_users.mobile) as phone from tbl_users where concat(SUBSTR(country_code, 2, LENGTH(country_code)),mobile) in (select contact_number from tbl_user_contacts where user_id='{$userObj -> user_id}')";


		$contacts = $m -> executeQuery($query,'select');
		$contactlist['status']=1;
		$contactlist['list']=$contacts;

		$this -> output($contactlist);
		
	}

// Code to send Notification to the User 

    /**
     * @param $callNotificationDetail
     */
function callerNotification($callNotificationDetail)
{

		$resObj = new stdClass();				

		$validateObject = $callNotificationDetail;
	
		$util  = new Util();
	
		if(!$validateObject)
		{
			$resObj = $util -> missingParam();
			$this -> output($resObj);
			return;
		}
		else
		{
			if(!property_exists($validateObject,'sender_id'))
			{
				$resObj = $util -> missingParam();
				$this -> output($resObj);	
				return;				
			}
			if(!property_exists($validateObject,'receiver_phone'))
			{
				$resObj = $util -> missingParam();
				$this -> output($resObj);	
				return;				
			}	
			if(!property_exists($validateObject,'message'))
			{
				$resObj = $util -> missingParam();
				$this -> output($resObj);	
				return;				
			}			
				
	}

	$senderid	= $callNotificationDetail -> sender_id;
	if(!$this->isUserExist($senderid))
	{
		$resObj = new stdClass();
		$resObj = $util -> inValidUser();

		$this -> output($resObj);
		return;
	}

	$senderid	= $callNotificationDetail -> sender_id;
	$receiver_phone = $callNotificationDetail -> receiver_phone;
	$message	= $callNotificationDetail -> message;	


	$m = new MySQL5();

	$resultObj = new stdClass();
	$query = "select id ,fcm_token from tbl_users where concat(country_code,mobile)='$receiver_phone'";
	$receiver = $m -> executeQuery($query);

	$receiverid  	= $receiver["id"];
	$receiver_fcm 	= $receiver["fcm_token"];


	if(strlen($receiver_fcm) < 10)
	{
		$resultObj -> message = "User not registered with Smart Call Assistant !".$receiverid;
		$this -> output($resultObj);
		return;
	}


	$query = "select concat(country_code,mobile) as mobile,name from tbl_users where id=$senderid";
	$sender = $m -> executeQuery($query);

	$sender_phone = $sender["mobile"];
	$sender_name = $sender["name"];	

	if(strlen($sender_name) < 2)
	{
		$sender_name = $sender_phone;	
	}	

	$userContactsObj = new stdClass();
	$userContactsObj -> phone = $sender_phone;
	$userContactsObj -> name = $sender_name;
	$userContactsObj -> user_id = $receiverid;
	$ignore = $this->addContact($userContactsObj);


	if($senderid == $receiverid)
	{
		$resultObj -> message = "You cannot send heart to yourself !";
		$this -> output($resultObj);
		return;
	}


	$url = "https://fcm.googleapis.com/fcm/send";

	$resultObj = new stdClass();
	$resultObj -> status = 0;


	$title = $sender_name." is not available !";
	$body= $message;


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
		$resultObj -> response = $err;
	}else{

		$res = json_decode($res, true);

		$resultObj -> status   = 1;
		$resultObj -> success  = $res['success'];
	    	$resultObj -> failure  = $res['failure'];
		$resultObj -> multicast_id = $res['multicast_id'];
		$resultObj -> message  = $message;
		$resultObj -> information  = "Message ->".$message." about the ".$sender_name." sent succesfully !";
		
	} 
	$this -> output($resultObj);
}


	


function getFCMToken($userObj){

	$resObj = new stdClass();
	$resObj -> status = 0;

	
	$util  = new Util();
	
	if(!$userObj)
	{
		$resObj = $util -> missingParam();
		$this -> output($resObj);
		return;
	}


	if(property_exists($userObj,'user_id'))
	{
 		$userid = $userObj -> user_id;
		if(!$this->isUserExist($userid))
		{
		 
			$resObj = $util -> inValidUser();
			$this -> output($resObj);
			return;
		}		
	}


	$resultObj = new stdClass();
	$m = new MySQL5();
	if(property_exists($userObj,'user_id'))
	{
		$query = "select name,fcm_token,concat(country_code,mobile) as mobile from tbl_users where id='{$userObj -> user_id}'";	
	}
	elseif (property_exists($userObj,'mobile')) {
		$query = "select name,fcm_token,concat(country_code,mobile) as mobile from tbl_users where concat(country_code,mobile)='{$userObj -> mobile}'";	
	}
	
	$receiver = $m -> executeQuery($query);

	$resObj = new stdClass();

	if(empty($receiver))
	{
		$resObj -> status = 0;			
		$resObj -> result = "failure";	
		$resObj -> message = "User ID or Mobile does not exist !";			
	}
	else{
		$resObj -> name = $receiver["name"];
		$resObj -> fcm_token = $receiver["fcm_token"];	
		$resObj -> mobile = $receiver["mobile"];		
		$resObj -> status = 1;			
		$resObj -> result = "success";				
		$resObj -> message = "success";					
	}

	$this -> output($resObj);
}

function countContacts($userObj){

	
	$util  = new Util();
	
	if(!$userObj)
	{
		$resObj = $util -> missingParam();
		$this -> output($resObj);
		return;
	}
	else
	{
		if(!property_exists($userObj,'user_id'))
		{
			$resObj = $util -> missingParam();
			$this -> output($resObj);
			return;
		}
								
	}
	

 	$userid = $userObj -> user_id;
	if(!$this->isUserExist($userid))
	{
		 
		$resObj = $util -> inValidUser();
		$this -> output($resObj);
		return;
	}

	$m = new MySQL5();
	$query = "select count(id) from tbl_user_contacts where user_id = '{$userObj -> user_id}'";
	$maxId = $m -> executeScalar($query);
	$resObj = new stdClass();
	$resObj -> contacts = $maxId;
	$this -> output($resObj);
}

// Debuggin Purpose
function saveLogs($userObj)
{
	if(!$userObj)
	{
		$resObj = $util -> missingParam();
		$this -> output($resObj);
		return;
	}
	if(property_exists($userObj,'user_id'))
	{
 		$userid = $userObj -> user_id;
		if(!$this->isUserExist($userid))
		{
		 
			$resObj = $util -> inValidUser();
			$this -> output($resObj);
			return;
		}		
	}

	$m = new MySQL5();
	$resultSupportObj = new stdClass();
	
	$query = "insert into tbl_logs (user_id, message) values ('{$userObj -> user_id}', '{$userObj -> message}')";
	$inserted_id = $m -> executeQuery($query,'insert');
			
	$resultSignUpObj -> status = "1";
	$resultSignUpObj -> user_id = $inserted_id;
	$resultSignUpObj -> message = "Log added";

	$this -> output($resultSignUpObj);


}
function isUserExist($user_id){

	$m = new MySQL5();
	$query = "select count(*) from tbl_users where id = '$user_id'";
	$count = $m -> executeScalar($query);

	if($count == 0)
	{
		return false;
	}
	else{
		return true;
	}

}
}

?>