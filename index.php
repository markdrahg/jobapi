<?php
require_once(__DIR__ . '/../../config.php');

$PAGE->set_url('/local/jobapi/job_list.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('joblistings', 'local_jobapi'));

echo $OUTPUT->header();
echo '<link rel="stylesheet" type="text/css" href="CSS/styles.css?v=' . time() . '">';


// Check user capability to view jobs
if (!has_capability('local/jobapi:viewjobs', context_system::instance())) {
    echo html_writer::tag('p', get_string('accessdenied', 'local_jobapi'));
    echo $OUTPUT->footer();
    exit;
}

// Display the search form
echo '<form method="GET" action="job_list.php" class="job-search-form">';
echo '<input type="text" name="search" placeholder="Search by title, company, or location" value="' . s(optional_param('search', '', PARAM_RAW)) . '">';
echo '<button type="submit">Search</button>';
echo '</form>';

// Get the search query if provided
$search_query = optional_param('search', '', PARAM_RAW);

if ($search_query) {
    $sql = "SELECT * FROM {local_jobapi_jobs}
            WHERE title LIKE :title OR company_name LIKE :company OR location LIKE :location
            ORDER BY date_posted DESC";
    $params = [
        'title' => '%' . $search_query . '%',
        'company' => '%' . $search_query . '%',
        'location' => '%' . $search_query . '%'
    ];
    $jobs = $DB->get_records_sql($sql, $params);
} else {
    $jobs = $DB->get_records('local_jobapi_jobs', null, 'date_posted DESC');
}


if ($jobs) {
    echo html_writer::start_tag('div', ['class' => 'job-listings']);

    foreach ($jobs as $job) {
        $joburl = new moodle_url('/local/jobapi/single_job_view.php', ['id' => $job->id]);
        echo html_writer::start_tag('div', ['class' => 'job-item']);
        echo html_writer::tag('h3', html_writer::link($joburl, $job->title));
        echo html_writer::tag('p', html_writer::tag('strong', 'Company: ') . s($job->company_name));
        echo html_writer::tag('p', html_writer::tag('strong', 'Location: ') . s($job->location));
        echo html_writer::tag('p', html_writer::tag('strong', 'Posted on: ') . userdate(strtotime($job->date_posted)));
        if ($job->closing_date) {
            echo html_writer::tag('p', html_writer::tag('strong', 'Closing Date: ') . userdate(strtotime($job->closing_date)));
        }
        echo html_writer::end_tag('div');
    }

    echo html_writer::end_tag('div');
} else {
    echo html_writer::tag('p', 'No job listings found.');
}

echo $OUTPUT->footer();
?>