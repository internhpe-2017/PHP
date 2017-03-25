<?php
$db_host = 'localhost'; //hostname
$db_user = 'root'; // username
$db_password = ''; // password
$db_name = 'admin'; //database name
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name );
if(! $conn )
{
  die('Could not connect: ' . mysqli_error());
}
echo 'Connected successfully';


?>
