<?php
require_once(__DIR__ . '/../../config.php');

// Moodle's core pluginfile handler delegates file handling to the plugin's function.
file_pluginfile($_REQUEST);
