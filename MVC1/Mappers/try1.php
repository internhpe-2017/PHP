<?php 

//require_once('Database/config.php');

class try1 
{
 public $name;
 public $pass; 
public function db($myusername,$myparticular,$date,$amount )
{
	$DB_DATABASE="trip";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
    $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
	  
    
       $sql = "INSERT INTO EXPENCES (NAME,PARTICULAR,PDATE,AMOUNT) VALUES ('$myusername','$myparticular','$date','$amount')";
      $result = mysqli_query($con,$sql);
      //$count = mysqli_num_rows($result);
	
      
      // If result matched $myusername and $mypassword, table row must be 1 row
		
      if($result) {
		  
		 return true;
      }else {
		 return false;
      }
}
}
	
	?>