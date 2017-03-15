<?php
require_once('Controllers/usercontroller.php');
//require_once('usermodel.php');
@$op=$_REQUEST['op']; //op = submit button in front end
$user_controller= new usercontroller();
switch($op){
case 'login':
              //$username=$_POST['user'];
              //$password=$_POST['pass'];
			  if($user_controller->get_value())
			  {
				  header("Location:main.php");
			  }
				  else
				  {
					  header("Location:login.php?err=1");
				  break;
				  }
case 'logout':
		$user_controller->logout();
		header("Location:login.php");
		break;
		default: header("Location:login.php");
		break;
		}
	?>