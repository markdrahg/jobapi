<?php
require_once(__DIR__ . '/../../config.php');
require_login();
require_capability('moodle/site:config', context_system::instance()); // Ensure only admins can access this page

$PAGE->set_url('/local/jobapi/delete_job.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Delete Job');
$PAGE->set_heading('Delete Job');

// Get the job_id from the URL parameter
$job_id = required_param('job_id', PARAM_INT); // Get the job ID from the URL parameter

// Ensure the job ID is valid
if (!$job_id) {
    redirect(new moodle_url('/local/jobapi/Jobs_And_Applicants_Control.php'), 'Invalid Job ID', null, \core\output\notification::NOTIFY_ERROR);
}

// Confirm the job exists
global $DB;
$job = $DB->get_record('local_jobapi_jobs', ['id' => $job_id]);

if (!$job) {
    redirect(new moodle_url('/local/jobapi/Jobs_And_Applicants_Control.php'), 'Job not found', null, \core\output\notification::NOTIFY_ERROR);
}

// Delete related applications
$DB->delete_records('local_jobapi_applications', ['job_id' => $job_id]);

// Now delete the job itself
$DB->delete_records('local_jobapi_jobs', ['id' => $job_id]);

// Redirect to the admin page with a success message
redirect(new moodle_url('/local/jobapi/Jobs_And_Applicants_Control.php'), 'Job successfully deleted', null, \core\output\notification::NOTIFY_SUCCESS);
?>
