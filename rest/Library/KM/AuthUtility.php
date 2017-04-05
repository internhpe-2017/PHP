<?php 
 
 class Library_KM_AuthUtility 
{ 

	public   function hash($keyId, $salt )
          { 
         
           
         
		if($salt == null || $keyId ==null)
			return null;
		$md =new Library_KM_MessageDigest();
		$md->getInstance("Library_KM_SHA1");
		//$md->reset();
		 
		
		$res = $keyId."|".$salt;
		//echo $res; 
		$test=$md->update($res);
  
		  
		 $test1=$md->digest();
		  echo $test1.'______'.$res	; 
	 
	 
		/*$encoded =  Base64.encodeBase64($md.digest(), false, true, 64);
		$md->reset();
		$res = $salt+"ConnectDay5"+$salt;
		$md->update($res.getBytes());
		$appender =  Base64.encodeBase64($md.digest(), false, true, 64);
		$encoded += "/" + $appender;
		 
 		return $encoded;
 		*/
 		return '';
         }

} 

?>