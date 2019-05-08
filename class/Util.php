<?php


class Util

{

	function missingParam()
	{
		$resObj = new stdClass();
		$resObj -> status = 0;			
		$resObj -> result = "failure";	
		$resObj -> message = "Missing parameters";	

		return $resObj;
	}

	function inValidUser()
	{
		$resObj = new stdClass();
		$resObj -> status = 0;			
		$resObj -> result = "failure";	
		$resObj -> message = "Invalid User";	

		return $resObj;
	}	
}