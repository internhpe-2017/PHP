<?php 

require_once('OAuthClient.php');
$x = new OAuthClient('http://localhost/rest', 'GhEWOakP0gyEHFG3tPyn+Kw2+fk=', 'MjU2N2Y1YjNlMWNhOWJlOGE3N2Q4ZGFiMzFlZGIyYjU=',
		'test', '', '');
		
$nonce = sha1(mt_rand() . time());
$timestamp = time();
$requestUrl = '/login';
$password = '5467';
$params = array('loginId' => '+919448855992',
			'nonce' => $nonce,
			'timestamp' => $timestamp,
			'digest' => getDigest($requestUrl, $password, $nonce, $timestamp));

$message = '<?xml version="1.0" encoding="UTF-8"?>';
$message .= '<tokenCreationRequest ><digestAuth>';
$message .= '<username>' . $params['loginId'] . '</username>';
$message .= '<nonce>' . $params['nonce'] . '</nonce>';
$message .= '<timestamp>' . $params['timestamp'] . '</timestamp>';
$message .= '<digest>' . $params['digest'] . '</digest>';
$message .= '</digestAuth></tokenCreationRequest>';

$x->restCall('POST', $requestUrl,array('XMLRequest' => $message), false);
$response = $x->getLastResponse();
 
$lastStatus = $x->getLastStatus();
echo $response . '<br />' . $lastStatus;

function getDigest($r, $p, $n, $t)
{
	$hash = md5(sha1($r . $p, true));
	return base64_encode(sha1($n . $hash . $t, true));
}
?>