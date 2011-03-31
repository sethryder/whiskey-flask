<?php

/**
 * This is an example for using the search function that comes with whiskey-flask.
 * search('query', 'resources','offset','limi','field_list')
 * Only the query field is required.
 */

//Include the whiskeyFlask class.
include 'whiskeyFlask.php';

//Create an instance of our whiskeyFlask class for the Giant Bomb.
//The second parameter takes your API Key. The third is for the response format, either JSON or XML.
$gb = new whiskeyFlask('gb', 'API-KEY', 'json');

//Now use the search function.
//Here we are searching for the word 'star' in the games resource. We are not using any offset, have set a limit of 5. 
//We are not limiting the field list so it is omitted.
$search = $gb->search('star','game','0','5');

//Now pring our result.
print_r($search);

?>