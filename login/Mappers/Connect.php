<?php 
require_once('Database/Config.php');
class Connect 
{
	public function __construct()
	{
		// $bc = new Config();
	}
public function db($myusername,$mypassword )
{
	 $bc = new Config();
	 $con=$bc->dbconc();
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