<?php

/**
 * @author Seth Ryder
 * @name Whiskey Flask
 */

class whiskeyFlask {
    
    var $api_key;
    var $api_url;
    var $format;
    
    //Build up some required settings.
    function __construct($site, $key, $format='json', $callback=NULL) {        
	   $this->api_key = $key;
       
        //Switch our API url depending on what API we are using.
        switch($site) {
            case 'cv':
                $this->api_url = 'http://api.comicvine.com';
                break;
            default:
                $this->api_url = 'http://api.giantbomb.com';
        }

        //Define how we want our results returned with either XML or JSON.
        switch($format) {
            case 'xml':
                $this->format = 'xml';
                break;
            case 'jsonp':
                $this->format = 'jsonp';
                break;
            default:
                $this->format = 'json';
                break;
        }
    }
    
    //Function used for a detail resource request.    
    private function getDetail($resource, $id, $field_list=NULL) {
        $rawData = file_get_contents("$this->api_url/$resource/$id/?api_key=$this->api_key&field_list=$field_list&format=$this->format");

        $result = $this->parseResponse($rawData);
        
        return $result;
    }
    
    //Function used for a list resource request.
    public function getList($resource, $offset=0, $limit=100, $field_list=NULL) {
        $options = $this->buildOptions(array($field_list, NULL));
        
        $rawData = file_get_contents("$this->api_url/$resource/?api_key=$this->api_key&offset=$offset&$options&limit=$limit&format=$this->format");
        
        $results = $this->parseResponse($rawData);
        
        return $results;
    }
    
    //Function used for searching.
    public function search($query, $resources=NULL, $offset=0, $limit=20, $field_list=NULL) {
        $options = $this->buildOptions(array($field_list,$resources));
        
        $rawData = file_get_contents("$this->api_url/search/?api_key=$this->api_key&query=$query&offset=$offset&$options&limit=$limit&format=$this->format");
        
        $results = $this->parseResponse($rawData);
                
        return $results;
    }
    
    //This function is used to build our optons for pulls when needed. Some filters do not like to be leaved blank, which is part of the reason this is required.
    private function buildOptions($options) { 
        if ($options[0] == NULL) {    
            $fOptions = '';
        } else {
            $fOptions = "field_list=$options[0]&";            
        }
        if ($options[1] == NULL) {  
            $rOptions = '';            
        } else {
            $rOptions = "resources=$options[1]&";
        }
                
        $oReturn = $rOptions.$fOptions;
        return $oReturn;
    }
    
    private function parseResponse($raw) {
        if($this->format == 'json') {
            $parsed = json_decode($raw);
        }
        elseif ($this->format == 'xml') {
            $parsed = simplexml_load_string($raw);
        }
        
        return $parsed;
    }
}

?>