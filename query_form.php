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
require_once 'lib.php';
require_once 'classes/form_query.php';

require_login();
if (!is_siteadmin()) {
    return;
}

$action = required_param('action', PARAM_ALPHANUMEXT);

$returnUrl = new moodle_url(local_notification::URL_MANAGEQUERIES);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_title(get_string('n_admin_add', local_notification::PLUGINNAME) . ' ' . format_string($course->fullname));
$urlParams['action'] = 'new';
$notificationConfig = null;

if ($action == 'edit') {
    $notification = required_param('id', PARAM_ALPHANUMEXT);
    $notificationConfig = $DB->get_record('notification_query', ['id' => $notification]);
    $urlParams['id'] = $notification;
    $urlParams['action'] = 'edit';
}


$url = new moodle_url(local_notification::URL_QUERY_FORM, $urlParams);
$mform = new form_query($url, $action, $notificationConfig);

$PAGE->set_url($url);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("nq_$action", local_notification::PLUGINNAME));
if ($mform->is_cancelled()) {
    redirect($returnUrl);
} elseif ($formdata = $mform->get_data()) {
    unset($formdata->submitbutton);
    if ($action == 'edit') {
        $formdata->id = $notification;
        $configId = $DB->update_record('notification_query', $formdata);
    } else {
        $configId = $DB->insert_record('notification_query', $formdata, $returnid = true);
    }
    if ($configId) {
        redirect($returnUrl);
    }
} else {
    $mform->display();
}
echo $OUTPUT->footer();
