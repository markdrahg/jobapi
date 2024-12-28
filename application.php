<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

// Check user capabilities.
$jobid = required_param('jobid', PARAM_INT);

// Fetch the job details.
$job = $DB->get_record('local_jobapi_jobs', ['id' => $jobid], '*', MUST_EXIST);

// Set up the page.
$PAGE->set_url('/local/jobapi/application.php', ['jobid' => $jobid]);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('applytojob', 'local_jobapi'));


// Define the application form.
class job_application_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Hidden field for the job ID.
        $mform->addElement('hidden', 'jobid', $this->_customdata['jobid']);
        $mform->setType('jobid', PARAM_INT);


        // Resume file picker.
        $mform->addElement('filepicker', 'resume', get_string('resume', 'local_jobapi'), null, [
            'maxbytes' => 10485760, // 10MB limit
            'accepted_types' => ['.pdf', '.doc', '.docx']
        ]);
        $mform->addRule('resume', get_string('required'), 'required', null, 'client');

        // Submit button.
        $this->add_action_buttons(true, get_string('submitapplication', 'local_jobapi'));
    }
}

// Handle form submission.
$mform = new job_application_form(null, ['jobid' => $jobid]);

if ($mform->is_cancelled()) {
    // Redirect if the form is canceled.
    redirect(new moodle_url('/local/jobapi/index.php', ['id' => $jobid]));
} else if ($data = $mform->get_data()) {
    // Insert initial application data.
    $application_data = [
        'job_id' => $data->jobid,
        'user_id' => $USER->id,
        'resume' => '', // Placeholder until file is saved.
        'application_date' => date('Y-m-d H:i:s')
    ];
    $application_id = $DB->insert_record('local_jobapi_applications', $application_data);

    // Handle resume upload.
    $draftitemid = file_get_submitted_draft_itemid('resume');
    if ($draftitemid) {
        file_save_draft_area_files(
            $draftitemid,
            context_user::instance($USER->id)->id, // User context.
            'local_jobapi', // Component.
            'resume', // File area.
            $application_id, // Item ID.
            ['subdirs' => 0, 'maxfiles' => 1]
        );

        // Retrieve the first saved file.
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            context_user::instance($USER->id)->id,
            'local_jobapi',
            'resume',
            $application_id,
            'timecreated',
            false
        );
        if ($file = reset($files)) {
            // Update the application record with the file ID instead of content hash.
            $DB->set_field('local_jobapi_applications', 'resume', $file->get_id(), ['id' => $application_id]);
        }
    }


    // Redirect to a confirmation or job view page.
    redirect(
        new moodle_url('/local/jobapi/index.php', ['id' => $jobid, 'applied' => 1]),
        get_string('applicationsubmitted', 'local_jobapi'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();

// Display the job title and form.
echo html_writer::tag('h1', get_string('applytojob', 'local_jobapi') . ': ' . format_string($job->title));
$mform->display();

echo $OUTPUT->footer();
