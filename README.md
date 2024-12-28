# Job API and Management

This plugin provides a job management system for Moodle, allowing administrators to create and manage job listings while enabling users to view and apply for positions. It includes features for job posting, application tracking, and CRUD operations for Admin.

## Features

- Job Posting: Admins can create and manage job listings, including job titles, descriptions, and application deadlines.

- User-Friendly Interface: Users can view job listings, apply for positions, and track their application status.

- Application Tracking: Admins can view and manage applicant information, including resumes and cover letters.

- CRUD Operations: Admins can perform CRUD operations on job listings, applicants, and resumes.

## Installing via uploaded ZIP file

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/jobapi

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Tables Added by the Plugin

- `mdl_local_jobapi_jobs`: stores job information
- `mdl_local_jobapi_applicants`: stores applicant information

## php files and their functions

- `add_job_form.php`: Displays a form for adding a new job listing.

- `add_job.php`: Handles the submission of the add job form and adds the job to the database.

- `application_form.php`: Displays a form for applying for a job.

- `application.php`: Handles the submission of the application form and adds the applicant to the database.

- `delete_job.php`: Deletes a job listing from the database.

- `download_a_resume.php`: Downloads a specific resume file.

- `download_all_resume.php`: Downloads all resume files.

- `edit_job.php`: Displays a form for editing a job listing.

- `export_applicants.php`: Exports a list of applicants to a CSV file.

- `index.php`: Displays a list of job listings.

- `job_list.php`: Displays a list of job listings.

- `Jobs_And_Applicants_Control.php`: Displays a list of applicants and their applications.

- `plugin.php`: Handles plugin configuration settings.

- `search_form.php`: Displays a form for searching job listings.

- `settings.php`: Displays plugin settings.

- `single_job.php`: Displays a single job listing.

## License

2024 Mark Drah <neutral520@gmail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <https://www.gnu.org/licenses/>.
