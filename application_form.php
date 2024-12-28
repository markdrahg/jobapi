<?php
defined('MOODLE_INTERNAL') || die();

// Include necessary files and classes
require_once($CFG->libdir . '/formslib.php');

// Define the form
class local_jobapi_application_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Add a hidden field for the job ID.
        $mform->addElement('hidden', 'jobid', '');
        $mform->setType('jobid', PARAM_INT);

        // Add a text area for the user to input their cover letter.
        $mform->addElement('textarea', 'coverletter', get_string('coverletter', 'local_jobapi'), 'wrap="virtual" rows="10" cols="50"');
        $mform->setType('coverletter', PARAM_TEXT);
        $mform->addRule('coverletter', null, 'required', null, 'client');

        // Add a file picker for uploading the resume.
        $mform->addElement('filepicker', 'resume', get_string('resume', 'local_jobapi'), null, [
            'maxbytes' => 10485760, // 10MB
            'accepted_types' => ['.pdf', '.doc', '.docx']
        ]);

        // Add submit and cancel buttons.
        $this->add_action_buttons(true, get_string('submitapplication', 'local_jobapi'));
    }
}
