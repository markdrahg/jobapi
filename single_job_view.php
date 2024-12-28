<?php
require_once(__DIR__ . '/../../config.php');

$jobid = required_param('id', PARAM_INT);
$job = $DB->get_record('local_jobapi_jobs', ['id' => $jobid], '*', MUST_EXIST);

// Fetch the application status for the current user
$userid = $USER->id; // Get the current logged-in user's ID
$application = $DB->get_record('local_jobapi_applications', ['user_id' => $userid, 'job_id' => $jobid]);
// Check if the user has already applied
$has_applied = $application ? true : false;

$PAGE->set_url('/local/jobapi/job_view.php', ['id' => $jobid]);
$PAGE->set_context(context_system::instance());
$PAGE->set_title($job->title);

echo $OUTPUT->header();
echo '<link rel="stylesheet" type="text/css" href="job_view.css">';  // Link the CSS file

echo html_writer::start_tag('div', ['class' => 'job-details']);

echo html_writer::tag('h1', $job->title, ['class' => 'job-title']);

echo html_writer::tag('p', 
    html_writer::tag('strong', 'Company: ') . s($job->company_name), 
    ['class' => 'job-company']
);

echo html_writer::tag('p', 
    html_writer::tag('strong', 'Location: ') . s($job->location), 
    ['class' => 'job-location']
);

echo html_writer::start_tag('div', ['class' => 'job-description']);
echo html_writer::tag('strong', 'Job Description: ');
echo format_text($job->description, FORMAT_HTML, ['noclean' => true]);
echo html_writer::end_tag('div');

echo html_writer::tag('p', 
    html_writer::tag('strong', 'Closing Date: ') . ($job->closing_date ? userdate($job->closing_date) : 'No closing date'), 
    ['class' => 'job-closing-date']
);

// Check if the user has already applied

$applyurl = new moodle_url('/local/jobapi/application.php', ['jobid' => $job->id]);

if (!$has_applied) {    
    echo html_writer::link($applyurl, 'Apply Now', ['class' => 'btn btn-apply']);
} else {
    echo html_writer::link($applyurl, 'Already an applicant', ['class' => 'btn btn-applied disabled']);
}

if (optional_param('applied', false, PARAM_BOOL)) {
    echo html_writer::tag('p', 'Your application has been received.', ['class' => 'application-status']);
}

echo html_writer::end_tag('div');
echo $OUTPUT->footer();
?>
