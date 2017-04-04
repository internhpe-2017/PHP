<?php
class Config
{
    function dbconc()
	 {
	     $DB_DATABASE="admin";
	     $DB_HOST="localhost";
	     $DB_PASSWORD='';
	     $DB_USER="root";
      $con = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_DATABASE);
    return $con;
	 }
}	 