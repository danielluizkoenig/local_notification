<?php
/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
*/

use local_notification\local_notification;

require_once "../../config.php";
require_once('classes/form_course.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/local/notification/lib.php');
require_once($CFG->dirroot . '/local/notification/locallib.php');

//course id
$courseid = required_param('id', PARAM_INT);
$action = required_param('action', PARAM_ALPHANUMEXT);

if (!$course = $DB->get_record("course", ["id" => $courseid])) {
    print_error('coursemisconf');
}

require_login($course);
$context = context_course::instance($course->id);

if (isloggedin() && (has_capability('local/notification:view', $context) || is_siteadmin())) {

    $returnUrl = new moodle_url(local_notification::URL_COURSELIST, ['id' => $courseid, 'action' => 'list']);
    $PAGE->set_title(get_string('n_admin_add', local_notification::PLUGINNAME) . ' ' . format_string($course->fullname));

    $PAGE->requires->js_call_amd('local_notification/notification', 'init', [$courseid, $USER->id]);

    $urlParams = ['id' => $courseid, 'action' => $action];

    $notificationConfig = null;
    if ($action == 'edit') {
        $notification = required_param('notification', PARAM_ALPHANUMEXT);
        $notificationConfig = $DB->get_record('notification', ['id' => $notification]);
        $urlParams['notification'] = $notification;
    }

    $url = new moodle_url(local_notification::URL_COURSEFORM, $urlParams);
    $mform = new \local_notification\form_course($url, $action, $notificationConfig);

    $PAGE->set_url($url);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string("n_$action", local_notification::PLUGINNAME));

    if ($mform->is_cancelled()) {
        redirect($returnUrl);
    } elseif ($formdata = $mform->get_data()) {
        unset($formdata->submitbutton);

        // Ensure days_after is set even if empty
        if (!isset($formdata->days_after)) {
            $formdata->days_after = 0;
        }

        $itemid = $formdata->content['itemid'];

        $deleteResult = local_notification::deleteLocalNotificationFiles(
            $context->id,
            $notification
        );
        if ($deleteResult) {
            $itemid = $deleteResult;
        }

        $formdata->content['text'] = file_save_draft_area_files(
            $itemid,
            $context->id,
            local_notification::PLUGINNAME,
            'notification_file',
            $itemid,
            null,
            $formdata->content['text']
        );

        $formdata->content['text'] = file_rewrite_pluginfile_urls(
            $formdata->content['text'],
            'pluginfile.php',
            $context->id,
            local_notification::PLUGINNAME,
            'notification_file',
            $itemid
        );

        $formdata->content = $formdata->content['text'];
        $formdata->course_id = $courseid;

        if ($action == 'edit') {
            $formdata->id = $notification;
            $configId = $DB->update_record('notification', $formdata);
        } else {
            $formdata->status = true;
            $configId = $DB->insert_record('notification', $formdata, $returnid = true);
        }

        if ($configId) {
            $files_id = local_notification::getLocalNotificationFilesId($itemid, $context->id);
            $notificationId = $action == 'edit' ? $notification : $configId;
            if (count($files_id) > 0) {
                foreach ($files_id as $file_id) {
                    $DB->insert_record('notification_files', ['notification_id' => $notificationId, 'fileid' => $file_id]);
                }
            }
            redirect(new moodle_url(local_notification::URL_COURSELIST, ['id' => $courseid, 'action' => 'list']));
        }
    } else {
        $mform->display();
    }

    echo $OUTPUT->footer();
} else {
    redirect('/');
}
