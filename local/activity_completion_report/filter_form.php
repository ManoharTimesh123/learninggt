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
 * local_activity_completion_report.
 *
 * @package    local_activity_completion_report
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class local_activity_completion_report_filter_form extends moodleform {
    /**
     * Form definition
     */
    public function definition () {
        global $DB;
        $mform = $this->_form;
        $data = $this->_customdata['data'];
        $users[0] = '--Select--';
        $users += $DB->get_records_sql_menu('Select u.id, u.email from {course_modules_completion} cmc
        JOIN {user} u ON u.id = cmc.userid');

        $mform->addElement('header', 'activity_completion_report', 'Activity report filter', '');

        $user = $mform->addElement('select', 'users', 'Select user', $users);
        $user->setSelected($data->users);

        $courses[0] = '--Select--';
        $courses += $DB->get_records_sql_menu('Select c.id, c.fullname from {course} c');

        $course = $mform->addElement('select', 'courses', 'Select course', $courses);
        $course->setSelected($data->courses);

        $modules[0] = '--Select--';
        $modules += $DB->get_records_sql_menu('Select m.id, m.name from {modules} m');
        $activity = $mform->addElement('select', 'activity', 'Select activity', $modules);
        $activity->setSelected($data->activity);

        $mform->addElement('date_selector', 'startdate', 'Completion from');
        if (!empty($data->startdate_enabled)) {
            $mform->setDefault('startdate', $data->startdate);
        }
        
        $mform->addElement('advcheckbox', 'startdate_enabled', get_string('enable'), '', ['class' => 'enable-date']);
        $mform->disabledIf('startdate', 'startdate_enabled', 'unchecked');

        $mform->addElement('date_selector', 'enddate', 'To date', '' );
        $mform->addElement('advcheckbox', 'enddate_enabled', get_string('enable'), '', ['class' => 'enable-date']);
        $mform->disabledIf('enddate', 'enddate_enabled', 'unchecked');
        if ($data->enddate_enabled) {
            $mform->setDefault('enddate', $data->enddate);
        }

        $this->add_action_buttons(true, 'Filter');
    }
}
