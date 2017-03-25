<?php
    include_once('config.php');
	
	public function occassion_reports()
	{
	
	$uid = isset($_GET['uid']) ? mysql_real_escape_string($conn,$_GET['uid']) : "")
	$query = array();
	$result = array();
	while($r = mysqli_fetch_array($query))
	{
		extract($r);
		$result[] = array("name" => $name, "occassion" => $occassion, "particular" => $particular, "amount" => $amount, "date" => $date);
	}
	
	$json =  array("status" => 1, "info" => $result);
	}
	else
	{
		$json = array("status" => 0, "msg" => "occassion not created");
	}
	@mysqli_close($conn);
	
	header('content-type: application/json');
	return json_encode($json);
	
     }
		?>
		
