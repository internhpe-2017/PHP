<?php

class Auth_KTUtils {
	private $clientMapper;

	public function __construct() {
		$this->clientMapper = new Mappers_Client();
	}

	public function generateSalt( ) {

 	}


	public function generatePublickey($principal,$salt) {

 	}

	public function  generatePrivateToken($keyId,$salt)
	{

	}


}
?>