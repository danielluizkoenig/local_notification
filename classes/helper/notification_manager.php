<?php

/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

namespace local_notification\helper;

defined('MOODLE_INTERNAL') || die();

use local_notification\local_notification;
use core\output\notification;
use moodle_exception;
use moodle_url;
use context;
use context_system;

class notification_manager
{

    public static function delete_query(int $id): array
    {
        global $DB;

        if ($DB->record_exists('notification', ['notification_query_id' => $id])) {
            return [
                'success' => false,
                'message' => get_string('errorinuse', local_notification::PLUGINNAME),
                'type' => notification::NOTIFY_ERROR
            ];
        }

        try {
            $DB->delete_records('notification_query', ['id' => $id]);
            return [
                'success' => true,
                'message' => get_string('changessaved'),
                'type' => notification::NOTIFY_SUCCESS
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => get_string('errordeleting', local_notification::PLUGINNAME),
                'type' => notification::NOTIFY_ERROR
            ];
        }
    }

    public static function delete_notification(int $notificationid, context $context, moodle_url $redirecturl): void
    {
        global $DB;
        require_capability('local/notification:manage', context_system::instance());
        try {
            $transaction = $DB->start_delegated_transaction();

            $deleted_notification = $DB->delete_records('notification', ['id' => $notificationid]);
            $deleted_history = $DB->delete_records('notification_history', ['notification_id' => $notificationid]);

            local_notification::deleteLocalNotificationFiles($context->id, $notificationid);

            if ($deleted_notification && $deleted_history) {
                $transaction->allow_commit();
                redirect($redirecturl, get_string('changessaved', 'core'), null, notification::NOTIFY_SUCCESS);
            } else {
                throw new moodle_exception('error_deleting_notification', local_notification::PLUGINNAME);
            }
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->rollback($e);
            }
            redirect($redirecturl, get_string('error', 'core') . ': ' . $e->getMessage(), null, notification::NOTIFY_ERROR);
        }
    }

    public static function duplicate_notification(int $notificationid, moodle_url $redirecturl): void
    {
        global $DB;
        require_capability('local/notification:manage', context_system::instance());

        try {
            $notification = $DB->get_record('notification', ['id' => $notificationid], '*', MUST_EXIST);
            unset($notification->id);

            $newid = $DB->insert_record('notification', $notification);
            if ($newid) {
                redirect($redirecturl, get_string('n_duplicated', local_notification::PLUGINNAME, $notificationid), null, notification::NOTIFY_SUCCESS);
            } else {
                throw new moodle_exception('error_duplicating_notification', local_notification::PLUGINNAME);
            }
        } catch (\Exception $e) {
            redirect($redirecturl, get_string('error', 'core') . ': ' . $e->getMessage(), null, notification::NOTIFY_ERROR);
        }
    }

    public static function toggle_notification(int $notificationid, moodle_url $redirecturl): void
    {
        global $DB;
        require_capability('local/notification:manage', context_system::instance());

        try {
                require_sesskey();
                $notification = $DB->get_record('notification', ['id' => $notificationid], '*', MUST_EXIST);
                $notification->status = !$notification->status;
                $DB->update_record('notification', $notification);
                redirect($redirecturl);
        } catch (\Exception $e) {
            redirect($redirecturl, get_string('error', 'core') . ': ' . $e->getMessage(), null, notification::NOTIFY_ERROR);
        }
    }

    public static function get_notification_details(int $notificationid): ?object
    {
        global $DB;

        $sql = "SELECT n.id, n.time, n.subject, n.content, nq.description, nq.query, n.course_id
                FROM {notification} n
                JOIN {notification_query} nq ON nq.id = n.notification_query_id
                WHERE n.id = :notificationid";

        return $DB->get_record_sql($sql, ['notificationid' => $notificationid]);
    }
    public static function get_notification_students(object $notification): array {
        global $DB;
    
        $times_raw = array_map('trim', explode(',', $notification->time));
        $time_sql = implode(',', array_map(function ($t) {
            return is_numeric($t) ? $t : "'$t'";
        }, $times_raw));
    

        $query = str_replace(['%%COURSEID%%', '%%TIME%%'], [$notification->course_id, $time_sql], $notification->query);
        $query .= " ORDER BY data ASC, nome_completo LIMIT 100";
       
        return [$DB->get_records_sql($query), $times_raw];
    }
    public static function get_course_users($courseid) {
        global $DB;
    
        $studentrole = $DB->get_record('role', ['shortname' => 'student'], 'id', MUST_EXIST);
        $studentroleid = $studentrole->id;
    
        $sql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) AS nome_completo, u.email
                FROM {user} u
                JOIN {user_enrolments} ue ON ue.userid = u.id
                JOIN {enrol} e ON e.id = ue.enrolid
                JOIN {context} ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = 50
                JOIN {role_assignments} ra ON ra.userid = u.id AND ra.contextid = ctx.id AND ra.roleid = :roleid
                WHERE e.courseid = :courseid AND ue.status = 0";
    
        return $DB->get_records_sql($sql, [
            'courseid' => $courseid,
            'roleid' => $studentroleid
        ]);
    }
}
