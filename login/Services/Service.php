<?php
require_once('Mappers/usermodel.php');
require_once('Mappers/try1.php');
class Service
{
	function Service()
	{}
	function login($username,$password)
	{
       if($this->authenticate($username,$password)){
		
	   session_start();
	    return true;
    }  
     else 
	 {
	  	
	  return false;
     }
	}
	
	static function authenticate($u,$p)
	{
		
		$db=new try1();
		$db->db($u,$p);
   if($db->db($u,$p)) {
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
