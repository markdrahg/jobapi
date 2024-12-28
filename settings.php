<?php

defined('MOODLE_INTERNAL') || die();

// Add a settings page for the plugin.
if ($hassiteconfig) {
    // Add the settings for job API functionality.
    $settings = new admin_settingpage('local_jobapi', get_string('pluginname', 'local_jobapi'));

    // File upload setting (set to 10MB for resume uploads).
    $settings->add(new admin_setting_configtext(
        'local_jobapi/resume_upload_size',
        get_string('resumeuploadsize', 'local_jobapi'),
        get_string('resumeuploadsize_desc', 'local_jobapi'),
        10485760, // Default 10MB
        PARAM_INT
    ));

    // Add the settings page to the admin menu.
    $ADMIN->add('localplugins', $settings);
}
