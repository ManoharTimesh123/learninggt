<?php
defined('MOODLE_INTERNAL') || die();

function local_activity_completion_report_extend_navigation(global_navigation $navigation) {
    if (is_siteadmin()) {
        $title = 'Activity Completion Report';
        $path = new moodle_url("/local/activity_completion_report/coursereport.php");
        $settingsnode = navigation_node::create($title,
            $path,
            navigation_node::TYPE_SETTING,
            null,
            'home',
            new pix_icon('z/dashboard', ''));
        $navigation->add_node($settingsnode);
    }
}

