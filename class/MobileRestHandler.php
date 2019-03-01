<?php
require_once("SimpleRest.php");
require_once("Mobile.php");
		
class MobileRestHandler extends SimpleRest {

	function getAllMobiles() {	
		$mobile = new Mobile();
		$rawData = $mobile->getAllMobile();
		$this -> output($rawData);		
	}
	
	public function getMobile($id) {

		$mobile = new Mobile();
		$rawData = $mobile->getMobile($id);
		$this -> output($rawData);
	}	
}
?>