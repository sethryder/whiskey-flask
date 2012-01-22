<?php

/**
 * This is an example for using the getList function that comes with whiskey-flask.
 * getList('resource', 'offset','limit','field_list')
 * Only the resource field is required.
 */

//Include the whiskeyFlask class.
include 'whiskeyFlask.php';

//Create an instance of our whiskeyFlask class for the Giant Bomb.
//The second parameter takes your API Key. The third is for the response format, either JSON or XML.
$gb = new whiskeyFlask('gb', 'API-KEY', 'json');

//Enable caching. We must have a /cache folder and it must be writable by the server.
$gb->enableCache();

//Now use getList to pull a list of information.
//Here we are pulling a list of franchises. We are not using any offset, have set a limit of 10, and limiting the fields to name, deck and description.
$franchises = $gb->getList('franchises','0','10','name,deck,description');

$gb->showDebug();

//Now pring our result.
print_r($franchises);

?>