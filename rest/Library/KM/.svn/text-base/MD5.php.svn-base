<?php 

/** 
 * MD5 hasher implementation. 
 *  
 * @author Marius Zadara <marius@zadara.org> 
 * @category org.zadara.marius.messagedigester.classes  
 * @copyright (C) 2008, Marius Zadara <marius@zadara.org> 
 * @license GNU GPL 
 * @package org.zadara.marius.messagedigester 
 *  
 * @final 
 * @see IHashAlgorithm 
 */ 
  
final class Library_KM_MD5 implements Library_KM_IHashAlgorithm  
{ 
    /** 
     * Hash function implementation. 
     * 
     * @param string $string The text to hash 
     * @param boolean $raw_output Raw output 
     * @return string The hash of the text 
     * @static  
     */ 
    public static function hash($string, $raw_output = false) 
    { 
        // validate the length of the string 
        if (strlen($string) == 0) 
            throw new Exception("Empty string to hash."); 

        // set the correct raw ouput 
        if (($raw_output !== false) && ($raw_output !== true)) 
            $raw_output = false;     
             
        // base function call 
        return md5($string, $raw_output); 
    } 
} 


?>