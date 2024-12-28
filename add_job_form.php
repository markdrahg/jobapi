<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class local_jobapi_admin_job_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Job Title.
        $mform->addElement('text', 'title', get_string('jobtitle', 'local_jobapi'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required'), 'required', null, 'client');

        // Company Name.
        $mform->addElement('text', 'company_name', get_string('companyname', 'local_jobapi'));
        $mform->setType('company_name', PARAM_TEXT);
        $mform->addRule('company_name', get_string('required'), 'required', null, 'client');

        // Location.
        $mform->addElement('text', 'location', get_string('location', 'local_jobapi'));
        $mform->setType('location', PARAM_TEXT);
        $mform->addRule('location', get_string('required'), 'required', null, 'client');

        // Job Description.
        $mform->addElement('editor', 'description', get_string('jobdescription', 'local_jobapi'), null);
        $mform->setType('description', PARAM_RAW);
        $mform->addRule('description', get_string('required'), 'required', null, 'client');

        // Closing Date.
        $mform->addElement('date_time_selector', 'closing_date', get_string('closingdate', 'local_jobapi'), [
            'optional' => true,
        ]);
        $mform->setType('closing_date', PARAM_INT);

        // Submit and cancel buttons.
        $this->add_action_buttons(true, get_string('savejob', 'local_jobapi'));
    }
}
