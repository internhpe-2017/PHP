<?php
//require_once('Mappers/usermodel.php');
require_once('Database/try1.php');
class Service
{
	function __construct() {
	}
	function Service()
	{}
	function AddExpences($username,$particular,$date,$amount)
	{
		$db=new try1();
		$db->Insert($username,$particular,$date,$amount);
    }  
	function view()
	{
		$db=new try1();
		$db->view();
    }  
	function Ownview($myusername)
	{
		$db=new try1();
		$db->Ownview($myusername);
    }  
	function Update($myusername,$myparticular,$date,$amount)
	{
		$db=new try1();
		$db->Update($myusername,$myparticular,$date,$amount);
    }  
     		    
   
}
