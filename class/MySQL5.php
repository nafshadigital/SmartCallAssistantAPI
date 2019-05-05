<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__)  . $ds . '..') . $ds;
require_once("{$base_dir}conf{$ds}dbSettings.php");


class MySQL5

{

	var $db;	

	//==============================	Function to Connect to Database ======================//


	function connect()

	{

		$this->db	=	mysqli_connect(server,db_username,db_password);

		

		mysqli_select_db($this->db,database);

		

		//echo server . username. password;

		

		if(!$this->db)

		

			echo "connection problem..";

			

	}

	

	

	

	//==============================	Function to Disconnect the Database ==================//

	

	function disConnect()

	{

		mysqli_close($this->db);

		

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

	

		mysqli_query($this->db,"update $table set $field where $whrCondt");

		

	else

	

	

		mysqlii_query("insert into $table($field) values($value)",$this->db);

		

		// auto DisConnect

	

		$this -> tryToDisConnect();

	}

	

	

	//========================	Function to read Data From Table ========================//

	

	function select($qry)

	{

		$this -> tryToConnect();

		

		$objData = mysqli_query($this->db,$qry);
		$this -> sendErrorToDeveloper($qry,$this->db);
		

		return $objData;

	}

	

	public function executeQuery($qry,$type=NULL)
	{

		$this -> tryToConnect();

		$objData = mysqli_query($this->db,$qry);
		
		$this -> sendErrorToDeveloper($qry,$this->db);
		

		if($type=="insert"){

		
		$rowid = mysqli_insert_id($this->db);
		$this -> tryToDisConnect(); //when we enable this line, can't get last inset id

		return $rowid;

		
		}
		elseif($type=="select")
		{
		
		$arr = array();

			while($row = mysqli_fetch_assoc($objData)) {

				$arr[] = $row;

			}
		$this -> tryToDisConnect(); //when we enable this line, can't get last inset id
		return $arr;
		
		}elseif($type=="update")
			{
			
			
			}
			else{

			$objRow =0;
	
			if(mysqli_num_rows($objData) > 0 ){

			$objRow = mysqli_fetch_array($objData);
			}
    
			$this -> tryToDisConnect(); //when we enable this line, can't get last inset id

	    	return $objRow;
		}

	}

	

	

	//========================	Function to Delete Data From Table ========================//

	

	function execute($qry)

	{

		$this -> tryToConnect();

		

		$objData = mysqli_query($this->db,$qry);
		$this -> sendErrorToDeveloper($qry,$this->db);
		

		$objRow = mysqli_fetch_array($objData);

		

		//$this -> tryToDisConnect(); when we enable this line, can't get last inset id

		

		return $objRow;

	}

	

	

	//========================	Function to Select Data From Table ========================//

	

	function executeScalar($qry)
	{

		$this -> tryToConnect();


		$objData = mysqli_query($this->db,$qry);
		$this -> sendErrorToDeveloper($qry,$this->db);
		
			
		$objRow = mysqli_fetch_array($objData);
		

		//$this -> tryToDisConnect(); when we enable this line, can't get last inset id
	

		return $objRow[0];
	}

	public function getLastInsertId()
	{
		return mysqli_insert_id($this -> db);
	}
	

	//========================	Function to Count the Data From Table ========================//

	

	function recordCount($qry)

	{

		$this -> tryToConnect();

		

		$objData = mysqli_query($qry,$this->db);

		$this -> sendErrorToDeveloper($qry,$this->db);

		//$this -> tryToDisConnect();

		

		return mysqli_num_rows($objData);
	}
	
	
	function sendErrorToDeveloper($query,$db) {
		
		$errorNo = mysqli_errno($db);
		$errorMessage = mysqli_error($db);
		
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
							<b>SmartCall Assistant</b>
							</body>
							</html>";
							
			$headers = "From: SmartCall Assistant <no-reply@gmail.com>\r\n";  
			$headers .= "Reply-To: no-reply@gmail.com\r\n";  
			$headers .= "MIME-Version: 1.0\r\n"; 
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			$domain = $_SERVER['SERVER_NAME'];
			$to = "nafshadigital@gmail.com";
			$subject = "Query Error on ". $domain . " " .date("Y-M-d D h:i a");
			@mail($to, $subject, $message, $headers);
		}
	}
}
?>