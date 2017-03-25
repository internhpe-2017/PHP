<?php
$servername = "localhost";
$username = "root";
$password = "";


$conn = new mysqli_connect($servername, $username, $password);

if ($conn->mysqli_connect_error())
{
	die("connection failed: " . $con->$conn->mysqli_connect_error());
}

$db_selected = mysqli_select_db($conn, 'trip_rest');
if (!$db_selected)
{
	die (' error: ' . mysqli_error());
}
?>