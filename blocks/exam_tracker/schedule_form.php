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
 * Form to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_edit_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition () {
        global $CFG, $COURSE, $USER;

        $currentYear = date('Y');
        $currentMonth = date('n');
        $currentDay = date('j');

        $mform = $this->_form;
        $mform->addElement('text', 'name', get_string('name', 'block_exam_tracker'));
        $mform->addElement('date_selector', 'date', get_string('date', 'block_exam_tracker'), ['startyear' => date('Y')]);
        $mform->setDefault('date', array('year' => $currentYear, 'month' => $currentMonth, 'day' => $currentDay));

        $mform->addElement('textarea', 'description', get_string("description", "block_exam_tracker"), 'wrap="virtual" rows="20" cols="50"');
        $this->add_action_buttons();
    }

    /**
     * Validate incoming form data.
     * @param array $examdetails An array of
     * @param array $files
     * @return array
     */
    public function validation($examdetails, $files) {
        global $CFG, $DB;

        $errors = parent::validation($examdetails, $files);

        $examdetails = (object)$examdetails;

        // Get the current timestamp
        $currentTimestamp = time();
        
        // Compare the dates
        if ($examdetails->date <= $currentTimestamp) {
            $errors['date'] = 'The exam date is today or in the future.';
        }
        
        return $errors;
    }
}