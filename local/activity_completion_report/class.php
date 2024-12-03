<?php
class coursereport_table extends table_sql {

    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('num', 'firstname', 'lastname', 'email', 'coursename', 'moduleid', 'instanceid', 'timemodified');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('S No.', 'Firstname', 'Lastname', 'Email', 'Course', 'Activity', 'Name', 'CompletionTime');
        $this->define_headers($headers);
    }

    function col_num($values) {
        
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->num;
        } else {
            return $values->num;
        }
    }
    
    function col_firstname($values) {
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->firstname;
        } else {
            return $values->firstname;
        }
    }
    
    function col_lastname($values) {
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->lastname;
        } else {
            return $values->lastname;
        }
    }

    function col_email($values) {
        if ($this->is_downloading()) {
            return $values->email;
        } else {
            return $values->email;
        }
    }
    
    function col_coursename($values) {

        global $DB, $CFG;
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->coursename;
        } else {
            return $values->coursename;
        }
    }

    function col_moduleid($values) {
        global $DB;
        $module = $DB->get_record_sql("SELECT m.name FROM {modules} m WHERE m.id = $values->moduleid");
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $module->name;
        } else {
            return $module->name;
        }
    }

    function col_instanceid($values) {
        global $DB;
        $module = $DB->get_record('modules', ['id' => $values->moduleid]);
        $modulename = $DB->get_record($module->name, ['id' => $values->instanceid]);
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $modulename->name;
        } else {
            return $modulename->name;
        }
    }

    function col_timemodified($values) {
        if ($this->is_downloading()) {
            return date('Y-m-d H:i:s', $values->timemodified);
        } else {
            return date('Y-m-d H:i:s', $values->timemodified);
        }
              
    }
}
