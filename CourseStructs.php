<?php
/**
 * Created by PhpStorm.
 * User: Jacky
 * Date: 2017-09-18
 * Time: 12:07 AM
 */
class CourseNode {
    public $courseName;
    public $courseSubject;
    public $courseNumber;

    public $prerequisites;

    function intOffset($text) {
        preg_match('/\d/', $text, $m, PREG_OFFSET_CAPTURE);
        if (sizeof($m)) {
            return $m[0][1];
        }
        return strlen($text);
    }

    public function __construct($courseName) {
        $this->courseName = $courseName;

        $this->prerequisites = array();
    }
}

class CourseTree {
    protected $root; // the root node of our tree

    public function __construct() {
        $this->root = null;
    }

    public function isEmpty() {
        return $this->root === null;
    }
}