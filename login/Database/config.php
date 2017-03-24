<? php
class config
{
	 function dbconc()
	 {
	$DB_DATABASE="admin";
	$DB_HOST="localhost";
	$DB_PASSWORD='';
	$DB_USER="root";
   $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
    return $con;
	$conec= new config();
	  $conec->dbconc();
	  
	  
	   $name = mysql_fetch_object($result,'uname');
		 $pass = mysql_fetch_object($result,'pwd');
		 return array($name,$pass);
		 
	 }	 
}

	?>