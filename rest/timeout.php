<?php
function checkIfTimedOut($classname)
{
 	$current = time();// take the current time
 	
 	
	if(isset($_SESSION['loggedAt']))
	{
		$diff = $current - $_SESSION['loggedAt'];
 		if($diff >= $_SESSION['timeOut'])
		{
		
			unset($_SESSION['isLoggedIn']);
			unset($_SESSION['jdata']);
			unset($_SESSION['loggedAt']);			
			unset($_SESSION['userinfo']);
			unset($_SESSION['module']);

			return true;
		}
		else
		{
 			return false;
		}
	}
	else
	{
			//return true;
	}
}

function selfURL(){
    if(!isset($_SERVER['REQUEST_URI'])){
        $serverrequri = $_SERVER['PHP_SELF'];
    }else{
        $serverrequri =    $_SERVER['REQUEST_URI'];
    }
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.$serverrequri;
}

function strleft($s1, $s2) {
	return substr($s1, 0, strpos($s1, $s2));
}
?>