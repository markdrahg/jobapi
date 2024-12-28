<?php
require_once(__DIR__ . '/../../config.php');
require_login();

// Check user capabilities.
require_capability('local/jobapi:managejobs', context_system::instance());

$PAGE->set_url('/local/jobapi/Jobs_And_Applicants_Control.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Job Applications');
$PAGE->set_heading('Job Listings and Applicants');

echo $OUTPUT->header();
echo '<link rel="stylesheet" type="text/css" href="CSS/Jobs_And_Applicants_Control.css?v=' . time() . '">';

global $DB;

// Capture search inputs.
$searchby = optional_param('searchby', 'all', PARAM_TEXT);
$searchterm = optional_param('searchterm', '', PARAM_RAW_TRIMMED);

// Base SQL query.
$sql = "SELECT 
            j.id AS job_id,
            j.title AS job_title,
            j.description AS job_description,
            j.company_name AS job_company,
            j.location AS job_location,
            j.date_posted AS post_dated,
            a.id AS application_id,
            a.user_id,
            a.resume,
            a.application_date,
            u.firstname,
            u.lastname,
            u.email
        FROM {local_jobapi_jobs} j
        LEFT JOIN {local_jobapi_applications} a ON j.id = a.job_id
        LEFT JOIN {user} u ON a.user_id = u.id 
        WHERE 1=1";  // Base condition for appending filters.

$params = [];

// Add conditions based on the search selection.
if (!empty($searchterm)) {
    $searchterm = '%' . $DB->sql_like_escape($searchterm) . '%';
    switch ($searchby) {
        case 'jobid':
            $sql .= " AND j.id LIKE ?";
            $params[] = $searchterm;
            break;
        case 'companyname':
            $sql .= " AND j.company_name LIKE ?";
            $params[] = $searchterm;
            break;
        case 'jobtitle':
            $sql .= " AND j.title LIKE ?";
            $params[] = $searchterm;
            break;
        case 'location':
            $sql .= " AND j.location LIKE ?";
            $params[] = $searchterm;
            break;
        case 'userid':
            $sql .= " AND a.user_id LIKE ?";
            $params[] = $searchterm;
            break;
        case 'username':
            $sql .= " AND (u.firstname LIKE ? OR u.lastname LIKE ?)";
            $params[] = $searchterm;
            $params[] = $searchterm;
            break;
        default: // 'all' or invalid option.
            $sql .= " AND (
                j.id LIKE ? OR 
                j.company_name LIKE ? OR 
                j.title LIKE ? OR 
                j.location LIKE ? OR 
                a.user_id LIKE ? OR 
                u.firstname LIKE ? OR 
                u.lastname LIKE ?)";
            $params = array_fill(0, 7, $searchterm);
            break;
    }
}

$sql .= " ORDER BY j.id, a.application_date";
$recordset = $DB->get_recordset_sql($sql, $params);

// Display the jobs and applicants.
$jobs = [];
foreach ($recordset as $record) {
    if (!isset($jobs[$record->job_id])) {
        $jobs[$record->job_id] = [
            'title' => $record->job_title,
            'company_name' => $record->job_company,
            'location' => $record->job_location,
            'description' => $record->job_description,
            'date_posted' => $record->post_dated,
            'applicants' => []
        ];
    }

    if (!empty($record->application_id)) {
        $jobs[$record->job_id]['applicants'][] = [
            'id' => $record->application_id,
            'name' => $record->firstname . ' ' . $record->lastname,
            'email' => $record->email,
            'resume' => $record->resume,
            'application_date' => $record->application_date
        ];
    }
}

// Calculate application counts dynamically
foreach ($jobs as $job_id => $job) {
    $applicationCounts[$job_id] = count($job['applicants']); // Dynamically count applications
}

$recordset->close();
?>

<!-- Add Button Form -->
<form action="<?php echo new moodle_url('/local/jobapi/add_job.php'); ?>" method="get">
    <button type="submit" class="btn btn-primary">Add</button>
</form>


<!-- Admin Search Form -->
<form method="GET" action="Jobs_And_Applicants_Control.php" class="search-form">
    <select name="searchby" class="search-select">
        <option value="all">All</option>
        <option value="jobid">Job ID</option>
        <option value="companyname">Company Name</option>
        <option value="jobtitle">Job Title</option>
        <option value="location">Location</option>
        <option value="userid">User ID</option>
        <option value="username">User Name</option>
    </select>
    <input type="text" name="searchterm" placeholder="Enter search term..." required class="search-input">
    <button type="submit" class="search-button">Search</button>
</form>


<!-- Display Job Listings -->
<?php
if (!empty($jobs)) {
    foreach ($jobs as $job_id => $job) {
        echo html_writer::start_div('job-block');
        echo html_writer::start_div('job-details');
        echo html_writer::tag('span', 'ID: ' . $job_id, ['class' => 'job-id']);
        echo html_writer::tag('span', 'Job: ' . format_string($job['title']), ['class' => 'job-title']);
        echo html_writer::tag('span', 'Company: ' . format_string($job['company_name']), ['class' => 'job-company']);
        echo html_writer::tag('span', 'Location: ' . format_string($job['location']), ['class' => 'job-location']);
        echo html_writer::tag('span', 'Posted on: ' . userdate(strtotime($job['date_posted'])));
        echo html_writer::tag('span', 'Applications: ' . $applicationCounts[$job_id], ['class' => 'job-applications']);
        echo html_writer::end_div();


        // Job Edit Button
        $edit_url = new moodle_url('/local/jobapi/edit_job.php', ['job_id' => $job_id]);
        echo html_writer::tag('a', 'Edit', ['href' => $edit_url, 'class' => 'edit-button']);

        // Job Delete Button
        $delete_url = new moodle_url('/local/jobapi/delete_job.php', ['job_id' => $job_id]);
        echo html_writer::tag('a', 'Delete', [
            'href' => $delete_url,
            'class' => 'delete-button',
            'onclick' => 'return confirm("Are you sure you want to delete this job?")'
        ]);

        // Job Excel Export Button
        $export_url = new moodle_url('/local/jobapi/export_excel.php', ['job_id' => $job_id]);
        echo html_writer::tag('a', 'Export to Excel', [
            'href' => $export_url,
            'class' => 'export-button'
        ]);

        // Download All Resume For A Specifc Job

        $download_all_url = new moodle_url('/local/jobapi/download_all_resumes.php', ['job_id' => $job_id]);
        echo html_writer::tag('a', 'Download All Resumes', [
            'href' => $download_all_url,
            'class' => 'download-all-resumes-button'
        ]);
        
        


        if (!empty($job['applicants'])) {
            echo '<table class="applicant-table">';
            echo '<thead><tr><th>Name</th><th>Email</th><th>Application Date</th><th>Resume</th></tr></thead><tbody>';
            foreach ($job['applicants'] as $applicant) {
                echo '<tr>';
                echo '<td>' . format_string($applicant['name']) . '</td>';
                echo '<td>' . format_string($applicant['email']) . '</td>';
                echo '<td>' . userdate(strtotime($applicant['application_date'])) . '</td>';
                echo '<td>' . (!empty($applicant['resume']) ? '<a href="' . new moodle_url('/local/jobapi/download_a_resume.php', ['fileid' => $applicant['resume']]) . '">Download</a>' : '') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo html_writer::tag('p', 'No applicants for this job.', ['class' => 'no-applicants']);
        }
        echo html_writer::end_div();
    }
} else {
    echo html_writer::tag('p', 'No job listings available.');
}
echo $OUTPUT->footer();
?>
