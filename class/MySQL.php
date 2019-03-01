<?php

	////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////	DATABASE INTERACTION	////////////////////////////
	/////////////////////////////////////	CREATED BY KARTHICK.S	////////////////////////////
	/////////////////////////////////////		ON 12-May-2009		////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////

include_once "conf/dbSettings.php";

class MySQL

{

	var $db;	

	

	//==============================	Function to Connect to Database ======================//

	

	function connect()

	{

		$this->db	=	mysql_pconnect(server,db_username,db_password);

		

		mysql_select_db(database,$this->db);

		

		//echo server . username. password;

		

		if(!$this->db)

		

			echo "connection problem..";

			

	}

	

	

	

	//==============================	Function to Disconnect the Database ==================//

	

	function disConnect()

	{

		mysql_close($this->db);

		

	}

	

	//==============================	Function Try to Connect to Database ======================//

	

	function tryToConnect()

	{

		if(!is_resource($this -> db))

		  $this -> connect();

	}

	

	//==============================	Function Try to disConnect to Database ======================//

	

	function tryToDisConnect()

	{

		if($this -> db)

		  $this -> disconnect();

		else 

			echo "Connection to Close does not exist...";

	}

	

	

	//===============================	Function used to insert or update data in DB =========//

	

	function insertUpdate($table, $arrData , $whrCondt)

	{

	

		// auto Connect 

		

		$this -> tryToConnect();

		

		$i	=	1;

		$field = ""; $value ="";

		foreach($arrData as $arr=>$key1)

		{	

			//------------ For Insert query parameter -------------------//

			

			if(!isset($whrCondt))

			{	

		

				$field.=	$arr;

				

				$value.= "'".	$key1 . "'" ;

					

	

			}

			

			//------------ For Update query parameter -------------------//



			else

			{

								

					$field.= $arr  . "='".	$key1 . "'" ;	

					

			}	

			

			//------------ For the comma(,) seperator in query parameter -------------------//



			if($i <	sizeof($arrData))

			{

				$value.=",";

				$field.=",";

			}

			$i++;

			

			

		

		}

	

	//echo "insert into $table($field) values($value)";

	

	if($whrCondt)

	

		mysql_query("update $table set $field where $whrCondt",$this->db);

		

	else

	

	

		mysql_query("insert into $table($field) values($value)",$this->db);

		

		// auto DisConnect

	

		$this -> tryToDisConnect();

	}

	

	

	//========================	Function to read Data From Table ========================//

	

	function select($qry)

	{

		$this -> tryToConnect();

		

		$objData = mysql_query($qry,$this->db);
		$this -> sendErrorToDeveloper($qry);
		

		return $objData;

	}

	

	public function executeQuery($qry)
	{

		$this -> tryToConnect();

		

		$objData = mysql_query($qry,$this->db);
		$this -> sendErrorToDeveloper($qry);
		
		//$this -> tryToDisConnect(); when we enable this line, can't get last inset id
		return $objData;

	}

	

	

	//========================	Function to Delete Data From Table ========================//

	

	function execute($qry)

	{

		$this -> tryToConnect();

		

		$objData = mysql_query($qry,$this->db);
		$this -> sendErrorToDeveloper($qry);
		

		$objRow = mysql_fetch_array($objData);

		

		//$this -> tryToDisConnect(); when we enable this line, can't get last inset id

		

		return $objRow;

	}

	

	

	//========================	Function to Select Data From Table ========================//

	

	function executeScalar($qry)
	{

		$this -> tryToConnect();


		$objData = mysql_query($qry,$this->db);
		$this -> sendErrorToDeveloper($qry);
		
		if(!is_resource($objData))
		    return NULL;
			
		$objRow = mysql_fetch_array($objData);
		

		//$this -> tryToDisConnect(); when we enable this line, can't get last inset id
	

		return $objRow[0];
	}

	public function getLastInsertId()
	{
		return mysql_insert_id($this -> db);
	}
	

	//========================	Function to Count the Data From Table ========================//

	

	function recordCount($qry)

	{

		$this -> tryToConnect();

		

		$objData = mysql_query($qry,$this->db);

		$this -> sendErrorToDeveloper($qry);

		//$this -> tryToDisConnect();

		

		return mysql_num_rows($objData);
	}
	
	
	function sendErrorToDeveloper($query) {
		
		$errorNo = mysql_errno();
		$errorMessage = mysql_error();
		
		if($errorNo || $errorMessage) {
			echo "<h1>Query Error $errorNo $errorMessage</h1>";
			$message = "<!DOCTYPE html>
							<head>
							<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
							<title>Untitled Document</title>
							</head>
							<body>
							<h2>Hi Developer,</h2>
							<table><tr><th>Error No</th><td>$errorNo</td></tr><tr><th>Error Message</th><td>$errorMessage</td></tr><tr><th>Query</th><td><pre>$query</pre></td></tr></table>
							<br />Thanks,<br />
							<b>EvisionCare</b>
							</body>
							</html>";
							
			$headers = "From: EvisionCare Internal Software <no-reply@evisioncare.com>\r\n";  
			$headers .= "Reply-To: no-reply@evisioncare.com\r\n";  
			$headers .= "MIME-Version: 1.0\r\n"; 
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$domain = $_SERVER['SERVER_NAME'];
			$to = "evision.developer@gmail.com";
			$subject = "Query Error on ". $domain . " " .date("Y-M-d D h:i a");
			@mail($to, $subject, $message, $headers);
		}
	}
}
?>