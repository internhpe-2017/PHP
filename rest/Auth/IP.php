<?php


class Auth_IP {
	private $clientMapper;

 	
	public function __construct() {
		$this->clientMapper = new Mappers_Client();
	}
	
	public function check() {
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
		} else {
			$ip = trim($_SERVER['REMOTE_ADDR']);
		}
 		return $this->clientMapper->findByIp($ip);
	}

}

?>
