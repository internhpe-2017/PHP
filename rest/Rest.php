<?php
/*
 * Copyright 2011 <http://voidweb.com>.
 * Author: Deepesh Malviya <https://github.com/deepeshmalviya>.
 *
 * Simple-REST - Lightweight PHP REST Library
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/**
 * Class implements RESTfulness
 */
 error_reporting(0);
class Rest extends RestBase{

 	//const DEFAULT_RESPONSE_FORMAT = 'json'; // Default response format

	/**
	 * Constructor
	 * calls processRequest internally
	 */
	public function __construct() {
		$this->processRequest();
	}

	/**
	 * Function processing raw HTTP request headers & body
	 * and populates them to class variables.
	 */
	private function processRequest() {
		$this->request['resource'] = (isset($_GET['RESTurl']) && !empty($_GET['RESTurl'])) ? $_GET['RESTurl'] : 'index';
		unset($_GET['RESTurl']);
echo $_GET['RESTurl'];
		$this->request['method'] = strtolower($_SERVER['REQUEST_METHOD']);
		$this->request['headers'] = $this->getHeaders();
		$this->request['format'] = isset($_GET['format']) ? trim($_GET['format']) : null;
		switch($this->request['method']) {
			case 'get':
				$this->request['params'] = $_GET;
				break;
			case 'post':
				$this->request['params'] = array_merge($_POST, $_GET);
				break;
			case 'put':
				parse_str(file_get_contents('php://input'), $this->request['params']);
            			break;
			case 'delete':
				$this->request['params'] = $_GET;
				break;
			default:
				break;
		}
		
		$this->request['content-type'] = $this->getResponseFormat($this->request['format']);
		
		if(!function_exists('trim_value')) {
			function trim_value(&$value) {
				$value = trim($value);
			}
		}
		array_walk_recursive($this->request, 'trim_value');
	}

	/**
	 * Function to resolve controller based on the resource name and http
	 * method (GET/POST/PUT/DELETE) using reflection and get the response.
	 * Passes the response to the response helpers class.
	 */
	public function process() {
		try	{
			$controllerName = $this->getController();

			if(null == $controllerName) {
				throw new Exception('Method not allowed', 405);
			}
			$controller = new ReflectionClass($controllerName);
			if(!$controller->isInstantiable()) {
				throw new Exception('Bad Request', 400);
			}
			try {
				$method = $controller->getMethod($this->request['method']);
			} catch(ReflectionException $re) {
				throw new Exception('Unsupported HTTP method ' . $this->request['method'], 405);
			}
			if(!$method->isStatic()) {
				$controller = $controller->newInstance($this->request);
				if(!$controller->isPublic()) {
				
 				 
					
					if( !isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn']!="isLoggedIn")
					{
						try {
							parent::checkAuth();
						} catch(Exception $re) {
 							  $this->response = array("code"=>"406","msg"=>"Not Authorised To Access");
 							// $this->response = '{"code":"406","msg":"message"}';
 							// $this->response = '{"code":"406","msg":"message"}';
							 $this->responseStatus = '406';
						}
					}
				   	
 
				}
				if($this->responseStatus!='406')
				{
					$method->invoke($controller);
					$this->response = $controller->getResponse();
					$this->responseStatus = $controller->getResponseStatus();
 				}
			} else {
				throw new Exception('Static methods not supported in Controllers', 500);
			}
 			if(is_null($this->response)) {
				throw new Exception('Method not allowed, Response is null', 405);
			}
		} catch (Exception $re)	{
			$this->responseStatus = $re->getCode();
			$this->response = array('ErrorCode' => $re->getCode(), 'ErrorMessage' => $re->getMessage());
		}
		$this->response()->send();
	}

	/**
	 * Function to resolve constroller from the Controllers
	 * directory based on resource name request.
	 */
	private function getController() {
		$expected = $this->request['resource'];


		foreach(glob(APPLICATION_PATH . '/Controllers/*.php', GLOB_NOSORT) as $controller) {
			$controller = basename($controller, '.php');
			if(strnatcasecmp($expected, $controller) == 0) {
				return 'Controllers_' . $controller;
			}
		}
		return null;
	}

	private function xmlHelper($data, $version = '1.0', $encoding = 'UTF-8') {
		$xml = new XMLWriter;
		$xml->openMemory();
		$xml->startDocument($version, $encoding);

		if(!function_exists('write')) {
			function write(XMLWriter $xml, $data, $old_key = null) {
				foreach($data as $key => $value){
				 
					if(is_array($value)){
						if(!is_int($key)) {
							$xml->startElement($key);
						}
						write($xml, $value, $key);
						if(!is_int($key)) {
							$xml->endElement();
						}
						continue;
					}
					// Special handling for integer keys in array
					$key = (is_int($key)) ? $old_key.$key : $key;
					$xml->writeElement($key, $value);
				}
			}
		}
		write($xml, $data);
		return $xml->outputMemory(true);
	}


	private function imgHelper($im,$text, $font , $W , $H , $X , $Y , $fsize , $color , $bgcolor ){

  		/* image is .jpg */
		header("Content-type:image/jpeg");
		/* name is secure.jpg */
		header("Content-Disposition:inline ; filename=secure.jpg");

 	}



 
	/**
	 * Function implementing xml response helper.
	 * Converts response array to xml response.
	 */
	private function xmlResponse() {
		return $this->xmlHelper($this->response);
	}

	/**
	 * Function implementating json response helper.
	 * Converts response array to json.
	 */
	private function jsonResponse() {
		return json_encode($this->response);
	}

	/**
	 * Function implementating rss response helper.
	 * Converts response array to rss.
	 */
	private function rssResponse() {
	
	 	  return $this->response ;
		// return  $this->xmlHelper($this->response) ;
	}


	/**
	 * Function implementing xml response helper.
	 * Converts response array to xml response.
	 */
	private function imgResponse() {
 
 		return  $this->response ;
 	}
	
	/**
	 * Function implementing querystring response helper
	 * Converts response array to querystring.
	 */
	private function qsResponse() {
		return http_build_query($this->response);
	}

	private function response() {
	
	
	
	
		if(!empty($this->response)) {
		        if($this->request['content-type']=='img')  {
		        
		        	$this->response;
			}
		        else if($this->request['content-type']=='rss')  {
		       
				 $method = $this->request['content-type'] . 'Response';
				
				 
				  //echo $this->$method();
				 $this->response = array('status' => $this->responseStatus, 'body' => $this->$method());
			}
			else
			{
			 

				$method = $this->request['content-type'] . 'Response';
				$this->response = array('status' => $this->responseStatus, 'body' => $this->$method());
				}
			 	
			
		} else {
		 
			$this->request['content-type'] = 'querystring';
			$this->response = array('status' => $this->responseStatus, 'body' => $this->response);
		}

		return $this;
	}

	/**
	 * Function to get HTTP headers
	 */
	private function getHeaders() {
		if(function_exists('apache_request_headers')) {
			return apache_request_headers();
		}
		$headers = array();
		$keys = preg_grep('{^HTTP_}i', array_keys($_SERVER));
		foreach($keys as $val) {
				$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($val, 5)))));
				$headers[$key] = $_SERVER[$val];
			}
		return $headers;
	}

	private static $codes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

	/**
	 * Function returns HTTP response message based on HTTP response status code
	 */
	private function getStatusMessage($status) {
        return (isset(self::$codes[$status])) ? self::$codes[$status] : self::$codes[500];
    }

	private static $formats = array('xml', 'json', 'img','qs','rss');

	/**
	 * Function returns response format from allowed list
	 * else the default response format
	 */
	private function getResponseFormat($format) {
		return (in_array($format, self::$formats)) ? $format : Config::RESPONSE_FORMAT;
	}

	private static $contentTypes = array(
				'xml' => 'application/xml;charset=UTF-8',
				'json' => 'application/json;charset=UTF-8',
				'img' =>  'application/octet-stream;charset=UTF-8',
				'qs' => 'text/plain;charset=UTF-8', 
				'rss' => 'application/rss+xml;charset=UTF-8' 
				

			);

	/**
	 * Function returns response content type.
	 */
	private function getResponseContentType($type = null) {
		if(isset($contentTypes))	
		return $contentTypes[$type];
	}

	private function send() {
		$status = (isset($this->response['status'])) ? $this->response['status'] : 200;
		
		
		$contentType = $this->getResponseContentType($this->request['content-type']);
		$body = (empty($this->response['body'])) ? '' : $this->response['body'];

		$headers = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusMessage($status);
		
		header($headers);
		header('Content-Type: ' . $contentType);
		//echo $status . $contentType.$headers;
		echo $body;
	}



}
