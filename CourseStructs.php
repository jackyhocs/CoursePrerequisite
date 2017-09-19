<?php
/**
 * Created by PhpStorm.
 * User: Jacky
 * Date: 2017-09-18
 * Time: 12:07 AM
 */

require __DIR__ . '/vendor/autoload.php';

use UWaterlooAPI\Client;
use UWaterlooAPI\Endpoints;

class CourseNode
{
    public $courseName;
    public $courseSubject;
    public $courseNumber;

    public $prerequisites = array();

    function intOffset($text)
    {
        preg_match('/\d/', $text, $m, PREG_OFFSET_CAPTURE);
        if (sizeof($m)) {
            return $m[0][1];
        }
        return strlen($text);
    }

    function validateCourse($courseName)
    {
        if (strlen($courseName) < 5) {
            return false;
        }
        return true;
    }

    function loadCourseInfo()
    {

        $client = new Client([
            'key' => '51fe73e9affa7afc1561fcd44f607fdc', // your API key
        ]);

        $client->setConfig([
            'format' => Client::JSON,
        ]);

        //echo "what ".$this->courseSubject." ".$this->courseNumber.PHP_EOL;
        $promise = $client->request(Endpoints::COURSES_SUBJECT_CATALOG_PREREQUISITES, [
            'subject' => $this->courseSubject,
            'catalog_number' => $this->courseNumber,
        ]);
        //echo "Wjat".PHP_EOL;

        $promise->then(
            function ($model) {
                $json_model = $model->getDecodedData();
                /*$json_model1 ='{
                            "meta": {
                            "requests": 17,
                    "timestamp": 1505854711,
                    "status": 200,
                    "message": "Request successful",
                    "method_id": 1181,
                    "method": {

                            }
                  },
                  "data": {
                            "subject": "CS",
                    "catalog_number": "246",
                    "title": "Object-Oriented Software Development",
                    "prerequisites": "Prereq: CS 146 or a grade of 60% or higher in CS 136 or 138; Honours Mathematics students only.",
                    "prerequisites_parsed": [
                                1,
                                "CS146",
                                "CS136",
                                "CS138"
                            ]
                  }
                }';
                        $json_model = json_decode($json_model1, true);*/
                //var_dump($json_model);
                //echo "dumped".PHP_EOL;
                if (isset($json_model["data"]["prerequisites_parsed"]) && !empty($json_model["data"]["prerequisites_parsed"])) {
                    //echo "What the fuck";
                    //var_dump($json_model["data"]["prerequisites_parsed"]);
                    foreach ($json_model["data"]["prerequisites_parsed"] as $prereqCourse) {
                        if ($prereqCourse == "1" || $prereqCourse == "2") {
                            continue;
                        }
                        //if it is valid and is not already processed
                        //echo $prereqCourse." IS THE PREREQ".PHP_EOL;
                        if ($this->validateCourse($prereqCourse) && !in_array($prereqCourse, CourseTree::$coursesProcessed)
                            && !in_array($prereqCourse, $this->prerequisites)) {
                            //echo "CREATING NEW NODE".PHP_EOL;
                            array_push(CourseTree::$coursesProcessed, $prereqCourse);
                            $course = new CourseNode($prereqCourse);
                            array_push($this->prerequisites, $course);

                        } else if (is_array($prereqCourse)) {
                            //echo "ARRAY FOUND. PARSING".PHP_EOL;
                            foreach ($prereqCourse as $subCourse) {
                                if ($this->validateCourse($subCourse)) {
                                    array_push(CourseTree::$coursesProcessed, $subCourse);
                                    $innerCourse = new CourseNode($subCourse);
                                    array_push($this->prerequisites, $innerCourse);
                                }
                            }
                        }
                    }
                }
            },
            function ($error) {
                echo $error->getMessage() . PHP_EOL;
                return false;
            }
        )->wait();
    }

    public function printNode()
    {
        if (isset($this->prerequisites)) {
            foreach ($this->prerequisites as $node) {
                $node->printNode();
            }
        }
        echo $this->courseName . " ";
    }

    public function __construct($courseName)
    {
        $this->courseName = $courseName;

        $subjectCharLocation = $this->intOffset($courseName);
        $this->courseSubject = substr($courseName, 0, $subjectCharLocation);
        $this->courseNumber = substr($courseName, $subjectCharLocation);

        if ($this->validateCourse($courseName)) {
            $this->loadCourseInfo();
            return true;
        } else {
            return false;
        }
    }
}

class CourseTree
{
    public static $nodes = array();

    public static $coursesProcessed = array();

    public function isEmpty()
    {
        return count(self::$nodes) == 0;
    }

    public function printTree()
    {
        foreach (self::$nodes as $courseNodes) {
            $courseNodes->printNode();
        }
        //echo "\"".$courseNodes->courseName."\"";
    }
}
/*
$client = new Client([
    'key' => '51fe73e9affa7afc1561fcd44f607fdc', // your API key
]);

$client->setConfig([
    'format' => Client::JSON,
]);*/

$courseTree = new CourseTree();

//$node = new CourseNode("CS246", $client);

$coursesYouWant = ["CS246"];
//$courseTree::$coursesProcessed[] = "CS146, CS145,CS136, CS138";

function courseParser($coursesWanted)
{
    foreach ($coursesWanted as $course) {
        $courseNode = new CourseNode($course);
        CourseTree::$nodes[] = $courseNode;

    }
}

courseParser($coursesYouWant);
$courseTree->printTree();
echo "FINISHED" . PHP_EOL;