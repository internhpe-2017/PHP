<?php 
require "config.php";

class try
{
	public function db()
	{
     
	$un = "SELECT uname FROM uname";
	$pw = "SELECT pwd FROM uname";
    $a=mysqli_query($con,$un);
    $b=mysqli_query($con,$pw);
    return $a,$b;
	}
	}
	?>