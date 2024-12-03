<?php
require_once('../../config.php');

require_login();

global $DB, $OUTPUT, $USER;

$courseid = optional_param('course', 0, PARAM_INT);
$userid = optional_param('user', 0, PARAM_INT);

$role = $DB->get_record('role', ['shortname' => 'student']);
$enrol = enrol_try_internal_enrol($courseid, $userid, $role->id, time());
if ($enrol) {
    $user = $DB->get_record('user', ['id' => $userid]);
    $course = $DB->get_record('course', ['id' => $courseid]);
    redirect($CFG->wwwroot. '/local/acca_new/requestlist.php', '' . $user->username . ' have been enrolled successfully in ' .$course->fullname . '!');
}
