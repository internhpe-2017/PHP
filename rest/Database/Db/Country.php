<?php

class Database_Db_Country extends Database_Db_Abstract {
	protected $table = Database_DatabaseTables::COUNTRY;

	public function __construct() {
		parent::__construct();
	}

        public function findlock($country_code,$year) {

		$lockdata = array();
	   	$rs = $this->db->query('select yc.lockedit as lockedit   from  country c , year_country_prof yc where  c.country_code=:country_code   and yc.year=:year and is_deleted=0 and is_disabled=0 and c.country_code=yc.country_code ');
 
		$rs->bindValue(':country_code', $country_code); 
		$rs->bindValue(':year', $year);
		
		
		$rs->execute();
		

		if(($rs->numberOfRows() == 0) || $this->db->isError()) {
			return false;
		}

 
		$lockdata  = $rs->value('lockedit');
		$rs->freeResult();
		return $lockdata;

 	}
	public function createCountry($jdata) {

		if($this->db->isError()) {
			return false;
		}

 		$json_a=json_decode($jdata,true);
 		
		 $link=$json_a['cntrycode'].'-'.$json_a['year'].'-'.'country';		
		 $link_no_year=$json_a['cntrycode'].'-'.'country';
		 
		 $lockdata=$this->findlock($json_a['cntrycode'],$json_a['year']);
		 
		 
		$jdata_excaped=mysql_real_escape_string($jdata) ;
		
		if( $lockdata=='on')

		$sql = "INSERT INTO sourcing (json_add,is_validate,type,email) VALUES ('$jdata_excaped', '1','3','$userid')";
		
		else
		 
		
		$sql = "INSERT INTO sourcing (json_add,is_validate,type,email) VALUES ('$jdata_excaped', '0','4','$userid')";
		
		$rs=$this->db->query($sql);
 		$handlerres=$rs->execute(); 
 		
		$orgUtils = new Library_Utilities(); 
		
		$orgUtils->addtoyahoo($link,'/');
		$orgUtils->addtogoogle($link,'/');
		$orgUtils->addtobookmark($link,'/');
		$orgUtils->addtoror($link,'','','/'); 
	
		$orgUtils->addtoyahoo($link_no_year,'/');
		$orgUtils->addtogoogle($link_no_year,'/');
		$orgUtils->addtobookmark($link_no_year,'/');
		$orgUtils->addtoror($link_no_year,'','','/'); 
 
		return true;


 	}
 	
 	public function findDefaultCountry( ) 
 	{
 		 	$sql = "SELECT * FROM country WHERE is_disabled=:isdisb   and is_default_cntry=:isdis";
			$rs=$this->db->query($sql);
			$rs->bindValue(':isdisb', 0);			 
			$rs->bindValue(':isdis', 1);
			$handlerres=$rs->execute();
			if($this->db->isError()) {
				return false;
			}
	 		$llist = array();
			while($rst = mysql_fetch_assoc($handlerres)){
	 			$clist =  array("country"=>array( 'country_lat'=>trim($rst['cap_loc_latitude'] ),'country_long'=>trim($rst['cap_loc_longitude'] ),  'country_cap'=>trim($rst['country_cap'] ),'country_name'=>trim($rst['country_name'] ),'country_cap'=>trim($rst['country_cap'] ), 'max_hols'=>trim($rst['max_hols']), 'country_code'=>trim($rst['country_code']), 'country_def_cntr'=>trim($rst['is_default_cntry']) , 'country_def_relig'=>trim($rst['def_religion_code']) ));
	 			$llist[] =  $clist;
			}
			$rs->freeResult();
			return  $llist;
	
	
 	}

        public function findCountry($cntrycode) {

		if($cntrycode=="")
 			  $cntrycode='IND';
 			  
	 	$sql = "SELECT * FROM country WHERE is_disabled=:isdisb and country_code=:cntrycode";
 		
		$rs=$this->db->query($sql);
		$rs->bindValue(':isdisb', 0);
		$rs->bindValue(':cntrycode', $cntrycode);
		$handlerres=$rs->execute();
		if($this->db->isError()) {
			return false;
		}
 		$llist = array();
		while($rst = mysql_fetch_assoc($handlerres)){
 			$clist =  array("country"=>array( 'country_lat'=>trim($rst['cap_loc_latitude'] ),'country_long'=>trim($rst['cap_loc_longitude'] ),  'country_cap'=>trim($rst['country_cap'] ),'country_name'=>trim($rst['country_name'] ),'country_cap'=>trim($rst['country_cap'] ), 'max_hols'=>trim($rst['max_hols']), 'country_code'=>trim($rst['country_code']), 'country_def_cntr'=>trim($rst['is_default_cntry'])  ));
 			$llist[] =  $clist;
		}
		$rs->freeResult();
		return  $llist;


 	}

	public function updateCountry($orgid) {


 	}

	public function deleteCountry($orgid) {


 	}

        public function listCountryList() {



		$sql = "SELECT * FROM country WHERE is_disabled=:isdisb order  by country_name";
		$rs=$this->db->query($sql);
		$rs->bindValue(':isdisb', 0);
		$handlerres=$rs->execute();
		if($this->db->isError()) {
			return false;
		}


		 
		$clist = array();
		while($rst = mysql_fetch_assoc($handlerres)){
			$clist = array_merge($clist,array($rst['country_code']=>trim($rst['country_name']) ));
		}
		$rs->freeResult();
		//return  array($clist);
		return   ($clist);


 	}
 	
       public function listCountry() {
       
        

 		$sql = "SELECT * FROM country b,regions a WHERE  a.region_id=b.region_id and is_disabled=:isdisb order  by country_name";
		$rs=$this->db->query($sql);
		$rs->bindValue(':isdisb', 0);
		$handlerres=$rs->execute();
		if($this->db->isError()) {
			return false;
		}
 		$llist = array();
		while($rst = mysql_fetch_assoc($handlerres)){
 			$clist =  array("country"=>array(  'region_id'=>trim($rst['region_id'] ),'country_lat'=>trim($rst['cap_loc_latitude'] ),'country_long'=>trim($rst['cap_loc_longitude'] ),  'country_cap'=>trim($rst['country_cap'] ),'country_name'=>trim($rst['country_name'] ), 'max_hols'=>trim($rst['max_hols']), 'country_code'=>trim($rst['country_code']) ));
 			$llist[] =  $clist;
		}
		$rs->freeResult();
		return  $llist;
 	}


	//{"sel":{"1":"india","2":"finland"},"nosel":[]} 
        public function listCountryByUserCustomisation($user,$year) {
    
 		 
// 		  echo    '%%%%'.$user.$year;
 
		$rs=$this->db->query('select  concat("{",concat((  ifnull((select   CONCAT("\"sel\":{",GROUP_CONCAT( "",CONCAT("\"",CONCAT(country_code,"\":\"",CONCAT(country_name,"","")),"\"") ,""),"}") from country  where country_code in ( select  country_code from country_customisation where user_id=:userid1 and year=:year1 order by country_code) and is_disabled=0 order by country_code ),"\"sel\":{}")),",", ifnull((select   CONCAT("\"nosel\":{",GROUP_CONCAT( "",CONCAT("\"",CONCAT(country_code,"\":\"",CONCAT(country_name,"","")),"\"") ,""),"}") from country where country_code not in ( select  country_code from country_customisation where user_id=:userid2 and year=:year3 order by country_code)  and is_disabled=0  order by country_code ), "\"nosel\":{}") ),"}") as data');

 		
		$rs->bindValue(':userid1', $user);
		$rs->bindValue(':userid2', $user);
		$rs->bindValue(':year1', $year);
 		$rs->bindValue(':year3', $year);
 		 
 		$handlerres=$rs->execute(); 
		if(($rs->numberOfRows() == 0) || $this->db->isError()) {
			return false;
		} 
		
		$otlist=$rs->value('data'); 
		
 		$rs->freeResult();
  				
		return json_decode($otlist); 
 	}

     public function listCountryDataByGraphToken($graph_share_token ) {
     		//descrypt 
    		$cryp = new Library_Cryp();
    		//utils
    		$utils = new Library_Utilities();
    		
    		$ug=substr($graph_share_token,0, $utils->lastIndexOf($graph_share_token,'-'));
    		
    		 
    		$decry=$cryp->decrypt($graph_share_token);
    		 
		$cntry_list= strtok($decry, '_');
		$user_id= strtok('_');
		$year= strtok('_');
		
		
		//var_dump($cntry_list);exit;
  		//return $cntry_list;
  		// HARDCODING
		$rs=$this->db->query('select GROUP_CONCAT( "", org_json_data   ,"") as data from org_profile where org_id in (2,1) and is_deleted=0 and year=:year');
 		 
		//$rs->bindValue(':orglist', str_replace($cntry_list,'"',''));
 		$rs->bindValue(':year', $year);
  		$handlerres=$rs->execute(); 
		if(($rs->numberOfRows() == 0) || $this->db->isError()) {
			return false;
		} 
				
		$otlist=$rs->value('data'); 		
 		$rs->freeResult();  				
		return json_decode('['.$otlist.']',true); 

     } 


        public function listCountryFest($cntrycode) 
        {
        	//HARDCODING
		if($cntrycode=="")
 			  $cntrycode='IND';
 			  
	 	$sql = "SELECT * FROM country WHERE is_disabled=:isdisb and country_code=:cntrycode";
 		
		$rs=$this->db->query($sql);
		$rs->bindValue(':isdisb', 0);
		$rs->bindValue(':cntrycode', $cntrycode);
		$handlerres=$rs->execute();
		if($this->db->isError()) {
			return false;
		}
 		while($rst = mysql_fetch_assoc($handlerres)){
 			$clist =     $rst['cntry_fest_json'] ; 			 
		}
		$rs->freeResult();
		// print_r( $clist);
		return  json_decode($clist,true) ;

	}
     
        public function listAllCountryFest( ) 
        {
  			  
	 	$sql = "SELECT * FROM country WHERE is_disabled=:isdisb ";
 		
		$rs=$this->db->query($sql);
		$rs->bindValue(':isdisb', 0);
 		$handlerres=$rs->execute();
		if($this->db->isError()) {
			return false;
		} 	 
		while($rst = mysql_fetch_assoc($handlerres)){
 			$llist[] =     $rst['cntry_fest_json']   ;
 			 
		}
		$rs->freeResult(); 
		// print_r( $llist);

		//return  $llist;
		return  $llist  ;

	}
	 
}
?>
