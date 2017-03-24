<?php
require_once('Services/Service.php');
class usercontroller extends Service
{ 
  
	//$service1= new Service();
  function usercontroller()
   {
     
	 }
   function get_value()
   {	 
   $serv= new Service();
    $username=$_POST['user'];
    $password=$_POST['pass'];
	if( $serv->login($username,$password) )
	{	
		return true;
	}
	else
	{
		return false;
	}
   }
//function method?()
////{
   //// $service->login($username, $password)
//}

function logout()
{
	session_start();
	session_destroy();
}
}
?>
