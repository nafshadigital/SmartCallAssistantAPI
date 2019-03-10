<?php
require_once("SimpleRest.php");
require_once("Mobile.php");

include_once "conf/dbSettings.php";
include_once 'class/MySQL.php';
include_once 'class/MailClient.php';
class SmartCallRestHandler extends SimpleRest 
{
	function getCountry()
	{
		$arr = json_decode(file_get_contents("CountryCodes.json"));
		$this -> output($arr);
	}
		
	function signUp($signUpObj)
	{
		if(!$signUpObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL();
		$resultSignUpObj = new stdClass();
		$resultSignUpObj -> status = 0;
		
		$query = "select count(id) isExists, id from tbl_users where mobile = '{$signUpObj -> mobile}' ";
		$res = $m -> executeQuery($query);
		$row = mysql_fetch_object($res);
		if($row -> isExists > 0)
		{
			$resultSignUpObj -> status = "1";
			$resultSignUpObj -> user_id = $row -> id;
			$resultSignUpObj -> message = "Sign Up Success";
		}
		else
		{
			$query = "insert into tbl_users ( 	country_code, mobile, created_date	 ) value ( 	'{$signUpObj -> country_code}', '{$signUpObj -> mobile}', '".date('Y-m-d')."'	 )";
			$m -> executeQuery($query);
			
			$resultSignUpObj -> status = "1";
			$resultSignUpObj -> user_id = mysql_insert_id();
			$resultSignUpObj -> message = "Sign Up Success";
		}
		
		$query = "select count(id) from tbl_verification_code where user_id = '{$resultSignUpObj -> user_id}' ";
		$isExists = $m -> executeScalar($query);
		
		$otpQuery = "select FLOOR(RAND() * 8997.00+ 100000)";
		$otp = $m -> executeScalar($otpQuery);
		$resultSignUpObj -> otp = $otp;
		if($isExists == 0)
		{
			$query = "insert into tbl_verification_code ( 	user_id, verification_code, created_date	 ) value ( 	'{$resultSignUpObj -> user_id}', '$otp', '".date('Y-m-d H:i:s')."'	 )";
			$m -> executeQuery($query);
		}
		else
		{
			$query = "update tbl_verification_code set verification_code = '$otp', created_date = '".date('Y-m-d H:i:s')."', status = '1' where user_id = '{$resultSignUpObj -> user_id}' ";
			$m -> executeQuery($query);
		}
		
		$this -> output($resultSignUpObj);
	}
	
	function checkOtp($checkOtpObj)
	{
		if(!$checkOtpObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL();
		$resultCheckOtpObj = new stdClass();
		
		$query = "select count(id) from tbl_verification_code where user_id = '{$checkOtpObj -> user_id}' and verification_code = '{$checkOtpObj -> verification_code}' ";
		$isExists = $m -> executeScalar($query);
		
		if($isExists != 0)
		{
			$query = "update tbl_verification_code set status = '0' where user_id = '{$checkOtpObj -> user_id}' ";
			$m -> executeQuery($query);
		
			$resultCheckOtpObj -> status = "1";
			$resultCheckOtpObj -> message = "Verification Code is Success..";
		}
		else
		{
			$resultCheckOtpObj -> status = "0";
			$resultCheckOtpObj -> message = "Verification Code is Wrong..";
		}
		
		$this -> output($resultCheckOtpObj);
	}
	
	function updateAccount($updateAccObj)
	{
		if(!$updateAccObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL();
		$resultUpdateAccObj = new stdClass();

		$query = "select count(name) from tbl_users where email = '{$updateAccObj -> email}'";
		$isExists = $m -> executeScalar($query);

		if($isExists == 0)
		{
			$query = "update tbl_users set name = '{$updateAccObj -> name}', email = '{$updateAccObj -> email}' where id = '{$updateAccObj -> id}' ";
			$m -> executeQuery($query);

			$resultUpdateAccObj -> message = "Update Account Success..";
		}
		else
		{
			$resultUpdateAccObj -> message = "Email Address already exist !";
		}
		$this -> output($resultUpdateAccObj);
	}
	
	function addContact($userContactsObj)
	{
		if(!$userContactsObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL();
		$resultSupportObj = new stdClass();
		
		$query = "insert into tbl_user_contacts (user_id, contact_name,contact_number,isRegistered) 

		value ('{$userContactsObj -> user_id}', '{$userContactsObj -> contact_name}', '{$userContactsObj -> contact_number}', false)";
		$m -> executeQuery($query);
		
		$id = mysql_insert_id();
		
	
		$resultSupportObj -> message = {$userContactsObj -> contact_number};
		
		$this -> output($resultSupportObj);
	}
	function createSupport($createSupportObj)
	{
		if(!$createSupportObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL();
		$resultSupportObj = new stdClass();
		
		$query = "insert into tbl_support ( 	user_id, message, msg_date	 ) value ( 	'{$createSupportObj -> user_id}', '{$createSupportObj -> message}', '".date('Y-m-d H:i:s')."'	 )";
		$m -> executeQuery($query);
		
		$id = mysql_insert_id();
		
		$this -> sendCreateSupportEmail($id);
		
		$resultSupportObj -> message = "Support Create Success and send email for admin..";
		
		$this -> output($resultSupportObj);
	}
	
	function getAdminEmailIds()
	{
		$m = new MySQL();
		$query = "select admin_email_ids from tbl_admin_email_ids where 1 ";
		return $m -> executeScalar($query);
	}
	
	function sendCreateSupportEmail($id)
	{
		$m = new MySQL();
		$mc = new MailClient();
		
		$query = "select t1.*, t2.name, mobile from tbl_support t1 left outer join tbl_users t2 on t1.user_id = t2.id where t1.id = '$id' and t1.status = '0' ";
		$res = $m -> executeQuery($query);
		$row = mysql_fetch_object($res);
		
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
		
		$m = new MySQL();
		$resultLastseenObj = new stdClass();
		
		$query = "update tbl_users set last_seen = '".date("Y-m-d H:i:s")."' where id = '{$updateLastObj -> user_id}' ";
		$m -> executeQuery($query);
		
		$resultLastseenObj -> message = "Last Seen Update Success..";
		
		$this -> output($resultLastseenObj);
	}
	
	function getProfile($profileObj){
		if(!$profileObj){
		echo "Incorrect Parameter";
		return;
		}
		$m = new MySQL();
		$resultSignIn = new stdClass();
		
		$user = new stdClass();
		$user -> user_exists = 0;
		$user -> message = "user does not exists.";
		
		if(isset($profileObj -> android_id) && isset($profileObj -> device_id)) {
				$query = "select * from tbl_users where id = '{$profileObj -> id}' ";
				$res = $m -> executeQuery($query);
				if($row = mysql_fetch_object($res)){
				$user = $row;
				$user -> user_exists = 1;
				$user -> message = "User exists";
				}
			}
			$this -> output($user);
				
}


function sendNotification($sendNotiObj){
		if(!$sendNotiObj)
		{
			echo "Incorrect Parameter";
			return;
		}
		
		$m = new MySQL();
		$resultNotiObj = new stdClass();
		$resultNotiObj -> status = 0;
		
		$query = "select id from tbl_users where concat(country_code,mobile) = '{$sendNotiObj -> phnNum}' ";
		$to_user_id = $m -> executeScalar($query);
		
		
		
		if($to_user_id){
			$query = "insert into tbl_notifications(user_id,to_user_id,message,created_date) value ('{$sendNotiObj -> user_id}','$to_user_id','{$sendNotiObj -> message}','".date('Y-m-d H:i:s')."')";
			$m -> executeQuery($query);
			
			$resultNotiObj -> status = "1";
			$resultNotiObj -> message = "Success";
		}
		
		$this -> output($resultNotiObj);
	}
	
function getMaxid($userObj){
	$m = new MySQL();
	$query = "select max(id) from tbl_notifications where to_user_id = '{$userObj -> user_id}'";
	$maxId = $m -> executeScalar($query);
	$resObj = new stdClass();
	$resObj -> maxId = $maxId;
	$this -> output($resObj);
}

function getNotification($getNotiObj){
	$m = new MySQL();

	//SELECT n.id,n.created_date,n.message,n.to_user_id,n.user_id,u.name FROM tbl_notifications n ,tbl_users u WHERE n.id > 49 AND n.to_user_id = 193 AND u.id = n.to_user_id ORDER BY n.id DESC LIMIT 0,5
	


	$query = "select * from tbl_notifications where id > '{$getNotiObj -> id}' and to_user_id = '{$getNotiObj -> user_id}' order by id desc limit 0,5";
	$query = "	SELECT n.id,n.created_date,n.message,n.to_user_id,n.user_id,u.name FROM tbl_notifications n ,tbl_users u WHERE n.id > '{$getNotiObj -> id}' and n.to_user_id = '{$getNotiObj -> user_id}' AND u.id = n.to_user_id ORDER BY n.id DESC LIMIT 0,5";

	//echo $query;
	$res =  $m -> executeQuery($query);
	$arr = array();
	while($row = mysql_fetch_assoc($res)) {
		$arr[] = $row;
	}
	
	$objRes = new stdClass();
	$objRes -> status = "0";
	$objRes -> message = "Notifications";
	$objRes -> notifications = $arr;
	
	if(mysql_num_rows($res) > 0){
		$objRes -> status = "1";
		$objRes -> message = "";
	}
	
	$this -> output($objRes);
}

}
?>>