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
 * Student Query
 *
 * @package    query
 * @author     Manohar
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2023 TTMS Limited
 */



require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $CFG, $DB, $_SESSION;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot . '/local/query/class.php');
// require_once($CFG->dirroot . '/local/query/filter_form.php');

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

$table = new studentquery_table('uniqueid2');

$table->is_downloading($download, 'studentquery', 'testing123');
require_login();

if (!$table->is_downloading()) {
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title('Student Query');
    $PAGE->set_heading('Student Query');
    $PAGE->set_url($CFG->dirroot . '/local/query/studentquery.php');
    $PAGE->set_pagelayout('admin');

    $PAGE->requires->jquery();
    $PAGE->navbar->add('Student Query', "$CFG->wwwroot/local/query/studentquery.php");
    echo $OUTPUT->header();
}

$params = [];

$fields = 'qt.id, @rownum:=@rownum+1 AS num, q.page_url, q.page_name, q.createdon, qt.description, qt.userid, q.is_rply, q.random_id';

$from = '{query} q
        JOIN {query_text} qt ON qt.random_id = q.random_id
        JOIN (SELECT @rownum:=0) r';
$where = ' 1=1 and qt.userid = '.$USER->id.'';
$count = $DB->get_records_sql("SELECT $fields FROM $from WHERE $where", $params);
$table->set_sql($fields, $from, $where, $params);
$queryparams = $_GET;
$baseurl = new moodle_url('/local/query/studentquery.php', ['sort' => $sort, 'dir' => $dir, 'perpage' => $perpage]);
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
