<?php 

class OAuthServer
{
	private $version = '1.0';
	private $signatureMethods = array();
	private $dataStore;
	private $timeThreshold = 500;
	
	public function __construct(OAuthDataStore $dataStore)
	{
		$this->dataStore = $dataStore;
	}
	
	public function addSignatureMethods(OAuthSignature $signatureMethod)
	{
		$this->signatureMethods[$signatureMethod->getName()] = $signatureMethod;
	}
	
	public function fetchRequestToken(OAuthRequest $request)
	{
		try
		{
			$this->getVersion($request);
			$consumer = $this->getConsumer($request);
			$token = null;
			$this->checkSignature($request, $consumer, $token);
			return $this->dataStore->newRequestToken($consumer);
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	
	public function fetchAccessToken(OAuthRequest $request)
	{
		try
		{
			$this->getVersion($request);
			$consumer = $this->getConsumer($request);
			//$token = $this->getToken($request, $consumer, 'request');
			$token = null;
			$this->checkSignature($request, $consumer, $token);
			return $this->dataStore->newAccessToken($consumer);
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage);
		}
	}
	
	public function getToken(OAuthRequest $request, OAuthConsumer $consumer, $tokenType = 'access')
	{
		$tokenField = $request->getParameters('oauth_token');
		$token = $this->dataStore->lookupToken($consumer, $tokenType, $tokenField);
		if(!$token)
		{
			throw new Exception('Invalid Token', 401);
		}
		return $token;
	}
	
	public function checkSignature(OAuthRequest $request, OAuthConsumer $consumer, $token)
	{
		$timestamp = $request->getParameters('oauth_timestamp');
		$nonce = $request->getParameters('oauth_nonce');
		
		try
		{
			$this->checkTimestamp($timestamp);
			$this->checkNonce($consumer, $token, $nonce, $timestamp);
			
			$signatureMethod = $this->getSignatureMethod($request);
			
			$signature = $request->getParameters('oauth_signature');
			$validSig = $signatureMethod->checkSignature($request, $consumer, $token, $signature);
		}
		catch(Exception $e)
		{
			throw new Excepton($e->getMessage());
		}
	}
	
	public function getSignatureMethod(OAuthRequest $request)
	{
		$signatureMethod = $request->getParameters('oauth_signature_method');
		if(!in_array($signatureMethod, array_keys($this->signatureMethods)))
		{
			throw new Exception('Signature method not supported', 401);
		}
		return $this->signatureMethods[$signatureMethod];
	}
	
	public function checkNonce($consumer, $token, $nonce, $timestamp)
	{
		$found = $this->dataStore->lookupNonce($consumer, $token, $nonce, $timestamp);
		if($found)
		{
			throw new Exception('Invalid nonce.', 401);
		}
	}
	
	public function checkTimestamp($timestamp)
	{
		$now = time();
		if(($now - $timestamp) > $this->timeThreshold)
		{
			throw new Exception('Expired timestamp', 401);
		}
	}
	
	public function getConsumer(OAuthRequest $request)
	{
		$consumerKey = $request->getParameters('oauth_consumer_key');
		if(empty($consumerKey))
		{
			throw new Exception('Invalid consumer key', 401);
		}
		
		$consumer = $this->dataStore->lookupConsumer($consumerKey);
		if(!$consumer)
		{
			throw new Exception('Invalid consumer', 401);
		}
		return $consumer;	
	}
	
	public function getVersion(OAuthRequest $request)
	{
		$version = $request->getParameters('oauth_version');
		if(!version)
		{
			$version = 1.0;
		}
		if($version && $version != $this->version)
		{
			throw new Exception('OAuth version not supported', 401);
		}
		return $version;
	}
}   

class OAuthRequest
{
	private $parameters;
	private $httpMethod;
	private $httpUrl;
	
	public function __construct($parameters, $httpMethod, $httpUrl)
	{
		$this->parameters = $parameters;
		$this->httpMethod = $httpMethod;
		$this->httpUrl = $httpUrl;
	}
	
	public static function buildRequest($httpMethod = null, $httpUrl = null, $requestHeaders = null, $parameters = null)
	{
		$scheme = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
	    @$httpUrl or $httpUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	@$httpMethod or $httpMethod = $_SERVER['REQUEST_METHOD'];
		@$requestHeaders or $requestHeaders = $this->getHeaders();
		
		if($parameters)
		{
			$req = new OAuthRequest($parameters, $httpMethod, $httpUrl);
		}
		else 
		{
			if(@substr($requestHeaders['Authorization'], 0, 5) == 'OAuth')
			{
				$headers = self::splitHeaders($requestHeaders['Authorization']);
			}
			$parameters = array();
			if($_GET)
			{
				$parameters = array_merge($parameters, $_GET);
			}
			if($_POST)
			{
				$parameters = array_merge($parameters, $_POST);
			}
			if(isset($headers) && !empty($headers))
			{
				$parameters = array_merge($headers, $parameters);
			}
			$req = new OAuthRequest($parameters, $httpMethod, $httpUrl);
		}
		return $req;
	}
	
	private static function splitHeaders($string)
	{
		$parts = explode(",", $string);
	    $out = array();
	    foreach ($parts as $param) {
	      $param = ltrim($param);
	      // skip the "realm" param, nobody ever uses it anyway
	      if (substr($param, 0, 5) != "oauth") continue;
	
	      $param_parts = explode("=", $param);
	
	      // rawurldecode() used because urldecode() will turn a "+" in the
	      // value into a space
	      $out[$param_parts[0]] = rawurldecode(substr($param_parts[1], 1, -1));
	    }
	    return $out;
	}
	
	public function getSignatureBaseString()
	{
		$parts = array($this->getNormalizedHttpMethod(),
					$this->getNormalizedHttpUrl(),
					$this->getSignableParameters());
					
		$parts = array_map(array('OAuthUtils', 'urlencodeRFC3986'), $parts);
		
		return implode('&', $parts);
	}
	
	private function getSignableParameters()
	{
		$url = parse_url($this->httpUrl);
		if(array_key_exists('query', $url))
		{
			parse_str($url['query'], $query);
			foreach($query as $key => $value)
			{
				$this->setParameters($key, $value);
			}
		}
			
		$params = $this->parameters;
			
		if(isset($params['oauth_signature']))
		{
			unset($params['oauth_signature']);
		}
			
		$keys = array_map(array('OAuthUtils', 'urlencodeRFC3986'), array_keys($params));
		$values = array_map(array('OAuthUtils', 'urlencodeRFC3986'), array_values($params));
		$params = array();
		for($i = 0; $i < count($keys); $i++)
		{
			$params[$keys[$i]] = $values[$i];
		}
		
		uksort($params, 'strnatcmp');
			
		$pairs = array();
		foreach($params as $key => $value)
		{
			if(is_array($value))
			{
				natsort($value);
				foreach($values as $v2)
				{
					$pairs[] = $key . '=' . $v2;
				}
			}
			else
			{
				$pairs[] = $key . '=' . $value; 
			}
		}
		return implode('&', $pairs);
	}
	
	private function getNormalizedHttpMethod()
	{
		return strtoupper($this->httpMethod);
	}
	
	private function getNormalizedHttpUrl()
	{
		$parts = parse_url($this->httpUrl);
		
		if($parts['scheme'] == 'http')
		{
			$port = (isset($parts['port']) && $parts['port'] != '80') ? ':' . $parts['port'] : '';
		} 
		elseif($parts['scheme'] == 'https')
		{
			$port = (isset($parts['port']) && $parts['port'] != '443') ? ':' . $part['port'] : '';
		}
		$parts['host'] = strtolower($parts['host']);
		$path = (isset($parts['path'])) ? $parts['path'] : '';
		
		return $parts['scheme'] . '://' . $parts['host'] . $port . $path;
	}
	
	public function setParameters($name, $value)
	{
		$this->parameters[$name] = $value;
	}
	
	public function getParameters($name = null)
	{
		if(is_null($name))
		{
			return $this->parameters;
		}
		else
		{
			return $this->parameters[$name];
		}
	}
	
	private function getHeaders()
	{
		if(function_exists('apache_request_headers'))
		{
			return apache_request_headers();
		}
		
		$headers = array();
		foreach($_SERVER as $key => $value)
		{
			if(substr($key, 0, 5) == 'HTTP_')
			{
				$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
				$headers[$key] = $value;
			}			
		}
		return $headers;
	}
}

class OAuthDataStore
{
	
}

class MySQL_OAuthDataStore extends OAuthDataStore
{
	private $dbLink;
	
	public function __construct($dbServer, $dbName, $dbUser, $dbPass)
	{
		if($this->dbLink = mysql_connect($dbServer, $dbUser, $dbPass, true))
		{
			if(mysql_select_db($dbName, $this->dbLink))
			{
				
			} 
			else 
			{
				throw new Exception(mysql_error());
			}
		}
		else
		{
			throw new Exception(mysql_error());
		}
	}
	
	public function lookupConsumer($consumerKey)
	{
		$sql = "select consumer_key, consumer_secret from theid_consumers where consumer_key = '" . mysql_real_escape_string($consumerKey) . "'";
		$result = mysql_query($sql, $this->dbLink);
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_assoc($result);
			return new OAuthConsumer($row['consumer_key'], $row['consumer_secret']);
		}
		else
		{
			throw new Exception('Consumer not found');
		}
	}
	
	public function lookupToken(OAuthConsumer $consumer, $tokenType, $tokenField)
	{
		$sql = "select token, token_secret, token_creation, token_ttl, token_expiry from theid_tokens where token = '" . $tokenField .  "' and token_type = '" . tokenType . "'";
		$result = mysql_query($sql, $this->dbLink);
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_assoc($result);
			return new OAuthToken($row['token'], $row['token_secret'], $row['token_creation'], $row['token_ttl'], $row['token_expiry']);
		}
		else
		{
			throw new Exception('Token not found');
		}
	}
	
	public function lookupNonce($consumer, $token, $nonce, $timestamp)
	{
		$sql = "select nonce from theid_nonce where nonce = '" . $nonce . "'";
		$result = mysql_query($sql, $this->dbLink);
		if(mysql_num_rows($result) == 0)
		{
			$sql1 = "insert into theid_nonce ( nonce, nonce_time ) values ('" . mysql_real_escape_string($nonce) . "', " . time() . ")";
			mysql_query($sql1, $this->dbLink);
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function newToken(OAuthConsumer $consumer, $tokenType)
	{
		$token = md5(sha1(sha1(mt_rand() * time(), true), true)); 
		$tokenSecret = md5(sha1(sha1(cos(mt_rand()) / tan(time()), true), true));		
		$tokenCreation = time();
		$tokenExpiry = strtotime("+5 hour");
		$tokenTtl = $tokenExpiry - $tokenCreation;
		
		$sql = "insert into theid_tokens (token, token_secret, token_creation, token_expiry, token_type) values ('" .
				mysql_real_escape_string($token) . "', '" . mysql_real_escape_string($tokenSecret) . "', " .
				$tokenCreation . ", " . $tokenExpiry . ", '" . mysql_real_escape_string($tokenType) . "')";
		mysql_query($sql, $this->dbLink);
		
		return new OAuthToken($token, $tokenSecret, $tokenCreation, $tokenTtl, $tokenExpiry);
	}
	
	public function newRequestToken(OAuthConsumer $consumer)
	{
		return $this->newToken($consumer, 'request');		
	}
	
	public function newAccessToken(OAuthConsumer $consumer)
	{
		return $this->newToken($consumer, 'access');		
	}
	
	public function __destruct()
	{
		if($this->dbLink)
			mysql_close($this->dbLink);
	}
}

class OAuthConsumer
{
	public $key;
	public $secret;
	
	public function __construct($key, $secret)
	{
		$this->key = $key;
		$this->secret = $secret;
	}
}

class OAuthToken
{
	public $key;
	public $secret;
	public $creation;
	public $ttl;
	public $expiry;
	
	public function __construct($key, $secret, $creation, $ttl, $expiry)
	{
		$this->key = $key;
		$this->secret = $secret;
		$this->creation = $creation;
		$this->ttl = $ttl;
		$this->expiry = $expiry;
	}	
}

class OAuthUtils
{
	public static function urlencodeRFC3986($str)
	{
		return str_replace('%7E', '~', rawurlencode($str));
	}
	
	public static function urldecodeRFC3986($str)
	{
		return rawurldecode($str);
	}
}

class OAuthSignature
{
	public function checkSignature(OAuthRequest $request, $consumer, $token, $signature)
	{
		$built = $this->buildSignature($request, $consumer, $token);
		return $build == $signature;
	}
}

class OAuthSignature_HMAC_SHA1 extends OAuthSignature
{
	public function getName()
	{
		return 'HMAC-SHA1';
	}
	
	public function buildSignature(OAuthRequest $request, OAuthConsumer $consumer, $token)
	{
		$signatureBaseString = $request->getSignatureBaseString();
		
		$keyParts = array($consumer->secret, ($token) ? $token->secret : '');
		$keyParts = array_map(array('OAuthUtils', 'urlencodeRFC3986'), $keyParts);
		$key = implode('&', $keyParts);
		
		return base64_encode(hash_hmac('sha1', $signatureBaseString, $key, true));
	}
}

if (!function_exists("hash_hmac")) {
    function hash_hmac($algo, $data, $key) {
        if ($algo != 'sha1') {
            throw new Exception('hash_hmac() only expects sha1.');
        }

        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key)>$blocksize)
            $key = pack('H*', $hashfunc($key));
        $key = str_pad($key,$blocksize,chr(0x00));
        $ipad = str_repeat(chr(0x36),$blocksize);
        $opad = str_repeat(chr(0x5c),$blocksize);
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
?>