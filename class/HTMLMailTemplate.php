<?php

class HTMLMailTemplate { 
	function SendMail(  $Email = "" , $Subject = "" , $Body = ""){
		if (!$this->spamcheck( $Email )) {
			return "Invalid EMail ID";
		}
		else {
			
			$headers  = "From: {$this -> clinicName}<{$this -> clinicEmail}>\r\n";
			$headers .= "Reply-To: {$this -> clinicName}<{$this -> clinicEmail}>\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			
			if ( @mail( $Email , "$Subject" , $Body , $headers ) ){
				return "success";
			}
			else{
				return "failed";
			}
		}
	}
	
	function spamcheck($field){
		$field = filter_var ( $field, FILTER_SANITIZE_EMAIL );
		
		if(filter_var($field, FILTER_VALIDATE_EMAIL)){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	function getHtmlBody(){
	   		
		$clientName = ""; //$this -> officeName;
		$clientEmail = $this -> officeMail;
		$clientLogo = "http://". $_SERVER['HTTP_HOST'] . "/images/email_logo.jpg";
		$clientSlogan = "";
		
		$Date = date ( 'd M , Y' );
		$Body = "{$this -> message}";		
	
	return $Body;
	}
}
?>