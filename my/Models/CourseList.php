<?php
namespace My\Models;

use Boostack\Models\BaseList;
use My\Models\Course;

class CourseList extends BaseList
{
    const BASE_CLASS = Course::class;

    public function __construct()
    {
        parent::init();
    }
}
?>