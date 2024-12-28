<?php
require_once(__DIR__ . '/../../config.php');
require_login();

// Ensure only admins or managers can access
require_capability('local/jobapi:managejobs', context_system::instance());

// Get the job_id from the URL parameter
$job_id = required_param('job_id', PARAM_INT);

// Fetch job details and applicant data
global $DB;
$job = $DB->get_record('local_jobapi_jobs', ['id' => $job_id], '*', MUST_EXIST);
$applicants = $DB->get_records_sql("
    SELECT u.firstname, u.lastname, u.email, a.application_date 
    FROM {local_jobapi_applications} a 
    JOIN {user} u ON a.user_id = u.id 
    WHERE a.job_id = :jobid
    ORDER BY a.application_date", ['jobid' => $job_id]);

// Load PhpSpreadsheet (install if not available)
require_once($CFG->libdir . '/phpspreadsheet/vendor/autoload.php'); // Adjust path as needed
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create new spreadsheet and set properties
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Job Applicants');

// Set the header row
$sheet->setCellValue('A1', 'Name');
$sheet->setCellValue('B1', 'Email');
$sheet->setCellValue('C1', 'Application Date');

// Populate data rows
$row = 2;
foreach ($applicants as $applicant) {
    $sheet->setCellValue('A' . $row, $applicant->firstname . ' ' . $applicant->lastname);
    $sheet->setCellValue('B' . $row, $applicant->email);
    $sheet->setCellValue('C' . $row, userdate(strtotime($applicant->application_date)));
    $row++;
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="job_applicants_' . $job_id . '.xlsx"');
header('Cache-Control: max-age=0');

// Save the spreadsheet to PHP output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit; // Stop script execution after download
?>
