<?php
/*
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
class RestBase {

	protected  $request = array(); // Array storing request
	protected $response; // Array storing response
	protected $responseStatus; //   storing response
	
	 
 	protected function checkAuth() {
 	
		if(!Config::ENABLE_AUTH) {
 			return;
		}
		/*
		const HTTP_BASIC = 1;
		const IP = 2;
		const OAUTH = 3;
		const FB_AUTH = 4;
		*/
		//print_r($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
		/*$type_string=substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'],0,strrpos($_SERVER['REDIRECT_HTTP_AUTHORIZATION'],' ')); 
		*/
		$type = Auth_Auth::HTTP_BASIC;
   		if(!Auth_Auth::getInstance($type)->check()) {
			throw new Exception('Unauthorized', 401);
			//header("Location:logout.php");
		}
		else
		{
			// throw new Exception('Unauthorized', 600);
			//list($client_id, $client_password ) = explode(':' , base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
			$_SESSION['isLoggedIn'] = "isLoggedIn";
			$_SESSION['timeOut'] = 120;
			$_SESSION['loggedAt']= time();	 	 
		}
	}

}
