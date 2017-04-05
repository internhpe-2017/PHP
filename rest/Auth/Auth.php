<?php


class Auth_Auth {
	const HTTP_BASIC = 1;
	const IP = 2;
	const OAUTH = 3;
	const FB_AUTH = 4;

	const HTTP_BASIC_STR = "Basic";
	const IP_STR = "IP";
	const OAUTH_STR = "OAuth";
	const FB_AUTH_STR = "FB";

	private static $instance;
	private $auth;
	private $type;
	private $identity;

	private function __construct($type) {
		$this->type = $type;
		switch($this->type) {
			case self::IP:
				$this->auth = new Auth_IP();
				break;
			default:
			case self::HTTP_BASIC:
				$this->auth = new Auth_HttpBasic();
				break;
			case self::OAUTH:
				$this->auth = new Auth_OAUTH();
				break;
			case self::FB_AUTH:
				$this->auth = new Auth_FBAuth();
				break;
			case self::KT_AUTH:
				$this->auth = new Auth_KTAuth();
				break;
		}
	}

	public static function getInstance($type = null) {
		if(isset(self::$instance) && !is_null($type) && self::$instance->type !== $type) {
			self::$instance = null;
		}
		if (!isset(self::$instance)) {
			if(is_null($type)) {
				throw new Exception('Invalid type', 500);
			}
            $c = __CLASS__;
            self::$instance = new $c($type);
        }
        return self::$instance;
	}

	public function check( ) {
		$this->identity = $this->auth->check( );
		if(false === $this->identity) {
			return false;
		}
		return true;
	}

	public function getIdentity() {
		return $this->identity;
	}
}
?>
