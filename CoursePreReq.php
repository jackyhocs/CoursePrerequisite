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
use CourseStructs;

// make a client
$client = new Client([
    'key' => '51fe73e9affa7afc1561fcd44f607fdc', // your API key
]);

$coursesYouWant = ["CS246", "MATH137", "CHINA120R"];
$courseSubject = array();
$courseCode = array();
$coursesToTake = array();

function courseParser($coursesWanted){
    foreach($coursesWanted as $course){
        //$Sub = course first part
        //$Code = course second part

        array_push($courseSubject, $firstThing);
        array_push($courseCode, $secondThing);
    }
}
/*
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
//        print_r("Courses to take: " . $coursesToTake, true);
        var_dump($model->getDecodedData());
    },
    function ($error){
        echo $error->getMessage().PHP_EOL;
    }
);