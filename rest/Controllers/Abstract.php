<?php


abstract class Controllers_Abstract {
	protected $request;
	protected $response;
	protected $contType;
	protected $responseStatus;
	protected $error;
	protected $validation;


	public function __construct($request) {
		$this->request = $request;
		
	}

	final public function getResponseStatus() {
		return $this->responseStatus;
	}

	final public function getResponse() {
		return $this->response;
	}

	final public function setcontType($contType) {
		 $this->contType=$contType;
	}

	final public function getcontType( ) {
		return  $this->contType ;
	}

	// @codeCoverageIgnoreStart
	abstract public function isPublic();
	abstract public function get();
	abstract public function post();
	abstract public function put();
	abstract public function delete();
	abstract public function validate();
	// @codeCoverageIgnoreEnd

}
?>
