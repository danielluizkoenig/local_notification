<?php
/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
*/
defined('MOODLE_INTERNAL') || die();

use local_notification\local_notification;

function local_notification_extend_settings_navigation($settingsnav, $context)
{
    global $PAGE, $USER, $DB;
    $context;
    if (!$PAGE->course || $PAGE->course->id == 1) {
        return;
    }

    if (
        isloggedin() && (has_capability('local/local_notification:view', $context)) &&
        ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE))
    ) {

        $url = new moodle_url(local_notification::URL_COURSELIST, ['id' => $PAGE->course->id, 'action' => 'list']);
        $foonode = navigation_node::create(
            get_string('pluginname', local_notification::PLUGINNAME),
            $url,
            navigation_node::NODETYPE_LEAF,
            'notification',
            'notification',
            new pix_icon('i/notifications', '')
        );

        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $foonode->make_active();
        }
        $settingnode->add_node($foonode);
    }
}

function local_notification_pluginfile(
    $course,
    $cm,
    $context,
    $filearea,
    $args,
    $forcedownload,
    array $options = array()
) {
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/" . local_notification::PLUGINNAME . "/$filearea/$relativepath";
    if (!($file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

function local_notification_extend_navigation_admin($navigation) {
    global $CFG;
    
    if (has_capability('moodle/site:config', context_system::instance())) {
        $node = $navigation->find('localplugins', navigation_node::TYPE_CATEGORY);
        if ($node) {
            $node->add(
                get_string('report_title', 'local_notification'),
                new moodle_url('/local/notification/report.php'),
                navigation_node::TYPE_SETTING
            );
        }
    }
}

function local_notification_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('moodle/course:viewhiddenactivities', $context)) {
        $node = $navigation->find('coursereports', navigation_node::TYPE_CONTAINER);
        if ($node) {
            $node->add(
                get_string('report_title', 'local_notification'),
                new moodle_url('/local/notification/report.php', array('courseid' => $course->id)),
                navigation_node::TYPE_SETTING,
                null,
                'notification_report'
            );
        }
    }
}
