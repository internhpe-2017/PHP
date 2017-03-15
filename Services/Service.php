<?php
require_once('Mappers/usermodel.php');
class Service
{
	function Service()
	{}
	function login($username,$password)
	{
       if($this->authenticate($username, $password)){
	   session_start();
	    $user=new usermodel($username);
	    $_SESSION['user']=$user;
	    return true;
    }  
     else 
	 {
	  return false;
     }
	}
	
	static function authenticate($u, $p)
	{
		$authentic=false;
	if($u=='admin' && $p='admin') $authentic=true;
	return $authentic;
   }
}
