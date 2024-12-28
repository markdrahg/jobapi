<?php
$capabilities = [
    // Existing capability to manage jobs (for managers)
    'local/jobapi:managejobs' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,  // Only managers can manage jobs
        ],
    ],

    // New capability to view jobs (for admins, managers, and job seekers)
    'local/jobapi:viewjobs' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM, // You can change this if needed (e.g., CONTEXT_COURSE)
        'archetypes' => [
            'admin' => CAP_ALLOW,  // Admins can view jobs
            'manager' => CAP_ALLOW, // Managers can view jobs
            'student' => CAP_ALLOW, // Job seekers (students) can view jobs
        ],
    ],
];
