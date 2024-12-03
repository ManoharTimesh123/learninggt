<?php
require_once('../../config.php');

require_login();

global $DB, $OUTPUT, $USER;

$courseid = optional_param('courseId', 0, PARAM_INT);
$result = optional_param('selected', '', PARAM_RAW);
$userid = optional_param('userId', 0, PARAM_INT);

if ($courseid && $result) {
    global $DB, $USER;
    if (!$userid) {
        $userid = $USER->id;
    }

    if ($result == 'noselect') {
        $result = '';
    }

    if ($DB->record_exists('local_acca', ['userid' => $userid, 'courseid' => $courseid])) {
        $record = $DB->get_record('local_acca', ['userid' => $userid, 'courseid' => $courseid]);
        $data = new stdClass();
        $data->id = $record->id;
        $data->userid = $userid;
        $data->courseid = $courseid;
        $data->result = $result;
        $data->timemodified = time();

        $update = $DB->update_record('local_acca', $data);
        if ($update) {
            echo 'successfully!';
        } else {
            echo 'Failed!';
        }
        exit;
    } else {
        $categoryidno = 'acca';
        $category = $DB->get_record('course_categories', ['idnumber' => $categoryidno]);
        $data = new stdClass();
        $data->userid = $userid;
        $data->categoryid = $category->id;
        $data->status = 1;
        $data->courseid = $courseid;
        $data->result = $result;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->usermodified = $USER->id;

        $insert = $DB->insert_record('local_acca', $data);
        if ($insert) {
            echo 'successfully!';
        } else {
            echo 'Failed!';
        }
        exit;
    }
}