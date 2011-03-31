<?php

/**
 * This is an example for using the getDetail function that comes with whiskey-flask.
 * getDetail('resource', 'id','field_list')
 * The resource and id parameters are required.
 */

//Include the whiskeyFlask class.
include 'whiskeyFlask.php';

//Create an instance of our whiskeyFlask class for the Giant Bomb.
//The second parameter takes your API Key. The third is for the response format, either JSON or XML.
$gb = new whiskeyFlask('gb', 'API-KEY', 'json');

//You use getDetail to pull the detailed information you need.
//Here we are pulling Pac-Man (ID: 149), from the character resource. Then limit it to the name, deck, description fields.
$game = $gb->getDetail('character','149','name,deck,description');

//Now print our result.
print_r($game);

?>