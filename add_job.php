<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/jobapi/add_job_form.php');

$jobid = optional_param('jobid', null, PARAM_INT); // For editing existing jobs.

$PAGE->set_url('/local/jobapi/add_job.php', ['jobid' => $jobid]);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('managejobs', 'local_jobapi'));
$PAGE->set_heading(get_string('managejobs', 'local_jobapi'));

// Check user permissions.
require_login();
require_capability('local/jobapi:managejobs', context_system::instance());


if ($jobid) {
    // Editing an existing job: fetch the record.
    $job = $DB->get_record('local_jobapi_jobs', ['id' => $jobid], '*', MUST_EXIST);
    $job->description = ['text' => $job->description, 'format' => FORMAT_HTML]; // Prepare for editor.
} else {
    $job = null;
}

// Define the form.
$mform = new local_jobapi_admin_job_form(null, $job);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobapi/Jobs_And_Applicants_Control.php'));
} else if ($data = $mform->get_data()) {
    $record = (object)[
        'title' => $data->title,
        'company_name' => $data->company_name,
        'location' => $data->location,
        'description' => $data->description['text'],
        // Convert the timestamp to MySQL DATETIME format.
        'closing_date' => $data->closing_date ? date('Y-m-d H:i:s', $data->closing_date) : null,
        'date_posted' => date('Y-m-d H:i:s'), // Current timestamp for posting date.
    ];

    if ($jobid) {
        $record->id = $jobid;
        $DB->update_record('local_jobapi_jobs', $record);
    } else {
        $DB->insert_record('local_jobapi_jobs', $record);
    }

    // Redirect to the job list page after successful submission.
    redirect(new moodle_url('/local/jobapi/Jobs_And_Applicants_Control.php', ['updated' => 1]));
}
// Render the page.
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
