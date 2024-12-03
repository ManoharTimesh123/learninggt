<?php
defined('MOODLE_INTERNAL') || die();

function local_acca_extend_navigation(global_navigation $navigation) {
   global $USER, $DB;
    // Check if the user is enrolled in the ACCA course
    $courseid = 2; // Replace with the actual ACCA course ID
    $course = $DB->get_record('course', array('id' => $courseid));
     // Add navigation item only if the course exists and the user is enrolled
    if ($course && is_enrolled(context_course::instance($courseid), $USER->id)) {
        $title = 'Acca-Dashboard';
        $path = new moodle_url("/local/acca/index.php");
        $settingsnode = navigation_node::create($title,
            $path,
            navigation_node::TYPE_SETTING,
            null,
            'home',
            new pix_icon('z/dashboard', ''));
        $navigation->add_node($settingsnode);
    }
}

function local_acca_before_standard_top_of_body_html() {
    global $PAGE;
    $style = '';
    if ($PAGE->pagetype == "mod-quiz-view") {
        $style = "<style>#block-region-side-pre.block-region {
            display : none !important;
        }
        </style>";
    }
    return $style;
}





















// function local_acca_before_standard_top_of_body_html() {
//     global $DB, $USER;
//     $record = new stdClass();
//     $record->usermodified = $USER->id;
//     $record->timecreated = time();
//     $record->timemodified = time();
//     $html = "<script> 
//     function studentattendence(course, user, cmid) {
//         ".$record->course ." =  course;
//         ".$record->usermodified ." =  user;
//         ".$DB->insert_record('local_webex_attendance', $record)."
//         alert('ok');
//         console.log(course, user, cmid);
//     }
//     </script>";
//     return $html;
// }

