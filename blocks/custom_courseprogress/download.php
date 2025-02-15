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
 * @package     local_edwiserreports
 * @category    admin
 * @copyright   2019 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ .'/../../config.php');
global $OUTPUT, $PAGE, $CFG, $DB;
use core_user\fields;
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot . '/blocks/custom_courseprogress/locallib.php');
require_login();

$from = optional_param('from', '', PARAM_RAW);
$to = optional_param('to', '', PARAM_RAW);
$userstate = optional_param('userstate', '', PARAM_RAW);
$courseid = optional_param('courseid', '', PARAM_RAW);
$graph = optional_param('graph', '', PARAM_RAW);
$lastday = optional_param('lastday', '', PARAM_RAW);

if (!empty($userstate)) {
    if (empty($from) || empty($to)) {
        $dates = last30daysstr();
        $from = $dates['from'];
        $to = $dates['to'];
    }

    $userids = user_lastaccess($from, $to, $userstate);
    $dataformat = 'csv';
    if ($dataformat) {
        $originfields = array('id' => 'id',
                        'username'  => 'username',
                        'email'     => 'email',
                        'firstname' => 'firstname',
                        'lastname'  => 'lastname',
                        'idnumber'  => 'idnumber',
                        'institution' => 'institution',
                        'department' => 'department',
                        'phone1'    => 'phone1',
                        'phone2'    => 'phone2',
                        'city'      => 'city',
                        'country'   => 'country',
                        'lastaccess' => 'lastaccess');
    
        $extrafields = profile_get_user_fields_with_data(0);
        $profilefields = [];
        foreach ($extrafields as $formfield) {
            $profilefields[$formfield->get_shortname()] = $formfield->get_shortname();
        }
    
        $filename = clean_filename('activeusers');
    
        $downloadusers = new ArrayObject($userids);
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $dataformat, array_merge($originfields, $profilefields), $iterator,
                function($userid, $supportshtml) use ($originfields) {
    
            global $DB;
    
            if (!$user = $DB->get_record('user', array('id' => $userid))) {
                return null;
            }
    
            $userprofiledata = array();
            foreach ($originfields as $field) {
                // Custom user profile textarea fields come in an array
                // The first element is the text and the second is the format.
                // We only take the text.
                if ($field == 'lastaccess') {
                    if(!$user->$field) {
                        $userprofiledata[$field] = 'Not Accessed';
                    } else {
                        $userprofiledata[$field] = date("Y-m-d h:i:sa", $user->$field);
                    }
                    continue;
                }
                if (is_array($user->$field)) {
                    $userprofiledata[$field] = reset($user->$field);
                } else if ($supportshtml) {
                    $userprofiledata[$field] = s($user->$field);
                } else {
                    $userprofiledata[$field] = $user->$field;
                }
            }

            // Formatting extra field if transform is true.
            $extrafields = profile_get_user_fields_with_data($userid);
            foreach ($extrafields as $field) {
                $fieldkey = $field->get_shortname();
                if ($field->is_transform_supported()) {
                    $userprofiledata[$fieldkey] = $field->display_data();
                } else {
                    $userprofiledata[$fieldkey] = $field->data;
                }
            }

            return $userprofiledata;
        });
    
        exit;
    }
}

if (!empty($courseid) && empty($graph)) {
    $allprogress = $DB->get_records_sql("SELECT u.username, up.progress, u.id FROM {local_user_progress} up join {user} u ON u.id = up.userid WHERE up.courseid = :courseid", [
        'courseid' => $courseid,
    ]);
    $data = [];
    $userids = [];
    foreach ($allprogress as $progress) {
        $userids[] = $progress->id;
        $response[$progress->username] = $progress->progress;
    }

    // $userids = get_course_access_usersid($courseid, 30);

    $dataformat = 'csv';

    if ($dataformat) {
        $originfields = array('id' => 'id',
                        'courseid' => 'courseid',
                        'coursename' => 'coursename',
                        'courselastaccess' => 'courselastaccess',
                        'courseprogress' => 'courseprogress',
                        'username'  => 'username',
                        'email'     => 'email',
                        'firstname' => 'firstname',
                        'lastname'  => 'lastname',
                        'idnumber'  => 'idnumber',
                        'institution' => 'institution',
                        'department' => 'department',
                        'phone1'    => 'phone1',
                        'phone2'    => 'phone2',
                        'city'      => 'city',
                        'country'   => 'country'
                    );
    
        $extrafields = profile_get_user_fields_with_data(0);
        $profilefields = [];
        foreach ($extrafields as $formfield) {
            $profilefields[$formfield->get_shortname()] = $formfield->get_shortname();
        }
    
        $filename = clean_filename('courselastaccessusers');
    
        $downloadusers = new ArrayObject($userids);
        $iterator = $downloadusers->getIterator();

        \core\dataformat::download_data($filename, $dataformat, array_merge($originfields, $profilefields), $iterator,
                function($userid, $supportshtml) use ($originfields, $courseid) {
    
            global $DB;
    
            if (!$user = $DB->get_record('user', array('id' => $userid))) {
                return null;
            }
            // var_dump($courseid);
            // die;
            $userprofiledata = array();
            foreach ($originfields as $field) {
                // Custom user profile textarea fields come in an array
                // The first element is the text and the second is the format.
                // We only take the text.
                if ($field == 'courseid' || $field == 'coursename' || $field == 'courselastaccess' || $field == 'courseprogress') {
                    $courseaccessdetails = get_course_access_usersid($courseid, 30, $user->id);
                    if($field == 'courselastaccess') {
                        $userprofiledata[$field] = date("Y-m-d h:i:sa", $courseaccessdetails[$field]);
                        continue;
                    }
                    if($field == 'courseprogress') {
                        $progress = $DB->get_record_sql("SELECT u.username, up.progress, u.id FROM {local_user_progress} up join {user} u ON u.id = up.userid WHERE up.courseid = :courseid and up.userid = :userid", [
                            'courseid' => $courseid,
                            'userid' => $user->id
                        ]);
                        $userprofiledata[$field] = $progress->progress;
                        continue;
                    }
                    $userprofiledata[$field] = $courseaccessdetails[$field];
                    continue;
                }
                if (is_array($user->$field)) {
                    $userprofiledata[$field] = reset($user->$field);
                } else if ($supportshtml) {
                    $userprofiledata[$field] = s($user->$field);
                } else {
                    $userprofiledata[$field] = $user->$field;
                }
            }

            // Formatting extra field if transform is true.
            $extrafields = profile_get_user_fields_with_data($userid);
            foreach ($extrafields as $field) {
                $fieldkey = $field->get_shortname();
                if ($field->is_transform_supported()) {
                    $userprofiledata[$fieldkey] = $field->display_data();
                } else {
                    $userprofiledata[$fieldkey] = $field->data;
                }
            }

            return $userprofiledata;
        });
    
        exit;
    }
}

if(!empty($courseid) && !empty($graph)) {
    $allactivity = $DB->get_records_sql("SELECT cm.id, c.fullname, m.name, COUNT(cm.module) AS modulecount
    FROM {course_modules} cm
    JOIN {modules} m ON m.id = cm.module join {course} c on c.id=cm.course
    GROUP BY cm.module , cm.course");

    $dataformat = 'csv';
    $activityid = [];
    foreach ($allactivity as $activity) {
        $activityid[] = $activity->id;
    }
    if ($dataformat) {
        $filename = clean_filename('allactivityreport');
    
        $allactivity = new ArrayObject($allactivity);
        $iterator = $allactivity->getIterator();

        $originfields = [
            'coursename' => 'coursename',
            'activityname' => 'activityname',
            'activitycount' => 'activitycount'
        ];
        \core\dataformat::download_data($filename, $dataformat, $originfields, $iterator,
                function($activity, $supportshtml) use ($originfields, $courseid) {
                    global $DB;
                    $userprofiledata = array();
                    foreach ($originfields as $field) {
                        if ($field == 'courseid' || $field == 'coursename' || $field == 'activityname' || $field == 'activitycount') {
                            $courseaccessdetails = get_course_access_usersid($courseid, 30, $user->id);
                            if($field == 'coursename') {
                                $userprofiledata[$field] = $activity->fullname;
                                continue;
                            }
                            if($field == 'activityname') {
                                $userprofiledata[$field] = $activity->name;
                                continue;
                            }
                            if($field == 'activitycount') {
                                $userprofiledata[$field] = $activity->modulecount;
                                continue;
                            }
                            $userprofiledata[$field] = $courseaccessdetails[$field];
                            continue;
                        }
                    }
                    return $userprofiledata;

                });
                exit;
    }
}