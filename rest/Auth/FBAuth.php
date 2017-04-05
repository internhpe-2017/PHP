<?php


class Auth_FBAuth {
	private $clientMapper;

	public function __construct() {
		$this->clientMapper = new Mappers_Client();
	}

	public function check( ) {

		//$_SESSION['username']='saju';
 		//return $this->clientMapper->findFaceBookAuth( );
	}
}

?>
