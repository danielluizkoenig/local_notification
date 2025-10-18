<?php

/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

use local_notification\local_notification;

defined('MOODLE_INTERNAL') || die();

function xmldb_local_notification_uninstall()
{
    global $DB;

    $dbman = $DB->get_manager();
    $backupdir = make_backup_temp_directory(local_notification::PLUGINNAME);

    $tables = [
        'notification_files',
        'notification_history',
        'notification',
        'notification_query',
    ];

    foreach ($tables as $tablename) {
        $records = $DB->get_records($tablename);
        if (!empty($records)) {
            $filepath = $backupdir . "/{$tablename}.csv";
            $fp = fopen($filepath, 'w');
            $first = reset($records);
            fputcsv($fp, array_keys((array) $first));
            foreach ($records as $record) {
                fputcsv($fp, (array) $record);
            }

            fclose($fp);
        }
        if ($dbman->table_exists($tablename)) {
            $table = new xmldb_table($tablename);
            $dbman->drop_table($table);
        }
    }

    return true;
}
