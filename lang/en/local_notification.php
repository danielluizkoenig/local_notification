<?php
/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Notifications';

$string['n_list'] = 'Notification list';
$string['n_add'] = 'Configure new notification';
$string['n_time'] = 'Time in days or specific date for the notification';
$string['n_time_help'] = 'You can enter dates in the format dd/mm/yyyy or a number of days. Multiple values can be entered, separated by commas, e.g.: 1,2,3 or 01/01/2000,02/01/2000';
$string['n_subject'] = 'Subject';
$string['n_description'] = 'Description';
$string['n_type'] = 'Course type';
$string['n_typenotification'] = 'Notification type';
$string['n_new'] = 'Add notification configuration';
$string['n_edit'] = 'Edit notification configuration';
$string['n_manage'] = 'Manage notification types';
$string['n_admin_add'] = 'Configure notifications';
$string['n_day'] = 'day(s)';
$string['htmlvars'] = '<div class="row"><div class="col-9 offset-3 pl-5"><b>You can use the following variables in the subject or message content:</b><br>%%ALUNO_NOME%% = student name<br>%%ALUNO_EMAIL%% = student email.</div></div>';
$string['selecttype'] = 'Select a type';
$string['nq_invalidquery'] = 'Invalid query: {$a}';
$string['nq_missing_nomecompleto'] = 'Add the column nome_completo to your query: {$a}';
$string['nq_missing_courseid'] = 'Use the variable %%COURSEID%% in your query to avoid sending notifications to users from other courses: {$a}';
$string['nq_query_required'] = 'Fill in the query field.';
$string['n_query'] = 'SQL query to be executed';
$string['nq_new'] = 'Add new notification type';
$string['nq_edit'] = 'Edit notification type';
$string['nq_htmlvars'] = "<div class='row'><div class='col-9 offset-3 pl-5'><b>Variables:</b><br>%%COURSEID%% = Course ID configured in the notification<br>%%TIME%% = Number of days configured for the notification. Can be negative for days before the event or positive for days after.<br><b>Required columns returned by the query:</b><br>id,username,CONCAT(firstname,' ',lastname) as nome_completo,email.</div></div>";
$string['non_negative_number'] = 'Please enter a valid date or a positive number.';
$string['future_date'] = 'The date cannot be earlier than today';
$string['local_notification:view'] = 'Allow listing local notification types';
$string['areyousureduplicate'] = 'Do you want to duplicate this notification?';
$string['numbers_and_symbols_only'] = 'Invalid content: only numbers or comma-separated dates are allowed.';
$string['student'] = 'Student';
$string['date_days'] = 'Date/Days';
$string['help_desc'] = 'Displays all course users; those in bold will receive the notification based on the parameters';
$string['file_big_size'] = 'The total file size cannot exceed the maximum limit of 15 MB';
$string['content_empty'] = 'The notification description cannot be empty';
$string['courseend_empty'] = '<p>This type of notification requires the course to have an end date set.</p>';
$string['n_duplicated'] = 'Notification duplicated successfully.';
$string['error_deleting_notification'] = 'Failed to delete the notification.';
$string['error_duplicating_notification'] = 'Failed to duplicate the notification.';
$string['changessaved'] = 'Changes saved successfully.';
$string['errorinuse'] = 'This query cannot be deleted because it is in use by notifications.';
$string['errordeleting'] = 'An error occurred while deleting the query.';
$string['notspecified'] = 'Not specified';
$string['areyousure'] = 'Are you sure you want to proceed?';
$string['enableemail'] = 'Enable email notifications';
$string['enableemail_desc'] = 'If enabled, notifications will be sent by email.';
$string['enablepopup'] = 'Enable pop-up notifications';
$string['enablepopup_desc'] = 'If enabled, notifications will be sent as system messages (pop-up notifications).';
$string['managenotifications'] = 'Manage queries';
$string['loadingstudents'] = 'Loading students...';
$string['errorfetchingstudents'] = 'An error occurred while fetching data! Please check if the type in days or date is correct!';
$string['days_after'] = 'Days after last notification';
$string['days_after_help'] = 'Number of days to wait after the last notification before sending a new one. Set to 0 to send immediately.';
$string['deactivate_notifications_task'] = 'Deactivate old notifications';
$string['report_title'] = 'Notifications Report by Course';
$string['notification'] = 'Notification';
$string['total_sent'] = 'Total Sent';
$string['last_sent'] = 'Last Sent';
$string['no_data'] = 'No data found';
$string['all'] = 'All';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['filter'] = 'Filter';
$string['report_details_title'] = 'Notification Details';
$string['sent_date'] = 'Sent Date';
$string['first_access_after'] = 'First Access After Notification';
$string['notification_not_found'] = 'Notification not found';
$string['no_students_found'] = 'No students found';
$string['back_to_report'] = 'Back to Report';
$string['details'] = 'Details';
$string['date_from'] = 'Date From';
$string['date_to'] = 'Date To';
$string['access_filter'] = 'Access Filter';
$string['accessed'] = 'Accessed';
$string['not_accessed'] = 'Not Accessed';
$string['export_csv'] = 'Export CSV';
$string['export_excel'] = 'Export Excel';
$string['days_difference'] = 'Days Difference';

// Privacy API strings.
$string['privacy:metadata:notification_history'] = 'Information about notifications sent to users';
$string['privacy:metadata:notification_history:user_id'] = 'The ID of the user who received the notification';
$string['privacy:metadata:notification_history:notification_id'] = 'The ID of the notification that was sent';
$string['privacy:metadata:notification_history:delivered'] = 'The timestamp when the notification was delivered';
$string['privacy:metadata:notification_history:timeaccess'] = 'The timestamp when the user first accessed after receiving the notification';
