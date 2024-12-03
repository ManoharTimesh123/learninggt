<?php
require_once('../../config.php');

require_login();

global $DB, $OUTPUT, $USER;

$courseid = optional_param('courseid', 0, PARAM_INT);
$requestaccess = optional_param('requestaccess', 0, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_acca_new'));
$PAGE->set_heading('Acca Courses');
$PAGE->set_pagelayout('standard');
$PAGE->blocks->add_region('content');
$PAGE->requires->css('/local/acca_new/style.css');

echo $OUTPUT->header();

echo $OUTPUT->addblockbutton('content');

echo $OUTPUT->custom_block_region('content');
// Your plugin content goes here
$categoryidno = 'acca';
$category = $DB->get_record('course_categories', ['idnumber' => $categoryidno]);

$courses = $DB->get_records('course', ['category' => $category->id]);
$html = '';
$data = [];
foreach ($courses as $course) {
    $course_url = new moodle_url('/course/view.php', ['id' => $course->id]);
    $coursecontext = context_course::instance($course->id);
    $record = $DB->get_record('local_acca', ['userid' => $USER->id, 'courseid' => $course->id]);
    if ($record && $record->result) {
      $result = $record->result;
    } else {
      $result = false;
    }
    if (is_enrolled($coursecontext, $USER->id)) {
      $data[] = [
        'course_url' => $course_url,
        'course_name' => $course->fullname,
        'courseid' => $course->id,
        'enrolled' => true,
        'course_progress' => \core_completion\progress::get_course_progress_percentage($course),
        'result' => $result        
      ];
    } else {
      if ($DB->record_exists('local_acca', ['userid' => $USER->id, 'courseid' => $course->id])) {        
        $button = false;
      } else {
        $button = true;
      }

      $data[] = [
        'course_url' => $course_url,
        'courseid' => $course->id,
        'course_name' => $course->fullname,
        'requesturl' => $CFG->wwwroot . '/local/acca_new/index.php?courseid=' . $course->id . '&requestaccess=1',
        'buttondisable' => $button,
        'result' => $result
      ];
    }

}

echo '<script>
function updateresult(courseId, selected) {
  $.ajax({
    url: "result_update.php",
    type: "POST",
    data: { courseId: courseId, selected: selected},
    success: function(response) {
      if (response) {
        location.reload();
      }
    }
  });
}
</script>';

if ($requestaccess) {
  $coursename = 'Are you sure';
  $categoryidno = 'acca';
  $category = $DB->get_record('course_categories', array('idnumber' => $categoryidno));

  $formcontinue = new single_button(new moodle_url($CFG->wwwroot.'/local/acca_new/request_access.php',
                                      array('agree' => 1, 'id' => $courseid)), get_string('yes'), 'get');
  $formcancel = new single_button(new moodle_url($CFG->wwwroot.'/local/acca_new/index.php',
                                      array('id' => $category->id)), get_string('no'), 'get');
  echo $OUTPUT->confirm($coursename, $formcontinue, $formcancel);
} else {
  echo $OUTPUT->render_from_template('local_acca_new/course', ['data' => $data]);
}

echo $OUTPUT->footer();
