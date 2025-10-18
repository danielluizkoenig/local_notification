<?php
/**
 * Local notification plugin observer.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

namespace local_notification;

defined('MOODLE_INTERNAL') || die();

class observer {
    
    /**
     * Observer for course viewed event
     */
    public static function course_viewed(\core\event\course_viewed $event) {
        global $DB;
        
        $courseid = $event->courseid;
        $userid = $event->userid;
        $time = $event->timecreated;
        
        // Atualiza timeaccess para registros onde timeaccess é nulo
        $sql = "UPDATE {notification_history} 
                SET timeaccess = :timeaccess 
                WHERE user_id = :userid 
                  AND timeaccess IS NULL 
                  AND notification_id IN (
                      SELECT id FROM {notification} WHERE course_id = :courseid
                  )";
        
        $DB->execute($sql, [
            'timeaccess' => $time,
            'userid' => $userid,
            'courseid' => $courseid
        ]);
    }
}