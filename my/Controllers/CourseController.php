<?php
namespace My\Controllers;

use DateTime;
use DateInterval;
use My\Models\Course;
use My\Models\CourseList;

class CourseController extends \My\Controller
{
    /**
     * Entry point for course-related operations.
     */
    public static function init()
    {
        echo "This is the courses page\n\n";

        // Step 1: Create and save a new random course
        $course = self::createRandomCourse();

        // Step 2: Print all courses
        self::printAllCourses();

        // Step 3: Print only courses with date_start after 2024
        self::printCoursesStartingAfter(2024);
    }

    /**
     * Creates and saves a random course instance.
     */
    private static function createRandomCourse(): Course
    {
        echo "Creating a new Course...\n";

        // Random course metadata
        $course_code = rand(1, 10000);
        $course_name = "Test Course code: $course_code";
        $course_description = "Lorem ipsum: $course_code";

        // Random start date generation
        $start_date = new DateTime();
        $start_date->setDate(rand(2021, 2026), rand(1, 12), rand(1, 28));
        $start_date->setTime(0, 0, 0);

        // Calculate end date: at least 30 days later
        $end_date = clone $start_date;
        $end_date->add(new DateInterval("P" . (30 + rand(0, 90)) . "D"));

        // Format to string for DB storage
        $formatted_start = $start_date->format("Y-m-d H:i:s");
        $formatted_end = $end_date->format("Y-m-d H:i:s");

        // Output course info
        echo "Course created:\n";
        echo " - Name: $course_name\n";
        echo " - Description: $course_description\n";
        echo " - Start Date: $formatted_start\n";
        echo " - End Date: $formatted_end\n\n";

        // Create and persist course
        $course = new Course();
        $course->name = $course_name;
        $course->description = $course_description;
        $course->date_start = $formatted_start;
        $course->date_end = $formatted_end;   
        $course->save();

        return $course;
    }

    /**
     * Loads and prints all courses.
     */
    private static function printAllCourses(): void
    {
        echo "Listing all courses:\n";

        $courseList = new CourseList();
        $courseList->loadAll();

        if ($courseList->size() === 0) {
            echo "No courses found.\n\n";
            return;
        }

        foreach ($courseList as $course) {
            self::printCourse($course);
        }

        echo "\n";
    }

    /**
     * Loads and prints all courses that start after a given year.
     */
    private static function printCoursesStartingAfter(int $year): void
    {
        echo "Listing courses with start date after $year:\n";

        $courseList = new CourseList();
        $filter = [["date_start", ">", "$year-12-31 23:59:59"]];
        $courseList->view($filter, "name", "asc");

        if ($courseList->size() === 0) {
            echo "No matching courses found.\n\n";
            return;
        }

        foreach ($courseList as $course) {
            self::printCourse($course);
        }

        echo "\n";
    }

    /**
     * Prints course details.
     */
    private static function printCourse(Course $course): void
    {
        echo "----------------------------------\n";
        echo "Name: " . $course->name . "\n";
        echo "Description: " . $course->description . "\n";
        echo "Start Date: " . $course->date_start . "\n";
        echo "End Date: " . $course->date_end . "\n";
        echo "JSON Format: " . json_encode($course, JSON_PRETTY_PRINT) . "\n";
        echo "----------------------------------\n";
    }
}
?>
