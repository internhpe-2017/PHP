<?php 

define('OAUTH_PARAM', 'HEADER');
define('SIGNATURE_METHOD', 'HMAC-SHA1');
define('SIGNATURE_CLASS', 'HMAC_SHA1');

class OAuthClient
{
	protected $REST_VERBS = array('GET', 'POST', 'PUT', 'DELETE');
	
	protected $baseUrl;
	protected $consumerKey;
	protected $consumerSecret;
	protected $realm;
	protected $proxy;
	protected $proxyUserPass;
	protected $language;
	protected $oAuthParams;
	
	const OAUTH_VERSION = '1.0';
	const HTTP_OK = '200';
	const HTTP_CREATED = '201';
	const HTTP_PRECON_FAILED = '412';	
			
	protected $lastStatus = -1;
	protected $lastContentType = null;
	protected $lastCallTime = 0;
	protected $lastResponse = null;
	protected $lastContentString = null;
	
	public function __construct($baseUrl, $consumerKey, $consumerSecret, 
		$realm, $proxy, $proxyUserPass)
	{		
		$this->baseUrl = $baseUrl;	
		$this->consumerKey = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->realm = $realm;
		$this->proxy = $proxy;
		$this->proxyUserPass = $proxyUserPass;
		$this->setAccessToken(null, null); 			
	}
	
	public function setAccessToken($accessTokenKey, $accessTokenSecret)
	{
		$this->accessTokenKey = $accessTokenKey;
	  	if(is_null($accessTokenSecret)) {
	  		$accessTokenSecret = '';  	
	  	}
	  	$this->accessTokenSecret = $accessTokenSecret;
	}
	
	public function restCall($restVerb, $requestUrl, $parameters, $addAccessToken)
	{
		if (!in_array($restVerb, $this->REST_VERBS))
		{
			throw new Exception(__CLASS__  . ' : ' . __FUNCTION__ . ': HTTP Method ' . $restVerb . ' not supported.');
		}

		if (substr($requestUrl, 0, 1) == '/')
		{
			$fullUrl = $this->baseUrl . $requestUrl;	
		}
		else
		{
			$fullUrl = $this->baseUrl . '/' . $requestUrl;
		}
		
		$this->setOAuthParams($restVerb, $fullUrl, $parameters, $addAccessToken);
		
		switch (OAUTH_PARAM)
		{
			default:
			case 'HEADER':
				$this->OAuthParamsInHeader($restVerb, $fullUrl, $this->oAuthParams, $parameters);
				break;
			case 'QUERYSTRING':
				$this->OAuthParamsInQueryString($restVerb, $fullUrl, $this->oAuthParams, $parameters);
				break;
			case 'BODY':
				$this->OAuthParamsInBody($restVerb, $fullUrl, $this->oAuthParams, $parameters);
				break;
		}							
	}
	
	private function OAuthParamsInHeader($restVerb, $fullUrl, $oAuthParams, $parameters)
	{
		$header = 'OAuth realm="http://localhost"';
		foreach($oAuthParams as $k => $v) {
	  		$header .= ',' . $k . '="' . $v . '"';
	  	}
		
		$queryString = null;
		if (!empty($parameters))
		{
			$queryString = http_build_query($parameters);
		}
		
		if ($restVerb == 'GET' && !is_null($queryString))
		{
			$fullUrl .= '?' . $queryString;
		}
		
		$headers = array('Authorization: ' . $header);
		
		$this->sendRequest($restVerb, $fullUrl, $headers, $queryString);
	}
	
	private function OAuthParamsInQueryString($restVerb, $fullUrl, $oAuthParams, $parameters)
	{
		$queryString = null;
		if (!empty($parameters))
		{
			$queryString = http_build_query($parameters);
		}		
		
		$headers = array();
		$oAuthParams = http_build_query($oAuthParams);		
		
		if ($restVerb == 'GET' && !is_null($queryString))
		{
			$fullUrl .= '?' . $queryString . '&' . $oAuthParams;
			$this->sendRequest($restVerb, $fullUrl, $headers, null);
		}
		else
		{
			$fullUrl .= '?' . $oAuthParams; 
			$this->sendRequest($restVerb, $fullUrl, $headers, $queryString);
		}	
	}
	
	private function OAuthParamsInBody($restVerb, $fullUrl, $oAuthParams, $parameters)
	{
		$queryString = null;
		if (!empty($parameters))
		{
			$queryString = http_build_query($parameters);
		}		
		
		$headers = array();
		$oAuthParams = http_build_query($oAuthParams);
		
		if ($restVerb == 'GET' && !is_null($queryString))
		{
			$fullUrl .= '?' . $queryString;		
			$this->sendRequest($restVerb, $fullUrl, $headers, $oAuthParams);
		} 
		else
		{
			$body = $oAuthParams . '&' . $queryString;
			$this->sendRequest($restVerb, $fullUrl, $headers, $body);
		}		

	}
	
	private function sendRequest($requestMethod, $url, $headers, $body)
	{
	    //  echo  $requestMethod.$url;
		if (!extension_loaded('curl'))
		{
			throw new Exception(__CLASS__  . ':' . __FUNCTION__ . ' Please install curl extension before using this SDK.');
		}
		
		if (!empty($this->language))
		{
			array_push($headers, 'Accept-Language: ' . $this->language);
		}
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestMethod);
  		curl_setopt($ch, CURLOPT_HEADER, false);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
  		curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
  		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyUserPass);
	  	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    	//curl_setopt($ch, CURLOPT_CAINFO, SDK_ROOT . 'config/cacert.pem');
  		if (($requestMethod == 'POST') || (OAUTH_PARAM == 'BODY') || ($requestMethod == 'PUT')) 
  		{
  			array_push($headers, 'Content-Type: application/x-www-form-urlencoded');
  			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  		}
  		else
  		{
  			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  		}
  		
  		$this->lastCallTime = time();  		  		
	  	$result = curl_exec($ch);	  	
	  		  	
	  	$callInfo = curl_getinfo($ch);

  		$this->lastStatus = $callInfo['http_code'];
  		if (isset($callInfo['content_type']))
  		{  		
  			$this->lastContentType = $callInfo['content_type'];
  		}  		
  		$this->lastResponse = $result;

  		if ($result === false) 
  		{
  			throw new Exception(__CLASS__  . ':' . __FUNCTION__ . curl_error($ch));
  		}  		
  		  		
	  	curl_close($ch);
	}
	
	private function setOAuthParams($restVerb, $fullUrl, $parameters, $addAccessToken) 
	{
	  	$this->oAuthParams = array();	  	
	  	$this->oAuthParams['oauth_consumer_key'] = OAuthUtils::encodeParam($this->consumerKey);	  	
	  	$this->oAuthParams['oauth_nonce'] = OAuthUtils::encodeParam($this->getNonce());	  	
	  	if($this->accessTokenKey != null && $addAccessToken === true)
	  		$this->oAuthParams['oauth_token'] = OAuthUtils::encodeParam($this->accessTokenKey);
	  	$this->oAuthParams['oauth_timestamp'] = OAuthUtils::encodeParam($this->getTimestamp());	  	
	  	$this->oAuthParams['oauth_signature_method'] = SIGNATURE_METHOD;	  	
	  	$this->oAuthParams['oauth_version'] = self::OAUTH_VERSION;
	  	
	  	$base = OAuthUtils::createSignatureBaseString($restVerb, $fullUrl, $this->oAuthParams, $parameters);
	  	
	  	$key = OAuthUtils::encodeParam($this->consumerSecret) . '&';

	  	if(!is_null($this->accessTokenKey) && $addAccessToken === true)
	  		$key .= OAuthUtils::encodeParam($this->accessTokenSecret);

	  	$signature = OAuthUtils::createSignature($base, $key); 
	  		  	
	  	$this->oAuthParams['oauth_signature'] = $signature;	   
  	}
	
	public function getOAuthParams() 
	{
		return $this->oAuthParams;		
	}
	
	public function getNonce()
	{
		return sha1(mt_rand() . time());
	}
	
	public function getTimestamp()
	{
		return time();
	}
	
	public function setLanguage($lang)
	{
		$this->language = $lang;
	}
	
	public function getLastStatus()
	{
		return $this->lastStatus;
	}
	
	public function getLastResponse()
	{
		return $this->lastResponse;
	}
	
	public function getLastContentType()
	{
		return $this->lastContentType;
	}
}

class OAuthUtils
{
	public static function createSignatureBaseString($httpMethod, $url, $authHeaders, $parameters)
	{
		if (!is_array($authHeaders) || empty($authHeaders))
		{
			throw new Exception(__CLASS__  . ':' . __FUNCTION__ . ' Empty Authorization headers array');	
		}
		
		//1. Convert HTTP method (GET, POST, PUT, DELETE) to uppercase
		$httpMethod = strtoupper($httpMethod);
		
		//2. Normalize Url
		try 
		{
			$urlString = self::encodeParam(self::normalizeUrl($url));
			
			//3. Sort the parameters by key first, if equal then by value
			uksort($authHeaders, "strnatcasecmp");			
			
			$requestParameters = array();
			if (is_array($parameters) && !empty($parameters)) 
			{
			  	foreach($parameters as $k => $v) {
			  		$requestParameters[self::encodeParam($k)] = self::encodeParam($v);  		  	
			  	}
			}
		  	$requestParameters = array_merge($requestParameters, $authHeaders);
		  	
		  	$paramArray = array();
		  	foreach($requestParameters as $k => $v) {
		  		$paramArray[] = $k . '=' . $v;
		  	}
		  	$paramString = self::encodeParam(implode('&', $paramArray));
		  	
		  	$returnUrl = $httpMethod . '&' . $urlString . '&' . $paramString;
		  	
		  	return $returnUrl;
		}
		catch (Exception $ne)
		{
			throw new Exception($ne->getMessage());
		}
	}
	
	public static function createSignature($base, $key)
	{
		$signatureClass = 'OAuthSignature_' . SIGNATURE_CLASS;
		$signature = new $signatureClass();
		return $signature->createSignature($base, $key);				
	}
	
	private static function normalizeUrl($url) {
	  	$urlArray = parse_url($url);
	  	if(array_key_exists('scheme', $urlArray) === false) 
	  	{
	  		throw new Exception(__CLASS__  . ':' . __FUNCTION__ . ' Error : Missing scheme in URL');
	  	}
	  	$urlString = strtolower($urlArray['scheme']) . '://';
		if(array_key_exists('host', $urlArray) === false) 
	  	{
	  		throw new Exception(__CLASS__  . ':' . __FUNCTION__ . ' Error : Missing host in URL');
	  	}
	  	$urlString .= strtolower($urlArray['host']);
	  	if(array_key_exists('port', $urlArray) && !is_null($urlArray['port']) && 
	  		(($urlArray['port'] != 80) || ($urlArray['port'] != 443))) {
	  		$urlString .= ':' . $urlArray['port'];  	
	  	}
	  	$urlString .=  $urlArray['path'];
	  	return $urlString;
	}

	public static function encodeParam($string) {
	  	return str_replace('%7E', '~', rawurlencode($string));
	}
}

interface OAuthSignature
{
	public function createSignature($base, $key);
}

class OAuthSignature_HMAC_SHA1 implements OAuthSignature
{
	/**
	 * Method to create HMAC-SHA1 OAuth signature
	 * @access private
	 * @param $base Signature base string
	 * @param $key Key for generating signature
	 * @return OAuth signature
	 */
	public function createSignature($base, $key)
	{
		if (function_exists('hash_hmac'))
		{
			return base64_encode(hash_hmac('sha1', $base, $key, true));
		}
		else
		{
			return base64_encode($this->hmacsha1('sha1', $base, $key));
		}
	}
	
	private function hmacsha1($hashfunc, $data, $key)
    {
	    $blocksize=64;
	    $hashfunc='sha1';
	    if (strlen($key)>$blocksize)
	        $key=pack('H*', $hashfunc($key));
	    $key=str_pad($key,$blocksize,chr(0x00));
	    $ipad=str_repeat(chr(0x36),$blocksize);
	    $opad=str_repeat(chr(0x5c),$blocksize);
	    $hmac = pack(
	                'H*',$hashfunc(
	                    ($key^$opad).pack(
	                        'H*',$hashfunc(
	                            ($key^$ipad).$data
	                        )
	                    )
	                )
	            );
	    return $hmac;
    }
}

class OAuthSignature_PLAINTEXT implements OAuthSignature
{
	public function createSignature($base, $key)
	{
		return $key;
	}
}

class OAuthSignature_RSA_SHA1 implements OAuthSignature
{
	public function createSignature($base, $key)
	{
		return false;
	}
}
?>