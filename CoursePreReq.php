<?php
/**
 * Created by PhpStorm.
 * User: Jacky
 * Date: 2017-09-16
 * Time: 12:07 PM
 */

// require the Composer autoloader
require __DIR__.'/vendor/autoload.php';

use UWaterlooAPI\Client;
use UWaterlooAPI\Endpoints;

// make a client
$client = new Client([
    'key' => '51fe73e9affa7afc1561fcd44f607fdc', // your API key
]);

$courses = [];

// change some client configuration options
// setConfig() will merge the provided options into the existing client configuration
$client->setConfig([
    'format' => Client::JSON,
]);

$promise = $client->request(Endpoints::COURSES_SUBJECT_CATALOG_PREREQUISITES, [
    'subject' => 'MATH',
    'catalog_number' => '137',
]);

$promise->then(
    function ($model) {
        echo $model->getMeta()['message'].PHP_EOL;
        var_dump($model->getDecodedData());
    },
    function ($error){
        echo $error->getMessage().PHP_EOL;
    }
);

?>