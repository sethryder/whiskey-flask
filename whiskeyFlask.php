<?php

/**
 * @author Seth Ryder
 * @name Whiskey Flask
 * @version 0.02
 */

class whiskeyFlask {
    
    var $api_key;
    var $api_url;
    var $format;
    
    //Build up some required settings.
    function __construct($site, $key, $format='json') {        
	   $this->api_key = $key;
       
        //Switch our API url depending on what API we are using.
        switch($site) {
            case "cv":
                $this->api_url = 'http://api.comicvine.com';
                break;
            default:
                $this->api_url = 'http://api.giantbomb.com';
        }

        //Define how we want our results returned with either XML or JSON.
        switch($format) {
            case "xml":
                $this->format = 'xml';
                break;
            default:
                $this->format = 'json';
                break;
        }
    }
    
    //Function used for a detail resource request.    
    function getDetail($resource, $id, $field_list=NULL) {
        $rawData = file_get_contents("$this->api_url/$resource/$id/?api_key=$this->api_key&field_list=$field_list&format=$this->format");

        if($this->format == 'json') {
            $result = json_decode($rawData);
        }
        elseif ($this->format == 'xml') {
            $result = simplexml_load_string($rawData);
        }
        
        return $result;
    }
    
    //Function used for a list resource request.
    function getList($resource, $offset=0, $limit=100, $field_list=NULL) {
        $options = $this->buildOptions('list', array($field_list));
        
        print_r($options);
        
        $rawData = file_get_contents("$this->api_url/$resource/?api_key=$this->api_key&offset=$offset&$options&format=$this->format");
        
        if($this->format == 'json') {
            $results = json_decode($rawData);
        }
        elseif ($this->format == 'xml') {
            $results = simplexml_load_string($rawData);
        }
        
        return $results;
    }
    
    //Function used for searching.
    function search($query, $resources=NULL, $offset=0, $limit=20, $field_list=NULL) {
        $options = $this->buildOptions('search', array($resources,$field_list));
        
        $rawData = file_get_contents("$this->api_url/search/?api_key=$this->api_key&query=$query&$options&offset=$offset&format=$this->format");
        
        if($this->format == 'json') {
            $results = json_decode($rawData);
        }
        elseif ($this->format == 'xml') {
            $results = simplexml_load_string($rawData);
        }
                
        return $results;
    }
    
    //This function is used to build our optons for pulls when needed. Some filters do not like to be leaved blank, which is part of the reason this is required.
    function buildOptions($resource, $options) {        
        switch($resource) {
            case "search":
                if ($options[0] == NULL) {    
                    $rOptions = '';
                } else {
                    $rOptions = "resources=$options[0]&";
                }
                if ($options[1] == NULL) {    
                    $fOptions = '';
                } else {
                    $fOptions = "field_list=$options[1]&";
                }
                $oReturn = $rOptions.$fOptions;
                return $oReturn;
                break;
            case "list":
                if ($options[0] == NULL) {    
                    $fOptions = '';
                } else {
                    $fOptions = "field_list=$options[0]&";
                }
                return $fOptions;
                break;
            /* At the moment this seems to not be needed.
            case "detail":
                break; 
            */
        }  
    }
}

?>