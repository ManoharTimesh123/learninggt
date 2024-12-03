<?php
require_once('../../config.php');

require_login();

global $DB, $OUTPUT, $USER;

$courseid = optional_param('courseid', 0, PARAM_INT);
$requestaccess = optional_param('requestaccess', 0, PARAM_INT);

$PAGE->set_context(context_system::instance());
$PAGE->set_title('requestlist');
$PAGE->set_heading('Acca Courses Request Lists');
$PAGE->set_pagelayout('standard');
// $PAGE->blocks->add_region('content');
// $PAGE->requires->css('/local/acca_new/style.css');

echo $OUTPUT->header();

// echo $OUTPUT->addblockbutton('content');

// echo $OUTPUT->custom_block_region('content');

$tableheader = [
    'S No.',
    'username',
    'course_name',
    'request_time',
    'action',
    'result'
];

$table = new html_table();

$table->head = $tableheader;

$table->attributes['class'] = 'table table-striped table-bordered table-hover';

$table->width = '100%';

$data = [];

$records = $DB->get_records('local_acca', ['status' => 1]);
$sno = 1;
if (count($records) > 0) {
  foreach ($records as $record) {
    $user = $DB->get_record('user', ['id' => $record->userid]);
    $course = $DB->get_record('course', ['id' => $record->courseid]);
    $context = context_course::instance($record->courseid, IGNORE_MISSING);
    if (!is_enrolled($context, $record->userid)) {
        $data[] = [
            $sno,
            $user->username,
            $course->fullname,
            date('Y-m-d H:i:s', $record->timemodified),
            '<a href="'. $CFG->wwwroot. '/local/acca_new/enroluser.php?course='. $course->id. '&user=' . $user->id . '">Enrol</a>',
            'Not Enrolled'
        ];
    } else {
      $record = $DB->get_record('local_acca', ['userid' => $user->id, 'courseid' => $course->id]);
      if ($record && $record->result) {
        $result = $record->result;
        $pass = '';
        $fail = '';
        $noselect = '';
        if ($record->result == 'pass') {
          $pass = 'selected';
        } else if ($record->result == 'fail') {
          $fail = 'selected';
        } else {
          $noselect = 'selected';
        }
      } else {
        $pass = '';
        $fail = '';
        $noselect = '';
        $result = false;
      }
      $data[] = [
          $sno,
          $user->username,
          $course->fullname,
          date('Y-m-d H:i:s', $record->timemodified),
          'Enrolled',
          '<select
            name="result"
            class="form-select"
            id="result"
            onchange="adminupdateresult('.$course->id.', $(this).val(), ' .$user->id. ')"
          >
            <option value="noselect" ' . $noselect . '>Select</option>
            <option value="pass" ' . $pass . '>Pass</option>
            <option value="fail"' . $fail . '>Fail</option>
          </select>'
      ];
    }
    $sno++;
  }

$table->data = $data;

$out = html_writer::table($table);
$out .= '
    <div class="modal fade customized-modal" id="personaltrainingmodelpopup" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="personaltrainingmodelpopupLabel"> Request list </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="training-detail">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close </button>
          </div>
        </div>
      </div>
    </div>
    
    <script>
    function adminupdateresult(courseId, selected, userId) {
        $.ajax({
          url: "result_update.php",
          type: "POST",
          data: { courseId: courseId, selected: selected, userId: userId },
          success: function(response) {
            if (response) {
              location.reload();
            }
          }
        });
    }
    </script>
    ';
$output = '<div class="table-responsive">' . $out . '</div>';
} else {
  $output = '<div class="alert alert-info w-100 float-left">No Record Found</div>';
}
echo $output;
echo $OUTPUT->footer();
