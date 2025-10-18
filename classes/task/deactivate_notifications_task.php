<?php

/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

namespace local_notification\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use local_notification\local_notification;

class deactivate_notifications_task extends scheduled_task
{
    public function get_name()
    {
        return get_string('deactivate_notifications_task', 'local_notification');
    }

    public function execute()
    {
        global $DB;
        
        mtrace('Starting notification deactivation task...');
        
        // Busca notificações que serão inativadas para mostrar histórico
        $notifications_to_deactivate = $DB->get_records_sql(
            "SELECT n.id, n.subject, n.course_id, c.fullname as course_name
             FROM {notification} n
             JOIN {course} c ON c.id = n.course_id
             WHERE n.days_after > 0 AND n.status = 1
               AND now() > (SELECT to_timestamp(MAX(delivered) + n.days_after * 86400) 
                            FROM {notification_history} nh 
                            WHERE n.id = nh.notification_id)"
        );
        
        if (!empty($notifications_to_deactivate)) {
            mtrace('Notifications to be deactivated:');
            foreach ($notifications_to_deactivate as $notification) {
                mtrace('- ID: ' . $notification->id . ' | Course: '.$notification->course_id .' - ' . $notification->course_name . ' | Subject: ' . $notification->subject);
            }
            
            $sql = "UPDATE {notification} SET status = 0 
                    WHERE id IN (" . implode(',', array_keys($notifications_to_deactivate)) . ")";
            
            $affected_rows = $DB->execute($sql);
            mtrace('Deactivated ' . $affected_rows . ' notifications.');
        } else {
            mtrace('No notifications found to deactivate.');
        }
        
        mtrace('Notification deactivation task completed.');
    }
}