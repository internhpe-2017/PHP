<?php
/** OAuth* classes */
require_once 'OAuth.php';
class Auth_SampleOauth
{

	private $_config  = array("BaseUrl"=>"http://localhost","TokenCreateUrl"=>"http://localhost/rest/login","XMLContent"=>"test","ConnectTimeout"=>"100","httpTimeout"=>"30","httpProxy"=>"" ,"Realm"=>"testrealm");

 

	public function doLogin($username, $password) 
	{
			 
$xmlString=<<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<tokenCreationRequest xmlns="">
</tokenCreationRequest>
XML;
 
//print_r("req".$xmlString);

			if(empty($username))
			throw new Exception("Username Required");

			if(empty($password))
			throw new Exception("Password Required");

			
			//create simplexml object
			$xml = simplexml_load_string($xmlString);
			
			//print_r($xml);
			
			$nonce = md5(microtime() . mt_rand()); //generate new nonce (different from request Authorization)
			$timestamp = time(); //generate timestamp
			//add more elements into xml
			$resourceId = '/rest/login/' . $username . '/';			
			//$digestAuthNode->addChild('username', $username);
			//$digestAuthNode->addChild('nonce', $nonce);
			//$digestAuthNode->addChild('created', $timestamp);			

			//make the request
			$responseXml = $this->requestTokenCreate($xml);			
			print_r(responseXml);
			$this->setToken(new OAuthToken((string) $responseXml->tokenInfo->token, (string) $responseXml->tokenInfo->tokenSecret)); 
			return $responseXml;
	}

	/**
	 * Request new token creation with $username and $password.
	 * @access Private
	 * @param SimpleXMLObject $xml
	 */
	private function requestTokenCreate($xml)
	{
		return $this->signedRequest($this->_config['BaseUrl'] . $this->_config['TokenCreateUrl'],'POST',array('XMLContent' => $xml->asXml()));
	}		
		
		
	protected function signedRequest($url, $method = 'GET', $parameters = null, $headers = array()) {

		/*$request = OAuthRequest::from_consumer_and_token(
		$this->getConsumerService(),
		$this->getToken(),
		$method,
		$url
		);*/

		 $request = OAuthRequest::from_request(
		$method,
		$url,null); 
		
		//print_r($request->get_normalized_http_url());

		if ($parameters && is_array($parameters)) {
			foreach ($parameters as $key => $value) {
				$request->set_parameter($key, $value);
			}
		}

		// sign request with service + token secrets
		/*$request->sign_request(
		new OAuthSignatureMethod_HMAC_SHA1(),
		$this->getConsumerService(),
		$this->getToken()
		);
		*/
		

		 $request->sign_request(
		new OAuthSignatureMethod_HMAC_SHA1(),
		'ConnectDays',
		'Token'
		);
		 

		// get user info from
		/*$responseString = $this->requestUrl(
		$request->get_normalized_http_url(),
		array_merge(array($request->to_header($this->_config['Realm'])), $headers),
		$request->get_normalized_http_method(),
		$parameters
		);*/
		
		$responseString = $this->requestUrl(
				$request->get_normalized_http_url(),
				array_merge(array($request->to_header($this->_config['Realm'])), $headers),
				$request->get_normalized_http_method(),
				$parameters
		);



		// parse response xml
		return $this->_parseStringToXml($responseString);
	}

	/**
	 * Make the actual HTTP request
	 * @param string $url
	 * @param array $headers
	 * @throws Exception
	 * @return string HTTP response body
	 */
	public function requestUrl($url, $headers = null, $httpMethod = 'GET', $parameters = null) {

		print_r($url);
		// initialize curl handler
		$ch = curl_init($url);			
		// check if proxy is needed for http requests
		if (isset($this->_config['httpProxy'])) {
			$proxyConfig = explode(':', $this->_config['httpProxy']);				
			curl_setopt($ch, CURLOPT_PROXY, $proxyConfig[0]);
			if (isset($proxyConfig[1]))
				curl_setopt($ch, CURLOPT_PROXYPORT, $proxyConfig[1]);

		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // yes - we need the response body
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ignore SSL certificate 
		curl_setopt($ch, CURLOPT_HEADER, 0); // no need for headers
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_config['ConnectTimeout']);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->_config['httpTimeout']);


		// set headers if available
		if (is_array($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		// set method
		switch ($httpMethod) {
			case 'POST':					
				curl_setopt($ch, CURLOPT_POST, 1);
				if ($parameters && is_array($parameters)) {
					$fields_string = '';
					foreach ($parameters as $key => $value) {														
						$fields_string .= urlencode($key).'='.urlencode($value).'&';																			
					}					
					$fields_string = substr($fields_string, 0, strlen($fields_string)-1);										
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
					//curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($key) . '=' . urlencode($value));	
					
					
				}					
				break;//add put and delete if needed
			case 'GET':
			default:
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
		}
		// execute curl request to get response				
		$response = curl_exec($ch);
		

		// validate response
		if ($response === false) {	
		 
			error_log("curl error #" . curl_errno($ch) . " found: " . curl_error($ch));
			throw new Exception("Error connecting server!", 503);
		} else {
		   
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);	 //get response code from last request	
			print_r($responseCode);
			print_r($response);
			/*if (intval(substr($responseCode, 0, 1)) !== 2)  //check if request was succesfull or not?
			{
				throw new Exception($response, $responseCode, $response); // Throws Exceptions 
			}
			*/
		}
		// close curl handler
		curl_close($ch);
		// return response string
		return $response;
	}

	/**
	 * Parse string into XML object.
	 * @return SimpleXMLElement
	 */
	protected function _parseStringToXml($string)
	{
		$o = null;
		try {
			$o = simplexml_load_string($string);
		} catch (Exception $e) {}
		return $o;
	}
		
}