<?php

class Auth_HttpBasic {
	private $clientMapper;

	public function __construct() {
		$this->clientMapper = new Mappers_Client();
	}

	public function check( ) {
	
		$client_id='';
		$client_password ='';
		$id_cut=explode(':' , base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
		if (isset( $id_cut[0], $id_cut[1]  ) ) 
		{
			 $client_id=$id_cut[1];
			 $client_password =$id_cut[2];
		 }
		 
		return $this->clientMapper->loginByHttpBasic($client_id, $client_password );
		 
	}
}
?>