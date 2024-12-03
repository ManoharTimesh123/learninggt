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
 * @package   block_exam_tracker
 * @copyright (c) 2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/exam_tracker/locallib.php");
class block_exam_tracker extends block_base {
    public function init() {
        $this->title = get_string('exam_tracker', 'block_exam_tracker');
    }
    public function get_content() {
        $this->content = new stdClass;
        $this->content->text = "";

        $renderable = new \block_exam_tracker\output\tracker();
        $renderer = $this->page->get_renderer('block_exam_tracker');
        $this->content->text .=custom_pre_process_html(format_text($renderer->render($renderable), FORMAT_HTML, array("noclean" => true)), $this->instance->id);
        ;

        return $this->content;
    }
    public function instance_allow_multiple() {
        return true;
    }
    public function has_config() {
        return true;
    }
    public function hide_header() {
        return false;
    }
    public function applicable_formats() {

        $allow = [];
        $allow['all'] = true;
        return $allow;
    }
}
