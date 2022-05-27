<?php

use Bluelabs\PHPErwin\Client;

require_once 'php-erwin.php';

// Set the API key
$api_key = 'SET_THE_KEY';
$client  = (new Client($api_key));

$response = $client->sendData([
    'type'       => 'test',
    'attributes' => [
        'name'  => 'test',
        'value' => 'test',
    ],
], [
    // 'with_request' => true, // exposes the request object
    // 'with_data'    => true, // exposes the data object
]);
var_dump($reponse);
