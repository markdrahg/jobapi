<?php
require_once(__DIR__ . '/../../config.php');

//require_login();
require_login();

// Check capabilities
require_capability('moodle/site:config', context_system::instance()); // Ensure only admins can access this.

$fileid = required_param('fileid', PARAM_INT); // File ID from the URL.

// Fetch the file
$fs = get_file_storage();
$file = $fs->get_file_by_id($fileid);  // Get the file by its ID

if ($file) {
    // Optional: Check if the file is associated with the current user or job
    
    // Serve the file as a download
    send_stored_file($file, 0, 0, true);  // 0 for no expiry, 0 for no maximum file size, true for forced download
} else {
    print_error('File not found or unavailable.');
}
