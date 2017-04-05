<?php

class Auth_KTAuth {
	private $clientMapper;

	public function __construct() {
		$this->clientMapper = new Mappers_Client();
	}

	public function check( ) {
 		list($key, $token ) = explode(':' , base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
    		return $this->clientMapper->loginByKT($key, $token );
	}
}
?>