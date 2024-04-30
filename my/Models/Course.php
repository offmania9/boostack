<?php
namespace My\Models\Courses;
class Course extends \Boostack\Models\BaseClassTraced {
    //do not set the $id field. It is already set by default
    protected $name;
    protected $description;
    protected $date_start;
    protected $date_end;
    //do not set the created_at, last_update, last_access and deleted_at fields. They are already set by default by BaseListTraced

    protected $default_values = [
        "name" => "",
        "description" => "",
        "date_start" => NULL,
        "date_end" => NULL
    ];

    const TABLENAME = "boostack_course";

    /**
    * Constructor.
    *
    * @param mixed|null $id The ID of the object.
    */
    public function __construct($id = NULL) {
            parent::__construct($id);
            $this->soft_delete = true;
    }
}

            