<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

require_login();
require_capability('local/jobapi:managejobs', context_system::instance());  // Ensure user has the necessary capability

$job_id = required_param('job_id', PARAM_INT);
$PAGE->set_url('/local/jobapi/edit_job.php', ['job_id' => $job_id]);
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Edit Job Post');
$PAGE->set_heading('Edit Job');

// Fetch the job record from the database
$job = $DB->get_record('local_jobapi_jobs', ['id' => $job_id], '*', MUST_EXIST);

// Define the form class inline
class edit_job_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Job ID
        $mform->addElement('hidden', 'job_id');
        $mform->setType('job_id', PARAM_INT);

        // Job Title
        $mform->addElement('text', 'title', 'Job Title');
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', 'Job title is required', 'required', null, 'client');

        // Company Name
        $mform->addElement('text', 'company_name', 'Company Name');
        $mform->setType('company_name', PARAM_TEXT);
        $mform->addRule('company_name', 'Company name is required', 'required', null, 'client');

        // Location
        $mform->addElement('text', 'location', 'Location');
        $mform->setType('location', PARAM_TEXT);
        $mform->addRule('location', 'Location is required', 'required', null, 'client');

        // Job Description with Rich Editor
        $mform->addElement('editor', 'description', 'Job Description', null, [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true,
            'context' => context_system::instance(),
            'trusttext' => true
        ]);
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', 'Job description is required', 'required', null, 'client');

        // Date Posted (Readonly)
        $mform->addElement('date_time_selector', 'date_posted', 'Date Posted');
        $mform->disabledIf('date_posted', 'date_posted', 'eq', true);

        // Closing Date
        $mform->addElement('date_time_selector', 'closing_date', 'Closing Date', ['optional' => true]);
        $mform->setType('closing_date', PARAM_INT);

        $this->add_action_buttons(true, 'Update Job');
    }

    // Validation function
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (strlen($data['title']) < 3) {
            $errors['title'] = 'Title must be at least 3 characters long.';
        }
        if (empty($data['company_name'])) {
            $errors['company_name'] = 'Company name is required.';
        }
        if (empty($data['location'])) {
            $errors['location'] = 'Location is required.';
        }
        return $errors;
    }
}

// Initialize the form and populate it with the current job data
$form = new edit_job_form(null, ['job_id' => $job_id]);
$form->set_data([
    'job_id' => $job->id,
    'title' => $job->title,
    'company_name' => $job->company_name,
    'location' => $job->location,
    'description' => ['text' => $job->description, 'format' => FORMAT_HTML],
    'date_posted' => strtotime($job->date_posted),
    'closing_date' => $job->closing_date ? strtotime($job->closing_date) : null,
]);

// Check form submission and validation
if ($form->is_submitted() && $form->is_validated()) {
    $data = $form->get_data();
    $job->title = $data->title;
    $job->company_name = $data->company_name;
    $job->location = $data->location;
    $job->description = $data->description['text'];
    $job->closing_date = $data->closing_date ? date('Y-m-d H:i:s', $data->closing_date) : null;

    // Update the job record in the database
    $DB->update_record('local_jobapi_jobs', $job);
    redirect(new moodle_url('/local/jobapi/Jobs_And_Applicants_Control.php'), 'Job updated successfully!', null, \core\output\notification::NOTIFY_SUCCESS);
}

// Display the form and the page
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
