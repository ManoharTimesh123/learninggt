<?php
class studentquery_table extends table_sql {

    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('num', 'page_url', 'page_name','userid', 'description','is_rply', 'createdon');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('S No.', 'page_url', 'page_name', 'Username','Your Query','Reply', 'Raisetime');
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
    
    function col_page_url($values) {
        $url = new moodle_url($values->page_url);
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return '<a href="'.$url.'">View page</a>';
        } else {
            return '<a href="'.$url.'">View page</a>';
        }
    }

    function col_page_name($values) {
        if ($this->is_downloading()) {
            return $values->page_name;
        } else {
            return $values->page_name;
        }
    }
    
    function col_description($values) {

        global $DB, $CFG;
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->description;
        } else {
            return $values->description;
        }
    }

    function col_userid($values) {
        global $DB;
        $user = $DB->get_record_sql("SELECT m.username FROM {user} m WHERE m.id = $values->userid");
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $user->username;
        } else {
            return $user->username;
        }
    }

    function col_is_rply($values) {
        global $DB;
        // If the data is being downloaded than we don't want to show HTML.
        if ($values->is_rply) {
            $user = $DB->get_record_sql("SELECT qt.description FROM {query_text} qt WHERE qt.random_id = $values->random_id And qt.userid = 2");
            if ($this->is_downloading()) {
                return $user->description;
            } else {
                return $user->description;
            }
        } else {
            return 'No reply available';
        }
    }

    function col_createdon($values) {
        if ($this->is_downloading()) {
            return date('Y-m-d H:i:s', $values->createdon);
        } else {
            return date('Y-m-d H:i:s', $values->createdon);
        }
              
    }
}
