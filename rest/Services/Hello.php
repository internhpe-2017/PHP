<?php
class Services_Hello {


 
	public function __construct() {
	}
	public function get($request) { 
	  	$a = $this->hello(request); 
  		return $a; 
 	}
	public function hello($request) {
		$hellomapper = new Mappers_Hello();
  
		   			
		//$a = $hellomapper->hello( );
  		 
 		return $a ;
 	}
?>
