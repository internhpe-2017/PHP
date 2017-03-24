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
    $particular=$_POST['part'];
	$date=$_POST['datee'];
	$amount=$_POST['amount'];
	if( $serv->login($username,$particular,$date,$amount) )
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
