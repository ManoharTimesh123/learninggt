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
 * @package   block_custom_trendingcourses
 * @copyright (c) 2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/blocks/custom_courseprogress/locallib.php");
require_once($CFG->dirroot . "/blocks/custom_trendingcourses/renderer.php");
class block_custom_trendingcourses extends block_base {
    public function init() {
        $this->title = get_string('custom_trendingcourses', 'block_custom_trendingcourses');
    }
    public function get_content() {
        global $OUTPUT, $CFG;
        $this->content = new stdClass;
        $html = '';
        $html = renderer_trending_courses(custom_get_maximum_active_user_in_course(10));
        $templatecontext = [];
        $templatecontext['blockstyle'] = $CFG->wwwroot . '/blocks/custom_trendingcourses/style.css';

        $templatecontext['blockhtml'] = format_text($html, FORMAT_HTML, array("noclean" => true));
        $templatecontext['blockcss'] = $CFG->wwwroot . '/blocks/custom_courseprogress/stylemain.css';
        $templatecontext['blockjs'] = $CFG->wwwroot . '/blocks/custom_trendingcourses/script.js';

        $this->content->text = "";

        $this->content->text .= $OUTPUT->render_from_template('block_custom_trendingcourses/blockcontent', $templatecontext);

        return $this->content;
    }
    public function instance_allow_multiple() {
        return true;
    }
    public function has_config() {
        return true;
    }
    public function hide_header() {
        return true;
    }
    public function applicable_formats() {

        $allow = [];
        $allow['all'] = true;
        return $allow;
    }
    
}
