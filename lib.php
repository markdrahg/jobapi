<?php
function local_jobapi_extend_navigation(global_navigation $navigation) {
    global $PAGE;

    // Add the link only if the user has permission.
    if (has_capability('local/jobapi:view', $PAGE->context)) {
        $jobnode = $navigation->add(
            get_string('joblistings', 'local_jobapi'), // Use a language string
            new moodle_url('/local/jobapi/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            null,
            new pix_icon('i/career', get_string('joblistings', 'local_jobapi'))
        );
    }
}

function local_jobapi_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $DB;

    if ($filearea !== 'resume') {
        send_file_not_found(); // Ensure only 'resume' filearea is handled.
    }

    $itemid = array_shift($args); // Application ID should match 'resume' column or similar.
    $filename = array_pop($args); // Retrieve the file's name.
    $filepath = '/' . implode('/', $args) . '/'; // Remaining parts as path.

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_jobapi', $filearea, $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        send_file_not_found(); // Return error if file not found.
    }

    // Check permissions (adjust to suit your use case, e.g., course context).
    if (!has_capability('local/jobapi:view', $context)) {
        send_file_not_found();
    }

    // Serve the file to the browser.
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

