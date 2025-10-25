<?php

/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

namespace local_notification;

defined('MOODLE_INTERNAL') || die();

use context_course;
use stdClass;

class local_notification
{

    const PLUGINNAME = 'local_notification';
    const URL_BASE = '/local/notification/';
    const URL_COURSELIST = '/local/notification/course_list.php';
    const URL_COURSEFORM = '/local/notification/course_form.php';
    const URL_REPORT = '/local/notification/report.php';
    const URL_MANAGEQUERIES = '/local/notification/manage_queries.php';
    const URL_QUERY_FORM = '/local/notification/query_form.php';

    const TYPES = [
        'all' => 'All courses',
        'default' => 'Default courses'
    ];

    /**
     * Get a course name.
     *
     * @param int $courseid The course ID.
     * @return str|null
     */
    public function get_course_name($courseid)
    {
        $context = context_course::instance((int) $courseid, IGNORE_MISSING);
        return $context ? $context->get_context_name(false, true) : null;
    }

    /**
     * Store delivery time in history
     *
     * @param int $userid User ID
     * @param int $notificationid Notification ID
     * @return bool
     */
    public static function store_deliveredtime($userid, $notificationid)
    {
        global $DB;
        $history = new stdClass();
        $history->user_id = $userid;
        $history->notification_id = $notificationid;
        $history->delivered = time();
        return $DB->insert_record('notification_history', $history, $returnid = true);
    }

    /**
     * Get file IDs for a notification
     *
     * @param int $itemid File item ID
     * @param int $contextid Context ID
     * @return array
     */
    public static function getLocalNotificationFilesId($itemid, $contextid)
    {
        $file_ids = [];
        $fs = get_file_storage();

        $files = $fs->get_area_files(
            $contextid,
            local_notification::PLUGINNAME,
            'notification_file',
            $itemid,
            'itemid, filepath, filename',
            false
        );
        foreach ($files as $file) {
            $file_ids[] = $file->get_id();
        }

        return $file_ids;
    }

    /**
     * Get files linked to a notification
     *
     * @param int $notificationId Notification ID
     * @return array
     */
    public static function getLocalNotificationFiles($notificationId)
    {
        global $DB;
        $sql = "SELECT * FROM {notification_files} nf
                JOIN {files} f ON nf.fileid = f.id
                WHERE notification_id = ?";
        return $DB->get_records_sql($sql, [$notificationId]);
    }

    /**
     * Delete files linked to a notification
     *
     * @param int $context Course context ID
     * @param int $notificationId Notification ID
     * @return bool
     */
    public static function deleteLocalNotificationFiles($context, $notificationId)
    {
        global $DB;
        $files = local_notification::getLocalNotificationFiles($notificationId);
        if (empty($files)) {
            return false;
        }

        $itemid = (reset($files)->itemid);
        $fs = get_file_storage();

        if ($fs->delete_area_files($context, local_notification::PLUGINNAME, 'notification_file', $itemid)) {
            $DB->delete_records('notification_files', ['notification_id' => $notificationId]);
            return $itemid;
        }
        return false;
    }
}
