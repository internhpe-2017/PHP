<?php 

//require_once('Database/config.php');

class try1 
{
 public $name;
 public $pass; 
	function __construct() 
	{
	 
    
	}
 
	public function Insert($myusername,$myparticular,$date,$amount )
	{
		$DB_DATABASE="trip";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
	
	  $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
    
       $sql = "INSERT INTO EXPENCES (NAME,PARTICULAR,PDATE,AMOUNT) VALUES ('$myusername','$myparticular','$date','$amount')";
      $result = mysqli_query($con,$sql);
			if($result) {
		  
				return true;
			}else {
				return false;
			}
	}
	public function view()
	{
		$DB_DATABASE="trip";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
	
	  $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
		$sql = "SELECT *FROM EXPENCES";
      $result = mysqli_query($con,$sql);
	  while($row = $result->fetch_assoc()) {
        echo "name: " . $row["NAME"]. " ----particular: " . $row["PARTICULAR"]. "----date:" . $row["PDATE"]."---AMOUNT:".$row["AMOUNT"]. "<br>";
    }
	}
	public function Ownview($myusername)
	{
		$DB_DATABASE="trip";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
	
	  $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
		$sql = "SELECT *FROM EXPENCES WHERE NAME='$myusername'";
      $result = mysqli_query($con,$sql);
	  $row = $result->fetch_assoc();
	  echo "name: " . $row["NAME"]. " ----particular: " . $row["PARTICULAR"]. "----date:" . $row["PDATE"]."---AMOUNT:".$row["AMOUNT"]. "<br>";
	}
	public function Update($myusername,$myparticular,$date,$amount)
	{
		$DB_DATABASE="trip";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
	
	  $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
		$sql = "UPDATE EXPENCES SET PARTICULAR='$myparticular' PDATE='$date' AMOUNT='$amount' WHERE NAME='$myusername'";
		$result = mysqli_query($con,$sql);
	}

	
}
	
	?>