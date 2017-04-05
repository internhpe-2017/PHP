<?php


class Controllers_Hello extends Controllers_Abstract {
	const IS_PUBLIC =1;
 	public function validate(  )
	{
  		//$this->validation = new Library_Validation();	 
		//$this->validation->add_fields($this->request['params']['term'], 'req', 'Please Enter term',0);
		//$request['pd_cntry'] $request['uid_cntry']) $request['plk_cntry']  $request['py_cntry']  $request['uid_cntry'] $request['pc_cntry'] 
  		return $this->validation->validate();
 	}

 
	public function isPublic() {
		return self::IS_PUBLIC;
	}

	public function get() {
 		
		//$cntry= new Services_Hello();
			$this->response ="{'checking controller':'ok','welcome message':'hello'}";
  		$this->responseStatus = 200;
	}



	public function post() {
		$cntry= new Services_Hello();
  		$this->responseStatus = 200;
	}
	// @codeCoverageIgnoreStart
	public function put() {
		return null;
	}

	public function delete() {
		return null;
	}
	// @codeCoverageIgnoreEnd
}
?>
