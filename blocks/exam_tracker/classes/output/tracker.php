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
 * Class containing data for my overview block.
 *
 * @package    block_exam_tracker
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_exam_tracker\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;
/**
 * Class containing data for my overview block.
 *
 * @copyright  2018 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tracker implements renderable, templatable {

    public function __construct(){
        // $thihiis->data = new stdClass();
    }
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array Context variables for the template
     * @throws \coding_exception
     *
     */
    public function export_for_template(renderer_base $output) {
        global $DB, $CFG, $USER;
        if ($data = $DB->get_record('local_acca_exam', ['usermodified' => $USER->id])) {
            $currentDate = new \DateTime();  // Current date
            $targetDate = new \DateTime("@$data->date");  // Target date from the timestamp
            
    
            // Calculate the difference
            $interval = $currentDate->diff($targetDate);
    
            // Get the remaining months and days
            $remainingMonths = sprintf('%02d',$interval->format('%m'));
            $remainingDays = sprintf('%02d',$interval->format('%d'));
    
            $disabled = "disabled";
        } else {
            $remainingMonths = "";
            $remainingDays = "";
            $disabled = "";
        }
        return ['remainingMonths' => $remainingMonths, 'remainingDays' => $remainingDays, 'disabled' => $disabled];
    }
}