<?php 
 define('SALT', 'qazwsx'); 

class Library_Cryp 
{


	public  function base64url_encode($data) { 
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
	} 

	public  function base64url_decode($data) { 
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
	} 


	public function getDigest($r, $p, $n, $t)
	{
		$hash = md5(sha1($r . $p, true));
		return base64_encode(sha1($n . $hash . $t, true));
	}


	public function encrypt_salt($text) 
	{ 
	
	 
	
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
	} 

	public function decrypt_salt($text) 
	{ 
	
 	 
	 
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	} 

	public function encrypt($text) 
	{ 
	
		return rtrim(strtr(base64_encode(gzdeflate($text, 9)), '+/', '-_'), '=');
	
 	} 

	public function decrypt($text) 
	{ 
	
		 return gzinflate(base64_decode(strtr($text, '-_', '+/')));
	 
 	} 

	public function sample($text) 
	{

		$encryptedmessage = encrypt("your message"); 
		echo decrypt($encryptedmessage); 
	}

}
?>