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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Define the Iomad menu items that are defined by this plugin

function local_email_menu() {
    /**
     * Start Changes
     * Hide the email menu from tenant.
     * @package core
     * @author Radiant Web Tech
     */
    return array(
        'EmailTemplates' => array(
            /*
            'category' => 'CompanyAdmin',
            'tab' => 1,
            'name' => get_string('blocktitle', 'local_email'),
            'url' => '/local/email/template_list.php',
            'cap' => 'local/email:list',
            'icondefault' => 'emailtemplates',
            'style' => 'company',
            'icon' => 'fa-inbox',
            'iconsmall' => 'fa-gear'
            */
        )
    );
    //End Changes
}
