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
 * Activity Completion Report
 *
 * @package    activity_completion_report
 * @author     Manohar
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2023 TTMS Limited
 */



require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $CFG, $DB, $_SESSION;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/activity_completion_report/class.php');
require_once($CFG->dirroot . '/local/activity_completion_report/filter_form.php');

$sort = optional_param('sort', 'num', PARAM_ALPHA);
$dir = optional_param('dir', 'ASC', PARAM_ALPHA);
$perpage = optional_param('perpage', 10, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

$data = new stdClass();
$data->users = optional_param('users', '', PARAM_RAW);
$data->courses = optional_param('courses', '', PARAM_RAW);
$data->activity = optional_param('activity', '', PARAM_RAW);
$data->startdate_enabled = optional_param('startdate_enabled', 0, PARAM_INT);
$data->enddate_enabled = optional_param('enddate_enabled', 0, PARAM_INT);
$data->startdate = optional_param('startdate', '', PARAM_RAW);
$data->enddate = optional_param('enddate', '', PARAM_RAW);

$table = new coursereport_table('uniqueid2');

$table->is_downloading($download, 'coursereport', 'testing123');
require_login();

if (!$table->is_downloading()) {
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title('Activity Completion Report');
    $PAGE->set_heading('Activity Completion Report');
    $PAGE->set_url($CFG->dirroot . '/local/activity_completion_report/coursereport.php');
    $PAGE->set_pagelayout('admin');

    $PAGE->requires->jquery();
    $PAGE->navbar->add('Activity Completion Report', "$CFG->wwwroot/local/activity_completion_report/coursereport.php");
    echo $OUTPUT->header();
}

$filterform = new local_activity_completion_report_filter_form('', array('data' => $data), 'get');

if (!$table->is_downloading()) {
    $filterform->display();
}
$wherearray = [];

$cancelurl = new moodle_url('/local/activity_completion_report/coursereport.php');
if ($filterform->is_cancelled()) {
    redirect($cancelurl);
} else if ($filterformdata = $filterform->get_data()) {
    if ($filterformdata->users) {
        $wherearray[] = "u.id IN (".$filterformdata->users.")";
    }

    if ($filterformdata->courses) {
        $wherearray[] = 'c.id in ("'.$filterformdata->courses.'")';
    }

    if ($filterformdata->activity) {
        $wherearray[] = 'cm.module in ("'.$filterformdata->activity.'")';
    }

    if ($filterformdata->startdate_enabled) {
        if ($download) {
            $filterformdata->startdate = $_SESSION['startdate'];
        } else {
            $_SESSION['startdate'] = $filterformdata->startdate;
        }
        $wherearray[] = "cmc.timemodified > $filterformdata->startdate";
    }

    if ($filterformdata->enddate_enabled) {
        if ($download) {
            $filterformdata->enddate = $_SESSION['enddate'];
        } else {
            $_SESSION['enddate'] = $filterformdata->enddate;
        }
        $wherearray[] = "cmc.timemodified < $filterformdata->enddate";
    }
    $filterform->set_data($data);
}

$wherearray = ($wherearray) ? implode(" and ", $wherearray) . " and" : '';

$params = [];

$fields = 'cmc.id, @rownum:=@rownum+1 AS num, u.firstname, u.lastname, u.email, cm.module as moduleid, cm.instance as instanceid, c.fullname AS coursename, cmc.timemodified';

$from = '{course_modules_completion} cmc
        JOIN {user} u ON u.id = cmc.userid
        JOIN {course_modules} cm ON cm.id = cmc.coursemoduleid
        JOIN {course} c ON c.id = cm.course
        JOIN (SELECT @rownum:=0) r';
$where = $wherearray . ' 1=1 AND cmc.completionstate=1';
$count = $DB->get_records_sql("SELECT $fields FROM $from WHERE $where", $params);
$table->set_sql($fields, $from, $where, $params);
$queryparams = $_GET;
$baseurl = new moodle_url('/local/activity_completion_report/coursereport.php', ['sort' => $sort, 'dir' => $dir, 'perpage' => $perpage]);
$baseurl .= '&'. http_build_query($queryparams);

$table->define_baseurl($baseurl);
if (count($count)) {
    $table->out(10, true);
} else {
    echo '<h1>Nothing to display</h1>';
}

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}