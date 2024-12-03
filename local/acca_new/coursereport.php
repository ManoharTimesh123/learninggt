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
 * Custom Student Activity Report
 *
 * @package    block_custom_student_activity_report
 * @author     Manohar
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2023 TTMS Limited
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $CFG, $DB;
require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/acca_new/class.php');
require_login();
$sort = optional_param('sort', 'num', PARAM_ALPHA);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);
$perpage = optional_param('perpage', 10, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

$table = new coursereport_table('uniqueid');
$table->is_downloading($download, 'index', 'testing123');
if (!$table->is_downloading()) {
    $PAGE->set_title('Course Report');
    $PAGE->set_heading('Course Report');

    $PAGE->set_pagetype('Course Report');
    $PAGE->set_title('Course Report');
    $PAGE->set_heading('Course Report');
    $PAGE->set_pagelayout('dashboard');

    $PAGE->requires->jquery();
    $PAGE->navbar->add('Course Report', "$CFG->wwwroot/local/acca_new/coursereport.php");
    echo $OUTPUT->header();
}

$params = [];

$fields = 'cmc.id, @rownum:=@rownum+1 AS num, u.firstname, u.lastname, cm.module as moduleid, cm.instance as instanceid, c.fullname AS coursename, cmc.timemodified';

$from = '{course_modules_completion} cmc
        JOIN {user} u ON u.id = cmc.userid
        JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
        JOIN {course} c ON c.id = cm.course
        JOIN (SELECT @rownum:=0) r';
$where = '1=1 AND cmc.completionstate=1';
$count = $DB->get_records_sql("SELECT $fields FROM $from WHERE $where", $params);
$table->set_sql($fields, $from, $where, $params);
$queryparams = $_GET;
$baseurl = new moodle_url('/local/acca_new/coursereport.php', ['sort' => $sort, 'dir' => $dir, 'perpage' => $perpage]);
$baseurl .= '&'. http_build_query($queryparams);

$table->define_baseurl($baseurl);
if (count($count)) {
    $table->out(10, true);
} else {
    echo '<h1>Nothing to display</h1>';
}

if (!$table->is_downloading()) {
    $queryparams = $_GET;

    echo $OUTPUT->paging_bar(count($count), $page, $perpage, $baseurl);

    echo $OUTPUT->footer();
}
