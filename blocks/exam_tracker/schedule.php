<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Plugin administration pages are defined here.
 *
 * @package     block_exam_tracker
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ .'/../../config.php');
require_once("schedule_form.php");
global $OUTPUT, $PAGE, $CFG, $DB, $USER;
$PAGE->set_title('Schedule Exam');
$PAGE->set_heading('Schedule Exam');

$PAGE->set_title('Schedule Exam');
$PAGE->set_heading('Schedule Exam');
$PAGE->set_pagelayout('dashboard');

$PAGE->requires->jquery();
$PAGE->navbar->add('Schedule Exam', "$CFG->wwwroot/blocks/exam_tracker/schedule.php");

$PAGE->requires->jquery();
require_login();

$mform = new schedule_form();
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot);
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
    $data = new stdClass();
    $data->name = $fromform->name;
    $data->date = $fromform->date;
    $data->description = $fromform->description;
    $data->usermodified = $USER->id;
    $data->timemodified = date('U');
    if ($existRecord = $DB->get_record('local_acca_exam', ['usermodified' => $USER->id])) {
        $data->id = $existRecord->id;
        $DB->update_record('local_acca_exam', $data);
        redirect($CFG->wwwroot, "Records update successfully");
    } else {
        $data->timecreated = date('U');
        $DB->insert_record('local_acca_exam', $data);
        redirect($CFG->wwwroot, "Records inserted successfully");
    }
} else {
    if ($toform = $DB->get_record('local_acca_exam', ['usermodified' => $USER->id])) {
        $mform->set_data($toform);
    }
    //displays the form
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}