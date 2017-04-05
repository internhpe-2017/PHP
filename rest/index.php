<?php
  // ini_set('display_errors',1);
  // error_reporting(E_ALL);
ob_start();
 
require_once('Config.php');
//require_once('Library/Validation.php');
//require_once('Library/KM/MessageDigest.php');
//require_once('Library/KM/MD5.php');
//require_once('Library/KM/SHA1.php');
//require_once('Library/KM/AuthUtility.php');
//require_once('Library/KM/IHashAlgorithm.php');



// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Ensure Library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH),
    get_include_path(),
)));
// Define path to data directory
defined('APPLICATION_DATA')
    || define('APPLICATION_DATA', realpath(dirname(__FILE__) . 'data/logs'));




function __autoload($path) {
	return include str_replace('_', '/', $path) . '.php';
}


$rest = new Rest();
$rest->process();
ob_end_flush();
?>

