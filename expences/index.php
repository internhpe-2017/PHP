<?php
require_once('Controllers/usercontroller.php');
//require_once('Mappers/usermodel.php');

@$op=$_REQUEST['op']; //op = submit button in front end
$user_controller= new usercontroller();
switch($op){
case 'Add':
			  $user_controller->get_value();
					 break;	  
case 'view':
		$user_controller->view();
		break;
case 'ownview':
	    $user_controller->Ownview();
					 break;	  
case 'update':
			  $user_controller->Update();
					 break;	  					 
		}
	?>