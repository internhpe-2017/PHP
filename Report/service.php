<? php

include_once("config.php");


if($_server['REQUEST_METHOD') =="POST")
{
	$name = isset($_POST ['name']) ? mysqli_real_escape_string($conn,$_POST['name']) : "";
	$occassion = isset ($_POST ['occassion']) ? mysqli_real_escape_string($conn,$_POST['occasion']) : "";
	$particular = isset ($_POST ['particular']) ? mysqli_real_escape_string($conn,$_POST['particular']) : "";
    $amount = isset ($_POST ['amount']) ? mysqli_real_escape_string($conn,$_POST['amount']) : "";
	$date = isset ($_POST ['date']) ? mysqli_real_escape_string($conn,$_POST['date']) : "";
	
	$sql = "SELECT * FROM EXPENSE WHERE occassion ='".$occassion."';";
	
	$query = mysqli_query($conn,$sql);
	if($query)
	{
		$json = array("status" => 1, "msg" => $result);
	}
	else
	{
		$json = array("status" => 2, "msg" => "Occassion not created");
	}
	@mysqli_close($conn);
	
	header('Content-type: application/json');
	return json_encode($json);
?>	
	
	
	
