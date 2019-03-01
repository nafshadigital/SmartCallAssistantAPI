<?php
include_once "class/HTMLMailTemplate.php";
class MailClient
{
	public static $from;
	private $to;
	
	public function __construct()
	{
		self::$from = "Mobile Car Parking <info@mobilecarparking.com>";
	}
	
	public static function sendEmail($to, $subject, $message, $cc='')
	{
		$officeName = "Mobile Car Parking";
		$officeMail = "info@mobilecarparking.com";
		self::$from = $officeName ."<".$officeMail.">";
		
		$headers = "From:" . self:: $from . "\r\n";  
		$headers .= "Reply-To:". self:: $from . "\r\n";  
		
		if($cc)
			$headers .= "CC: $cc\r\n";  
			
		$headers .= "MIME-Version: 1.0\r\n"; 
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
			$html = new HTMLMailTemplate();
			$html -> officeName = $officeName;
			$html -> officeMail = $officeMail;
			$html -> subject = $subject;
			$html -> message = $message;
			$message = $html -> getHtmlBody();
		
		return mail($to, $subject, $message, $headers);
	}
	
	public static function sendEmailWithAttachments($to, $subject, $message, $attachments = array(), $cc = '')
	{
		// email fields: to, from, subject, and so on
		$officeName = "Mobile Car Parking";
		$officeMail = "info@mobilecarparking.com";
		$from = $officeName ."<".$officeMail.">";
		
		$headers = "From: $from";
		//$headers = "From:" . $from . "\r\n";  
		//$headers .= "Reply-To:". $from . "\r\n";  
		
		if($cc)
			$headers .= "\r\nCC: $cc";  
		// if($cc)
			//$headers .= "CC: $cc\r\n"; 
		// boundary 
		$semi_rand = md5(time()); 
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
		 
		// headers for attachment 
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
		 
		
			$html = new HTMLMailTemplate();
			$html -> officeName = $officeName;
			$html -> officeMail = $officeMail;
			$html -> subject = $subject;
			$html -> message = $message;
			$message = $html -> getHtmlBody();
		
		
		// multipart boundary 
		$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
		$message .= "--{$mime_boundary}\n";
		 
		// preparing attachments
		foreach($attachments as $attachment){
			$fileData = $attachment["data"];
			$fileName = $attachment["name"];
			if($fileData != "")
			{
				$data = $fileData; // Already encoded in the flex application.
				$data = chunk_split(base64_encode($data));
				$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$fileName\"\n" . 
				"Content-Disposition: attachment;\n" . " filename=\"$fileName\"\n" . 
				"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
				$message .= "--{$mime_boundary}\n";
			}
		}		 
		// send		
		
		
		return @mail($to, $subject, $message, $headers); 
	}
	
}
?>