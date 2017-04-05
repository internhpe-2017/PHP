<?php 

class Library_Utilities 
{

	public function lastIndexOf($string,$item){  
	    $index=strpos(strrev($string),strrev($item));  
	    if ($index){  
		$index=strlen($string)-strlen($item)-$index;  
		return $index;  
	    }  
		else  
		return -1;  
	}  

	public function getDigest($r, $p, $n, $t)
	{
		$hash = md5(sha1($r . $p, true));
		return base64_encode(sha1($n . $hash . $t, true));
	}


	public function character_remove($content)
	{
		$newcontent = preg_replace("/[^a-zA-Z0-9\s]/", "", stripslashes($content));
		$newcontent = str_replace('\'','',$newcontent);
		$newcontent = str_replace('%','',$newcontent);
		$newcontent = str_replace('&','',$newcontent);
		$newcontent = str_replace('/','',$newcontent);
		$newcontent = str_replace(' ','-',$newcontent);
		return $newcontent;
	}


	public function truncate($string, $del)
	{
		$len = strlen($string);
		if ($len > $del)
		{
		$new = substr($string,0,$del)."...";
		return $new;
		}
		else
			return $string;
	}

	public function escapeToHTMLString($str)
	{

		$str=str_replace("&","&amp;",$str);
		$str=str_replace("<","&lt;",$str);
		$str=str_replace(">","&gt;",$str);
		$str=str_replace("'","&#39;",$str);
		return $str;
	}

	public  function addtoyahoo($link,$url)
	{



		// country_state_org_location_year
		$yahoolink='';
		//date('Y-j-m')

		$yahooheader = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		$yahoolink .= '<url>'."\n".'<loc>'.'http://'.$_SERVER['HTTP_HOST'].''.''.$url.''.$link.'</loc>'."\n".'<lastmod>'.date('Y-j-m').'</lastmod>'."\n".'<changefreq>monthly</changefreq>'."\n".'</url>'."\n";
		$yahoofooter = '</urlset>';
		$fnyahoo ="../sitemap.xml";
		$fileyahoo = fopen($fnyahoo,"r");
		$sizeyahoo = filesize($fnyahoo);
		if($sizeyahoo == 0)
		{
			$sizeyahoo = 1;
		}
		$textyahoo = fread($fileyahoo,$sizeyahoo);
		fclose($fileyahoo);
		$fileyahoo = fopen($fnyahoo,"w");
		fwrite($fileyahoo,null);
		if(empty($textyahoo))
		{
			$fullyahoolink = $yahooheader.$yahoolink.$yahoofooter;
		}
		else
		{
			$textyahoo = str_replace('</urlset>','',$textyahoo);
			$fullyahoolink = $textyahoo.$yahoolink.$yahoofooter;

		}

		fwrite($fileyahoo,$fullyahoolink);
		fclose($fileyahoo);
		return;

	}

	public  function addtogoogle($link,$url)
	{


		$fn = "../sitemap.txt";
		 $file = fopen($fn, "a+");
		$size = filesize($fn);
		if($size == 0)
		{
			$size = 1;
		}
		$text = fread($file, $size);
		fwrite ($file, 'http://'.$_SERVER['HTTP_HOST'].''.''.$url.''.$link."\n");
		fclose($file);
		return;

	}

	public  function addtobookmark($link,$url)
	{


		$json_a=json_decode($link,true);




		$bkheader = '<!DOCTYPE NETSCAPE-Bookmark-file-1>'."\n";
		$bktitle = '<TITLE>Connect your common Days</TITLE>'."\n";
		$bkh1 = '<H1> Holidays Plans, Event ,Travel Planning,Vacation </H1>'."\n";
		$bkdlT ='<DL><p>'."\n";
		$bkdlD ='<DT><p><A HREF="'.'http://'.$_SERVER['HTTP_HOST'].''.''.$url.''.$link.'</A>'."\n";
		$bkdfooter ='</DL>';
		$fnyahoo ="../bookmark.xml";
		$fileyahoo = fopen($fnyahoo,"r");
		$sizeyahoo = filesize($fnyahoo);
		if($sizeyahoo == 0)
		{
			$sizeyahoo = 1;
		}
		$textyahoo = fread($fileyahoo,$sizeyahoo);
		fclose($fileyahoo);
		$fileyahoo = fopen($fnyahoo,"w");
		fwrite($fileyahoo,null);
		if(empty($textyahoo))
		{
			$fullyahoolink = $bkheader.$bktitle.$bkh1.$bkdlT.$bkdlD.$bkdfooter  ;
		}
		else
		{

			$textyahoo = str_replace('</DL>','',$textyahoo);
			$fullyahoolink = $textyahoo.$bkdlD.$bkdfooter  ;

		}

		fwrite($fileyahoo,$fullyahoolink);
		fclose($fileyahoo);
		return;



	}


	public  function addtoror($link,$title1,$image1,$url)
	{

		$json_a=json_decode($link,true);
		$rorheader = '<?xml version="1.0" encoding="utf-8"?>'."\n".'<rss version="2.0" xmlns:ror="http://rorweb.com/0.1/">'."\n";
		$rorchannelhead= '<channel>'."\n";
		$rortitlehead= '<title>'.$title1.'</title>'."\n";
		$rorlinkhead= '<link>'.$title1.'</link>'."\n";
		$roritems = '<item>'."\n".'<title>'.'http://'.$_SERVER['HTTP_HOST'].''.''.$url.''.$link.'</title>'."\n".'<link>'.'http://'.$_SERVER['HTTP_HOST'].'/'.''.$url.''.$link.'</link>'."\n".'<description>daily</description>'."\n".'<ror:type>daily</ror:type>'."\n".'<ror:keywords>'.$title1.'</ror:keywords>'."\n".'<ror:image>'.$image1.'</ror:image>'."\n".'<ror:updated>monthly</ror:updated>'."\n".'<ror:updatePeriod>monthly</ror:updatePeriod>'."\n".'</item>'."\n";
		$rorfooterc = '</channel></rss>'."\n";

		$fnror ="../ror.xml";
		$fileror = fopen($fnror,"r");
		$sizeror = filesize($fnror);
		if($sizeror == 0)
		{
			$sizeror = 1;
		}
		$textror = fread($fileror,$sizeror);
		fclose($fileror);
		$fileror = fopen($fnror,"w");
		fwrite($fileror,null);
		if(empty($textror))
		{
			$fullrorlink = $rorheader.$rorchannelhead.$rortitlehead.$rorlinkhead.$roritems.$rorfooterc;
		}
		else
		{
			$textror = str_replace('</channel></rss>','',$textror);

			$fullrorlink = $textror.$roritems.$rorfooterc ;
		}
		fwrite($fileror,$fullrorlink);
		fclose($fileror);
		return;

	}


	public function format_email($info, $format)
	{   
		//set the root   
		$root = $_SERVER['DOCUMENT_ROOT'].'/';    
		//grab the template content   
		$template = file_get_contents($root.'/signup_template.'.$format);    
		//replace all the tags   
		$template = ereg_replace('{USERNAME}', $info['username'],$template);   
		$template = ereg_replace('{EMAIL}', $info['email'],$template);   
		$template = ereg_replace('{KEY}', $info['key'],$template);   
		$template = ereg_replace('{SITEPATH}','http://site-path.com',$template);   
		//return the html of the template   
		return $template;   
	}   

	 public function send_email($info){   

	    //format each email   
	    $body = format_email($info,'html');   
	    $body_plain_txt = format_email($info,'txt');   

	    //setup the mailer   
	    $transport = Swift_MailTransport::newInstance();   
	    $mailer = Swift_Mailer::newInstance($transport);   
	     $message = Swift_Message::newInstance();   
	    $message ->setSubject('Welcome to Connect Days ');   
	    $message ->setFrom(array('noreply@connectdays.com' => 'Connect Days'));   
	    $message ->setTo(array($info['email'] => $info['username']));   

	    $message ->setBody($body_plain_txt);   
	    $message ->addPart($body, 'text/html');   

	    $result = $mailer->send($message);   

	    return $result;   

	}  

}
?>