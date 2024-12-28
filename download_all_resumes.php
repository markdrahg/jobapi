<?php
require_once(__DIR__ . '/../../config.php');
require_login();
require_capability('local/jobapi:managejobs', context_system::instance()); // Ensure only admins can access this page

$job_id = required_param('job_id', PARAM_INT); // Get the job ID from the URL parameter

global $DB, $CFG;
$fs = get_file_storage();

// Fetch all resumes for the specified job.
$sql = "SELECT a.resume, u.firstname, u.lastname 
        FROM {local_jobapi_applications} a
        JOIN {user} u ON a.user_id = u.id
        WHERE a.job_id = ?";
$resumes = $DB->get_records_sql($sql, [$job_id]);

if (empty($resumes)) {
    print_error('No resumes found for this job.');
}

// Create a temporary ZIP file.
$zip_file = $CFG->tempdir . '/resumes_job_' . $job_id . '_' . time() . '.zip';
$zip = new ZipArchive();

if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    print_error('Unable to create ZIP file.');
}

// Add each resume to the ZIP file.
foreach ($resumes as $resume) {
    $file = $fs->get_file_by_id($resume->resume);
    if ($file) {
        // Use a safe and descriptive filename.
        $filename = clean_filename($resume->firstname . '_' . $resume->lastname . '.pdf');
        $zip->addFromString($filename, $file->get_content());
    }
}

// Close the ZIP file.
$zip->close();

// Serve the ZIP file for download.
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="resumes_job_' . $job_id . '.zip"');
header('Content-Length: ' . filesize($zip_file));
readfile($zip_file);

// Remove the temporary ZIP file.
unlink($zip_file);
exit;
