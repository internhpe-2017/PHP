<?php


class Services_Country {


 
	public function __construct() {
  	}
 	
	public function get($request) { 
	  	$org = $this->getCountry($request); 
  		return $org; 
 	}

 	public function findDefaultCountry($request) {
		$cntrymapper = new Mappers_Country();
  
		   			
		$org = $cntrymapper->findDefaultCountry( );
  		 
 		return $org ;
 	}
 	 
 	public function findCountry($request) {
		$cntrymapper = new Mappers_Country();
  
		if(isset($request['cntrycode']) && !empty($request['cntrycode']))
		{  			
		  $org = $cntrymapper->findCountry(mysql_real_escape_string($request['cntrycode']));
  		}
 		return $org ;
 	}
 	
 	
	public  function listCountryByUserCustomisation($request){
		$cntrymapper = new Mappers_Country();

		if($year=='')
		 	 $year=$_SESSION['year'];

		if(empty($user)) 
			$user= $_SESSION['userinfo']['user_id'];

 		$cust = $cntrymapper->listCountryByUserCustomisation(mysql_real_escape_string( $user),mysql_real_escape_string($year));
   		return $cust ; 
	}

	public  function createCountry($request){
 		$cntrymapper = new Mappers_Country();		
 		$dates = json_decode("[".$request['pd_cntry']."]",true);
		$userid= trim(mysql_real_escape_string($request['uid_cntry']));
		
		
		$jdata = json_encode(array('lock'=>trim(mysql_real_escape_string($request['plk_cntry']),"'"), 'year'=> $request['py_cntry'] ,"uid"=>trim(mysql_real_escape_string($request['uid_cntry'])),"cntrycode"=>trim(mysql_real_escape_string($request['pc_cntry'])),"dates"=>$dates));
     		return  $cntrymapper->createCountry($jdata,$userid);
 	}


	/*public function createCountryCustomisation($request) {
		$cntrymapper = new Mappers_Country();
		
		if(empty($user))
 			$userid= $_SESSION['userinfo']['user_id'];
	
		if(isset($request['pd']) && !empty($request['pd']))
 		{ 
			return $cntrymapper->createCountryCustomisation( mysql_real_escape_string($request['pd']), mysql_real_escape_string($request['year']),$userid);
		}	 
	}
	*/


        public function listCountry() {
		$cntrymapper = new Mappers_Country();

		return $org = $cntrymapper->listCountry( );

	}
	

        public function listCountryFest($request) {
		$cntrymapper = new Mappers_Country();
		
		$cntrycode=$request['pc'];

		return $org = $cntrymapper->listCountryFest( $cntrycode);

	}

        public function listAllCountryFest() {
		$cntrymapper = new Mappers_Country();
		
 
		return  $cntrymapper->listAllCountryFest( );

	}
	 


}
?>
