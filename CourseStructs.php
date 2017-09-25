<?php
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

        $promise = $client->request(Endpoints::COURSES_SUBJECT_CATALOG_PREREQUISITES, [
            'subject' => $this->courseSubject,
            'catalog_number' => $this->courseNumber,
        ]);
        $promise->then(
            function ($model) {
                $json_model = $model->getDecodedData();
                if (isset($json_model["data"]["prerequisites_parsed"]) && !empty($json_model["data"]["prerequisites_parsed"])) {
                    foreach ($json_model["data"]["prerequisites_parsed"] as $prereqCourse) {
                        if ($prereqCourse == "1" || $prereqCourse == "2") {
                            continue;
                        }
                        if ($this->validateCourse($prereqCourse) && !in_array($prereqCourse, CourseTree::$coursesProcessed)
                            && !in_array($prereqCourse, $this->prerequisites)) {
                            array_push(CourseTree::$coursesProcessed, $prereqCourse);
                            $course = new CourseNode($prereqCourse);
                            array_push($this->prerequisites, $course);
                        } else if (is_array($prereqCourse)) {
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
    }
}