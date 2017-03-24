<?php 

//require_once('Database/config.php');

class try1 
{
 public $name;
 public $pass; 
public function db($myusername,$mypassword )
{
	$DB_DATABASE="admin";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
    $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
	  
    
       $sql = "SELECT uname,pwd FROM uname WHERE uname = '$myusername' and pwd = '$mypassword'";
      $result = mysqli_query($con,$sql);
      $count = mysqli_num_rows($result);
	
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($count == 1) {
		  
		 return true;
      }else {
		 return false;
      }
}
}
	
	?>