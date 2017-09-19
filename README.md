# CoursePrerequisite
This PHP program will output the list of chain prerequisites for a given list of desired courses. Courses are output in a post-order order by a graph traversal

#Example

//Array contains courses that we want to eventually take

$coursesYouWant= ["MATH128", "CS136"];

//Output (Read from left to right)

MATH117 MATH104 MATH127 MATH137 MATH147 MATH128

CS115 CS135 CS116 CS145 CS136
