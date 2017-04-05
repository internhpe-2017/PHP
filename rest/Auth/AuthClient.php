<?php
/**
 */
class AuthClient {
  public $baseuri;
  public $username;
  public $password;
  
  public function __construct($server, $port = null, $username = null, $password = null) {
    $this->baseuri = $server;
    if(!empty($port) && is_int($port)) {
      $this->baseuri .= ':' . $port;
    }
    $this->username = $username;
    $this->password = $password;
  }
  
  public function doRequest($method, $uri, $querystring = null, $body = null, $headers = null) {
	// create request url
	if(!is_null($querystring)) {
		$uri .= '?' . $querystring;
	}

	// prepare headers
	if(!is_array($headers) && empty($headers)) {
		$headers = array();
	}
	if($this->username && $this->password) {
		//print_r('userpass'.$this->username.$this->password);
		//print_r(base64_encode( $this->password));
		$headers[] = 'Authorization: Basic '.base64_encode($this->username.':'.$this->password);
	}    

	// Make a curl call

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $this->baseuri.$uri);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);    
	curl_setopt($ch, CURLOPT_FAILONERROR, false);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
	//curl_setopt($ch, CURLOPT_PROXY, '192.168.1.1:8090');

	if($method == 'POST' || $method == 'PUT') {
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		$headers[] = 'Expect: ';
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

	}


	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// store the response
	$response = curl_exec($ch);    
	
	//print_r(''.$response );

	// In case of error
	if(false === $response) {
		$response = curl_error($ch);
	}
	//$data=json_encode($response);
	 
	// get the http response status
	$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   
 	curl_close($ch); 
	  //return array('result' => $response, 'status' => "success");
         

	if($response_code==200)
	{
		
		 $val= json_decode($response,true);
		 
		 // return $val['active'];
		
		if($val["active"]=="1")
		{
 			//error_log(serialize($_SESSION['username']));
			return array('result' => $val, 'status' => "inactive");
 		}
		else if($val["active"]=="3")
		{
 			//error_log(serialize($_SESSION['username']));
			return array('result' => $val, 'status' => "notfound");
 		}
   		else if($val["active"]=="0")
		{
			$_SESSION['userinfo'] =  $val;
			//error_log(serialize($_SESSION['username']));
			
			return array('result' =>  $val, 'status' => "success");
		}
	}
	else 	
	{
		return array('result' =>  $val, 'status' => "error");
	}
	 //print_r($_SESSION['username']['name']);
	
        //header("Location:{$site_http_path}home.php");
   }  
}

?>