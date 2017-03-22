<?php
$servername = "localhost";
$username = "root";
$password = "";


$conn = new mysqli(servername, $username, $password);

if ($conn->connect_error)
{
	die("connection failed: " . $con->$conn->connect_error);
}

$db_selected = mysqli_select_db($conn, 'trip_rest');
if (!$db_selected)
{
	die (' error: ' . mysqli_error());
}
?>