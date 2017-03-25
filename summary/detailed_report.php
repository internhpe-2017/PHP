<?php
/* include dbconfig.php */
include_once('config.php');

// Get user id
$uname = isset($_GET['name']) ? mysqli_real_escape_string($conn,$_GET['name']) : "";
$pwd = isset($_GET['password']) ? mysqli_real_escape_string($conn,$_GET['password']) : "";

if(empty($uname)){
$data = array("result" => 0, "message" => "Wrong user id Let's try once again!");
} else {
// get user data
$sql = "SELECT name,particular,amount,date FROM expense where name='$uname' and password='$pwd'";
$select = mysqli_query($conn,$sql);
$result = array();
while($data = mysqli_fetch_assoc($select)) {
$result[] = $data;
}

$data = array("result" => 1, "data" => $result);
}

mysqli_close($conn);
/* JSON Response */
header('Content-type: application/json');
echo json_encode($data);

?>
