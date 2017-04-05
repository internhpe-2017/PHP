<?php
class TokensController extends Controller
{
	/**
	 * @RESTMethod post
	 * @RESTResource tokens/new
	 */
	public function newToken()
	{
		global $db, $oAuth;

		if(array_key_exists('XMLRequest', $this->requestData['post']))
		{		
			$xmlRequest = stripslashes($this->requestData['post']['XMLRequest']);
		}
		else
		{
			return $this->triggerError(400, 'Bad Request');
		}	

		try
		{
			require_once('parsers/TokenCreationRequestParser.php');
			require_once('vo/TokenRequestVO.php');
			$tokenCreationRequestParser = new TokenCreationRequestParser($xmlRequest);
			$tokenRequestVO = $tokenCreationRequestParser->parse();

			$this->checkAuthentication($tokenRequestVO);
			
			$oAuthRequest = OAuthRequest::buildRequest($this->requestMethod, $this->requestResource, $this->requestHeaders);
			$token = $oAuth->fetchAccessToken($oAuthRequest);
			$body = '<tokenCreationResponse xmlns="http://theid.mobi/rest/1.0/schemas">';
			$body .= '<tokenInfo>';
			$body .= '<token>' . $token->key . '</token>';
			$body .= '<tokenSecret>' . $token->secret . '</tokenSecret>';
			$body .= '<tokenCreation>' . $token->creation . '</tokenCreation>';
			$body .= '<tokenTtl>' . $token->ttl . '</tokenTtl>';
			$body .= '<tokenExpires>'. $token->expiry . '</tokenExpires>';
			$body .= '</tokenInfo>';
			$body .= '</tokenCreationResponse>';
			
			$output = array('status' => 201, 'body' => $body);
			return $output;
		}
		catch(Exception $e)
		{
			return $this->triggerError($e->getCode(), $e->getMessage());
		}
	}
	
	private function checkAuthentication(TokenRequestVO $tokenRequestVO)
	{
		global $db;
		
		$Qusername = $db->query('select mobileno, pin from :table_theid where mobileno = :mobileno');
		$Qusername->bindTable(':table_theid', 'theid_theid');
		$Qusername->bindValue(':mobileno', $tokenRequestVO->getUsername());
		$Qusername->execute();
		
		if($Qusername->numberOfRows() == 1)
		{
			$mobileno = $Qusername->value('mobileno');
			$pin = $Qusername->value('pin');
		}
		else
		{
			throw new Exception('Username cannot be found.', 403);
		}

		$digest = base64_encode(sha1($tokenRequestVO->getNonce() . $pin . $tokenRequestVO->getTimestamp(), true));
		if ($digest != $tokenRequestVO->getDigest())
		{
			throw new Exception('Authentication failed. Please check the username/pin again', 403);			
		}
	}
	
	/**
	 * @RESTMethod get
	 * @RESTResource tokens/(.*)/info
	 */
	public function tokenInfo()
	{
		print_r('y');
	}	

}
?>