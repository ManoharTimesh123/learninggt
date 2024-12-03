<?php
require_once('../../config.php');

require_login();

global $DB, $OUTPUT, $USER;

$courseid = optional_param('id', 0, PARAM_INT);
$agree = optional_param('agree', 0, PARAM_INT);

if ($agree && $courseid) {
    $categoryidno = 'acca';
    $category = $DB->get_record('course_categories', array('idnumber' => $categoryidno));
    $courseobject = $DB->get_record('course', ['id' => $courseid]);
    if (!$DB->record_exists('local_acca', ['userid' => $USER->id, 'courseid' => $courseid])) {
        $data = new stdClass();
        $data->userid = $USER->id;
        $data->categoryid = $category->id;
        $data->status = 1;
        $data->courseid = $courseid;
        $data->timecreated = time();
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        
        $DB->insert_record('local_acca', $data);
        redirect($CFG->wwwroot . '/local/acca_new/index.php?id=' . $category->id, 'Your request for ' . $courseobject->fullname . ' has been sent successfully!');
   
    } else {
        $recordid = $DB->get_record('local_acca', ['user' => $USER->id, 'course' => $courseid]);

        $data = new stdClass();
        $data->id = $recordid->id;
        $data->categoryid = $category->id;
        $data->status = 1;
        $data->timemodified = time();
        $data->usermodified = $USER->id;
        $DB->update_record('local_acca', $data);
        redirect($CFG->wwwroot . '/local/acca_new/index.php?id=' . $category->id, 'Your request for ' . $courseobject->fullname . ' has been sent successfully!');
    }
}