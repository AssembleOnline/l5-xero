<?php

namespace Assemble\XeroIntegration;

use Validator;
use Config;
use Illuminate\Support\MessageBag;
use Log;
/**
 * This is the xero class.
 *
 * @author Alex Blake <alex@assemble.co.za>
 */
class Xero
{

    /**
     * The class entities in the system.
     */
    private $OAUTH;
    private $URL;
    private $ENDPOINTS;

    /**
     * Create a new Searcher instance.
     */
    public function __construct()
    {
        $this->OAUTH = Config::get('xero.oauth');
        $this->URL = Config::get('xero.url');
        $this->ENDPOINTS = include 'XeroEndpoints.php';
    }


    /**
     * Authentication method for xero requests
     *
     * @var String
     * @return \Class
     */
    public function auth(){
        return $this->request("GET", "contacts");
    }

    private function request($method, $endpoint, $data = null, $headers = null)
    {
    	//check if endpoint exists
    	if( !isset($this->ENDPOINTS[strtolower($endpoint)]) ) return false;
    	//check if method allowed
    	if( !in_array(strtoupper($method), $this->ENDPOINTS[$endpoint]['methods']) ) return false;
    	    	
		//set continue
		$url = $this->URL.strtolower($endpoint);
		if(!isset($headers) || empty($headers))
    	{
    		$headers = array();
    	}
		// Now build the request
        $ch = curl_init();

        //POST
        switch (strtoupper($method)) 
        {
        	/*
	        *
	        *	POST method case, handle all post parameters etc here.
	        *	json / multipart needs to be managed too..
	        *
	        */
	        case "POST":
	        	curl_setopt($ch, CURLOPT_URL, $url);

	        	//Set Post Data
	        	curl_setopt($ch, CURLOPT_POST, true);
        		if( preg_grep( "/Content-Type:\s?application\/json/i", $headers) )
        		{ // JSON
        			if(is_array($data))
        			{
        				$data = stripslashes(json_encode($data));
        			}
        				
        			curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        			if( ! preg_grep( "/Content-Length:\s?\d*/i", $headers) )
        			{
        				array_push($headers, 'Content-Length: ' . strlen($data));
        			}
        		}
        		else
        		{ // MULTIPART
        			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            		curl_setopt($ch, CURLOPT_VERBOSE, true);
        		}
	        	

	        break;
	        
	        /*
	        *
	        *	GET method case, handle all get parameters etc here.
	        *
	        */
	        case "GET":

	        	if(isset($data) && !empty($data))
	        	{
	        		$url .= '?' . http_build_query($data);
	        	}
	        	curl_setopt($ch, CURLOPT_URL, $url);

	        break;
	        
	        /*
	        *
	        *	PUT method case, handle all put parameters etc here.
	        *
	        */
	        case "PUT":

	        	curl_setopt($ch, CURLOPT_URL, $url);

	        	//Set PUT Data
	        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	        	
        		if( preg_grep( "/Content-Type:\s?application\/json/i", $headers) )
        		{ // JSON
        			if(is_array($data))
        			{
        				$data = stripslashes(json_encode($data));
        			}
        				
        			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
        			if( ! preg_grep( "/Content-Length:\s?\d*/i", $headers) )
        			{
        				array_push($headers, 'Content-Length: ' . strlen($data));
        			}
        		}
        		else
        		{ // MULTIPART
        			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            		curl_setopt($ch, CURLOPT_VERBOSE, true);
        		}

	        break;
	        
	        /*
	        *
	        *	DELETE method case, handle all delete parameters etc here.
	        *
	        */
	        case "DELETE":

	        	if(isset($data) && !empty($data))
	        	{
	        		$url .= '?' . http_build_query($data);
	        	}
	        	curl_setopt($ch, CURLOPT_URL, $url);

	        break;
	        default:
	        	return false;
	    }
	    //END switch for METHOD



	    //MAKE SURE TO ASK FOR JSON!
        if( !preg_grep( "/Accept:\s?\w*/i", $headers) )
		{
			array_push($headers, 'Accept: application/json');
		}
		
        //set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    
	    //set remaining options
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HEADER, true); //need this for content type.

        //execute request && close connection
        $response = curl_exec($ch);

        //check if json and process
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        if($contentType == 'application/json')
        {
        	return json_decode($response, true);
        }
        else
        {
        	//check response and return false if failed.
	        //@TODO: expand later to handle failed responses, need advanced error handling here, this is basic...
	        return ( $response ? $response : false );
        }

        
    }


}
