<?php
/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
*/
defined('MOODLE_INTERNAL') || die();


function xmldb_local_notification_install()
{

    global $CFG, $DB;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('notification');

    // Adding fields to table local_notification_log.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('notification_query_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('time', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    $table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 1);
    $table->add_field('days_after', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    // Adding keys to table local_notification_log.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for local_notification_log.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    $table = new xmldb_table('notification_query');

    // Adding fields to table local_notification_log.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('query', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

    // Adding keys to table local_notification_log.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for local_notification_log.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    $table = new xmldb_table('notification_history');

    // Adding fields to table local_notification_log.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('notification_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('delivered', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('timeaccess', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

    // Adding keys to table local_notification_log.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for local_notification_log.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    $table = new xmldb_table('notification_files');

    // Adding fields to table local_notification_log.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('notification_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('fileid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    // Adding keys to table local_notification_log.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

    // Conditionally launch create table for local_notification_log.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    $table->add_key('notification_id', XMLDB_KEY_FOREIGN, ['notification_id'], 'notification', ['id']);
    $table->add_key('fileid', XMLDB_KEY_FOREIGN, ['fileid'], 'files', ['id']);

    return true;
}
