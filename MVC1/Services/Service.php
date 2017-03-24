<?php
require_once('Mappers/usermodel.php');
require_once('Mappers/try1.php');
class Service
{
	function Service()
	{}
	function login($username,$particular,$date,$amount)
	{
       if($this->authenticate($username,$particular,$date,$amount)){
		
	   session_start();
	    return true;
    }  
     else 
	 {
	  	
	  return false;
     }
	}
	
	static function authenticate($u,$p,$d,$a)
	{
		
		$db=new try1();
		//$db->db($u,$p,$d,$a);
   if($db->db($u,$p,$d,$a)) {
	  $authentic=true;
      return $authentic;
      }
 	  else
	  {
     $authentic=false;
     return $authentic;
	  }
		  
		  
   }
}
