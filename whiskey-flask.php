<?php

/**
 * @author Seth Ryder
 * @name Whiskey Flask
 * @version 0.01
 */

class wmAPI 
{
    
    var $api_key;
    var $api_url;
    var $format;
    
    function __construct($site, $key, $format) 
	{
	   $this->api_key = $key;
       
        switch($site) 
		{
            case "cv":
                $this->api_url = 'http://api.comicvine.com';
                break;
            default:
                $this->api_url = 'http://api.giantbomb.com';
        }

        switch($format) 
		{
            case "xml":
                $this->format = 'xml';
                break;
            default:
                $this->format = 'json';
                break;
        }
    }
        
    function getDetail($resource, $id, $field_list) 
	{
        $rawData = file_get_contents("$this->api_url/$resource/$id/?api_key=$this->api_key&field_list=$field_list&format=$this->format");

        if($this->format == 'json') 
		{
            $decoded = json_decode($rawData);
        }
        elseif ($this->format == 'xml') 
		{
            $decoded = simplexml_load_string($rawData);
        }
        
        return $decoded;
    }
    
    function getList($resource, $limit, $offset, $field_list) 
	{
        $raw_data = file_get_contents("$this->api_url/$resource/?api_key=$this->api_key&offset=$offset&field_list=$field_list&format=$this->format");
        $decoded = json_decode($raw_data);
        return $decoded;
    }
}

?>