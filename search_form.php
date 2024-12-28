<?php
require_once("$CFG->libdir/formslib.php");

class job_search_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

        // Add a text input for the search query.
        $mform->addElement('text', 'searchquery', get_string('search', 'local_jobapi'));
        $mform->setType('searchquery', PARAM_TEXT);
        $mform->setDefault('searchquery', '');

        // Add a submit button.
        $mform->addElement('submit', 'submitbutton', get_string('search', 'local_jobapi'));
    }
}
?>
