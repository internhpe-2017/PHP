<?php


class Mappers_Country {
	private $db;

	public function __construct() {
		$this->db = new Database_Db_Country();
	}


	public function findCountry($cntrycode) {
		return $this->db->findCountry($cntrycode);
	}
	public function findDefaultCountry( ) {
		return $this->db->findDefaultCountry( );
	}



	public  function listCountryByUserCustomisation($user,$year){
		return $this->db->listCountryByUserCustomisation($user,$year);


	}
        public function listCountryDataByGraphToken($graph_share_token ) {
     
   
     	    return $this->db->listCountryDataByGraphToken($graph_share_token );	
        }

	public function createCountryCustomisation($cntryids,$year,$userid) {
	
		 return $this->db->createCountryCustomisation($cntryids ,$year,$userid);
	}
	
	public function createCountry($jdata) {
		return $this->db->createCountry($jdata);
	}

        public function listCountry() {
		return $this->db->listCountry();

	}

        public function listCountryFest($cntrycode) {
		return $this->db->listCountryFest($cntrycode);

	}
	
        public function listAllCountryFest() {
		return $this->db->listAllCountryFest();

	}
	
	

	 
}
?>