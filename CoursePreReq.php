<?php

// require the Composer autoloader
require __DIR__.'/vendor/autoload.php';
require_once "CourseStructs.php";
$courseTree = new CourseTree();
$coursesYouWant = ["MATH128", "CS136"];

function courseParser($coursesWanted)
{
    foreach ($coursesWanted as $course) {
        $courseNode = new CourseNode($course);
        CourseTree::$nodes[] = $courseNode;
    }
}

courseParser($coursesYouWant);
$courseTree->printTree();
echo PHP_EOL;