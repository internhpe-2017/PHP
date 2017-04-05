<?php
//session_write_close();
//session_id($_GET['sessionid']);
session_start();

//require_once('timeout.php');

date_default_timezone_set('UTC');
if(isset($_SESSION['isLoggedIn']) &&  $_SESSION['isLoggedIn']=="isLoggedIn")
{


	//  user is logged in
	$hasSessionExpired = checkIfTimedOut('configrest');

	// print_r('rest'.$hasSessionExpired.' config.php');
	if($hasSessionExpired)
	{

		unset($_SESSION['isLoggedIn']);
		unset($_SESSION['loggedAt']);
		unset($_SESSION['jdata']);
		 unset($_SESSION['userinfo']);
		unset($_SESSION['module']);

		//session_unset();
		header("Location:logout.php");
		//exit;
 	}


}
else
{

  


	//header("Location:logout.php");

	//  print_r('MYSESSIONDATA IS THIS sdsd');
	//  exit();
	//  user is not logged in
	//  simply redirect to index.html
	//  session_unset();
	//  print_r( selfURL() ) ;
	// print_r($_SESSION['jdata'].'---------------');

	// exit;
}



//$site_http_user_path="http://localhost/";
final class Config {

	const DB_HOST = '127.0.0.1';
	const DB_USER = 'holiconnect';
	const DB_PASS = 'holiconnect123';
	const DB_NAME = 'holiconnect';
	const DB_TYPE = 'Mysql';
	const DB_NEW_LINK = 1;
	const USE_PCONNECT = 0;
	const DEBUG = 1; //Possible values are 0 or 1
	const RESPONSE_FORMAT = 'json'; //Possible values are 'xml', 'json', 'querystring'
	const USE_CACHE = 0; //Possible values are 0 or 1
	const CACHE_CLASS = 'Memcached'; // Possible values are 'Memcached'
	const ENABLE_AUTH = 1; //Possible values are 0 or 1
	const SITE_HTTP_USER_PATH="http://localhost/";
	const Host = "smtp1.servage.net";
	const SMTPAuth = true;
	const Username = 'buzz@bumblebeeholidays.com';
	const Password = 'buzz123$';
	const From="ConnectDays <info@bumblebeeholidays.com>";
	const FromName="ConnectDays Team";
	const Sender="info@bumblebeeholidays.com";

}
?>
