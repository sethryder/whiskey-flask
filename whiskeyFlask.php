<?php
/**
 * @author Seth Ryder
 * @name Whiskey Flask
 */

class whiskeyFlask
{
    var $api_key;
    var $api_url;
    var $format;
    var $cache;
    var $cache_timeout;
    var $timeout = 10;
    var $debug = null;

    //Build up some required settings.
    function __construct($site, $key, $format = 'json', $callback = null)
    {
        $this->api_key = $key;

        //Switch our API url depending on what API we are using.
        switch ($site)
        {
            case 'cv':
                $this->api_url = 'http://api.comicvine.com';
                $this->site = 'cv';
                break;
            default:
                $this->api_url = 'http://api.giantbomb.com';
                $this->site = 'gb';
        }

        //Define how we want our results returned with either XML or JSON.
        switch ($format)
        {
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

    function showDebug()
    {
        $this->debug = TRUE;
    }

    function enableCache($timeout = 86400)
    {
        $this->cache = TRUE;
        $this->cache_timeout = $timeout;
    }

    //Function used for a detail resource request.
    public function getDetail($resource, $id, $field_list = null)
    {
        if($this->cache)
        {
            $cache = $this->fileCacheCheck($id, $resource, $field_list);
            if (!$cache)
            {
                $rawData = file_get_contents("$this->api_url/$resource/$id/?api_key=$this->api_key&field_list=$field_list&format=$this->format");
                $result = $this->parseResponse($rawData);
                $this->fileUpdateCache($id, $resource, $field_list, $rawData);

                return $result;
            } 
            else
            {
                $result = $this->parseResponse($cache);
                return $result;
            }
        }
    }

    //Function used for a list resource request.
    public function getList($resource, $offset = 0, $limit = 100, $field_list = null)
    {
        if($this->cache)
        {
            $cache = $this->fileCacheCheck('', $resource, $field_list);
            if(!$cache)
            {
                $options = $this->buildOptions(array($field_list, null));
                $url = "$this->api_url/$resource/?api_key=$this->api_key&offset=$offset&$options&limit=$limit&format=$this->format";
                $rawData = $this->pullData($url);
                $results = $this->parseResponse($rawData);
                $this->fileUpdateCache('', $resource, $field_list, $rawData);

                return $results;
            }
            else
            {
                $results = $this->parseResponse($cache);
                return $results;
            }
        }
    }

    //Function used for searching.
    public function search($query, $resources = null, $offset = 0, $limit = 20, $field_list = null)
    {
        $options = $this->buildOptions(array($field_list, $resources));

        $rawData = file_get_contents("$this->api_url/search/?api_key=$this->api_key&query=$query&offset=$offset&$options&limit=$limit&format=$this->format");

        $results = $this->parseResponse($rawData);

        return $results;
    }

    //Function used to build our optons for pulls when needed. Some filters do not like to be leaved blank, which is part of the reason this is required.
    private function buildOptions($options)
    {
        if ($options[0] == null)
        {
            $fOptions = '';
        }
        else
        {
            $fOptions = "field_list=$options[0]&";
        }
        if ($options[1] == null)
        {
            $rOptions = '';
        }
        else
        {
            $rOptions = "resources=$options[1]&";
        }
        $oReturn = $rOptions . $fOptions;

        return $oReturn;
    }

    //Function used to parse our response from the API. Will switch depending on JSON or XML.
    private function parseResponse($raw)
    {
        if ($this->format == 'json')
        {
            $parsed = json_decode($raw);
        }
        elseif ($this->format == 'xml')
        {
            $parsed = simplexml_load_string($raw);
        }

        return $parsed;
    }

    //Pulls our data from the API, sets a timeout to it doesn't explode.
    private function pullData($url)
    {
        $context = stream_context_create(array('http' => array('timeout' => '10'
                    // Timeout in seconds
                    )));

        $contents = file_get_contents($url, 0, $context);

        return $contents;
    }

    //Output some helpful debug information (call and response)
    private function runDebug($call, $response)
    {
        echo '<pre>Call: ' . $call . '</pre>';
        echo '<pre>Response: ' . $response . '</pre>';
    }

    private function fileCacheCheck($id = 0, $resource, $field_list)
    {
        $pre_hash = $this->site.$id.$resource.$field_list;
        $file_name = md5($pre_hash);
        $cache_file = "cache/$file_name.$this->format";
        $timedif = @(time() - filemtime($cache_file));

        if (file_exists($cache_file) && $timedif < $this->cache_timeout)
        {
            return file_get_contents($cache_file);
        } 
        else
        {
            return FALSE;
        }

    }

    private function fileUpdateCache($id = 0, $resource, $field_list, $data)
    {
        $pre_hash = $this->site.$id.$resource.$field_list;
        $file_name = md5($pre_hash);
        $cache_file = "cache/$file_name.$this->format";

        if ($f = @fopen($cache_file, 'w'))
        {
            fwrite($f, $data, strlen($data));
            fclose($f);
        }
    }
}