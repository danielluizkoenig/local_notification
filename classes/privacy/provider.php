<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Privacy Subsystem implementation for local_notification.
 *
 * @package    local_notification
 * @copyright  2025 SENAI Soluções Digitais - SC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_notification\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for local_notification.
 *
 * @copyright  2025 SENAI Soluções Digitais - SC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_database_table(
            'notification_history',
            [
                'user_id' => 'privacy:metadata:notification_history:user_id',
                'notification_id' => 'privacy:metadata:notification_history:notification_id',
                'delivered' => 'privacy:metadata:notification_history:delivered',
                'timeaccess' => 'privacy:metadata:notification_history:timeaccess',
            ],
            'privacy:metadata:notification_history'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {course} c ON c.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {notification} n ON n.course_id = c.id
                  JOIN {notification_history} nh ON nh.notification_id = n.id
                 WHERE nh.user_id = :userid";

        $contextlist->add_from_sql($sql, [
            'contextlevel' => CONTEXT_COURSE,
            'userid' => $userid
        ]);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        $sql = "SELECT nh.user_id
                  FROM {notification} n
                  JOIN {notification_history} nh ON nh.notification_id = n.id
                 WHERE n.course_id = :courseid";

        $userlist->add_from_sql('user_id', $sql, ['courseid' => $context->instanceid]);
    }

    /**
     * Export personal data for the given approved_contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_COURSE) {
                continue;
            }

            $sql = "SELECT nh.id, nh.delivered, nh.timeaccess, n.subject
                      FROM {notification} n
                      JOIN {notification_history} nh ON nh.notification_id = n.id
                     WHERE n.course_id = :courseid AND nh.user_id = :userid";

            $records = $DB->get_records_sql($sql, [
                'courseid' => $context->instanceid,
                'userid' => $user->id
            ]);

            if (!empty($records)) {
                $data = [];
                foreach ($records as $record) {
                    $data[] = [
                        'subject' => $record->subject,
                        'delivered' => transform::datetime($record->delivered),
                        'timeaccess' => $record->timeaccess ? transform::datetime($record->timeaccess) : null,
                    ];
                }

                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_notification')],
                    (object) ['notifications' => $data]
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $sql = "DELETE FROM {notification_history}
                 WHERE notification_id IN (
                    SELECT id FROM {notification} WHERE course_id = :courseid
                 )";

        $DB->execute($sql, ['courseid' => $context->instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_COURSE) {
                continue;
            }

            $sql = "DELETE FROM {notification_history}
                     WHERE user_id = :userid
                       AND notification_id IN (
                        SELECT id FROM {notification} WHERE course_id = :courseid
                       )";

            $DB->execute($sql, [
                'userid' => $user->id,
                'courseid' => $context->instanceid
            ]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $sql = "DELETE FROM {notification_history}
                 WHERE user_id {$usersql}
                   AND notification_id IN (
                    SELECT id FROM {notification} WHERE course_id = :courseid
                   )";

        $params = array_merge($userparams, ['courseid' => $context->instanceid]);
        $DB->execute($sql, $params);
    }
}