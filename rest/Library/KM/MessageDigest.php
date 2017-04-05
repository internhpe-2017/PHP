<?php 

/** 
 * Message digest implementation. 
 *  
 * @author Marius Zadara <marius@zadara.org> 
 * @category org.zadara.marius.messagedigester.classes  
 * @copyright (C) 2008, Marius Zadara <marius@zadara.org> 
 * @license GNU GPL 
 * @package org.zadara.marius.messagedigester 
 *  
 * @final 
 */ 
final class Library_KM_MessageDigest 
{ 
    /** 
     * The choosen digest algorithm. 
     *  
     * @var String 
     * @access private 
     * @see getInstance() 
     */ 
    private $hashAlgorithm; 

    /** 
     * The text to digest 
     *  
     * @var String 
     * @access private 
     * @see update() 
     */ 
    private $textToDigest; 
     
    /** 
     * Default constructor. 
     * It will initialize the hash algorithm and text to digest 
     * will NULL values. 
     *  
     * @access public 
     */     
    public function __construct() 
    { 
        // set to null the hash algorithm and text to digest 
        $this->hashAlgorithm = null; 
        $this->textToDigest = null; 
    } 
      
    /** 
     * Get an instance of a hash algorithm. 
     * The hash algorithm must have been defined. 
     * 
     * @param String $hashAlgorithmName The name of the hash algorithm 
     * @access public 
     */ 
    public function getInstance($hashAlgorithmName) 
    { 
        // search for the hash algorithm class 
        if (!class_exists($hashAlgorithmName))  
        {
            throw new Exception(sprintf("'%s' license algorithm not found", $hashAlgorithmName)); 
        }	
        
        // set the algorithm 
        $this->hashAlgorithm = new $hashAlgorithmName; 
        
        // printf(  $this->hashAlgorithm ); 
        // echo $this->hashAlgorithm; 
        // the class algorithm must implement the specific interface 
        // if not, error 
         
        if (!in_array("Library_KM_IHashAlgorithm",  class_implements ( $this->hashAlgorithm ) )) 
        { 
          
            $this->hashAlgorithm = null; 
            throw new Exception(sprintf("'%s' license is not correctly defined", $hashAlgorithmName));             
        } 
        
        
    } 
     
    /** 
     * Reset the message digest data. 
     * @access public 
     */ 
    public function reset() 
    { 
        if (!is_null($this->hashAlgorithm)) 
        { 
            // reset to default values 
            $this->hashAlgorithm = null; 
            $this->textToDigest = null; 
        } 
    } 
     
    /** 
     * Function to set the text for digest.  
     * Also will check the algorithm to see if has been set 
     * 
     * @param String $text The text to digest later 
     */ 
    public function update($text) 
    { 
         // check the hash algorithm 
         
        
        if (is_null($this->hashAlgorithm)) 
        {
         
            throw new Exception("Hash algorithm not set yet");  
         
        }

        // check the length of the string 
        if (strlen($text) == 0) 
            throw new Exception("Empty text to digest"); 
         
        // set the text 
        $this->textToDigest = $text;   
        
        //echo $this->textToDigest;
    } 
         
    /** 
     * Digest function.  
     * This function will digest the text set before. 
     * 
     * @return string The text digested 
     * @see Library_KM_IHashAlgorithm::hash() 
     */ 
    public function digest() 
    { 
        // check the hash algorithm 
        if (is_null($this->hashAlgorithm)) 
            throw new Exception("Hash algorithm not set yet"); 
             
        // check the text to digest 
        if (is_null(($this->textToDigest))) 
            throw new Exception("Text to hash not set yet");     
             
        // set the default return string 
        $hashedText = "";     
             
        try 
        { 
            // try to call the hash method from the algorithm 
            $hashedText = $this->hashAlgorithm->hash($this->textToDigest);  
        } 
        catch (HashAlgorithmException $hashAlgorithmException) 
        { 
            // throw the exception further 
            throw new Exception 
            ( 
                sprintf 
                ( 
                    "Failed to hash the text '%s' using the '%s' algorithm: %s",  
                    $this->textToDigest,  
                    $this->hashAlgorithm, 
                    $hashAlgorithmException->getMessage() 
                ) 
            ); 
        } 
        catch (Exception $generalException) 
        { 
            // throw the exception further 
            throw new Exception 
            ( 
                sprintf 
                ( 
                    "Failed to hash the text '%s' using the '%s' algorithm: %s",  
                    $this->textToDigest,  
                    $this->hashAlgorithm, 
                    $hashAlgorithmException->getMessage() 
                ) 
            ); 
        } 
         
        // return the hashed text 
        return $hashedText; 
    } 
} 

?>