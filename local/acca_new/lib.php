<?php
defined('MOODLE_INTERNAL') || die();

function local_acca_new_extend_navigation(global_navigation $navigation) {
   global $USER, $DB;
    // Check if the user is enrolled in the ACCA course
    $categoryidno = 'acca'; // Replace with the actual ACCA course ID
    $category = $DB->get_record('course_categories', array('idnumber' => $categoryidno));
     // Add navigation item only if the course exists and the user is enrolled
    if ($category) {
        if (is_siteadmin()) {
            $title = 'Acca Request List';
            $path = new moodle_url("/local/acca_new/requestlist.php");
            $settingsnode = navigation_node::create($title,
                $path,
                navigation_node::TYPE_SETTING,
                null,
                'home',
                new pix_icon('z/dashboard', ''));
            $navigation->add_node($settingsnode);
        } else {
            $title = 'Acca';
            $path = new moodle_url("/local/acca_new/index.php", ['id' => $category->id]);
            $settingsnode = navigation_node::create($title,
                $path,
                navigation_node::TYPE_SETTING,
                null,
                'home',
                new pix_icon('z/dashboard', ''));
            $navigation->add_node($settingsnode);
        }
    }
}

