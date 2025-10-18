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

if ($hassiteconfig) {


    $settings = new admin_settingpage(local_notification::PLUGINNAME, get_string('pluginname', local_notification::PLUGINNAME));

    $settings->add(new admin_setting_heading(
        'local_notification/managequeries',
        '',
        html_writer::link(
            new moodle_url(local_notification::URL_MANAGEQUERIES),
            get_string('managenotifications', local_notification::PLUGINNAME),
            ['class' => 'btn btn-secondary']
        ) . ' ' .
        html_writer::link(
            new moodle_url('/local/notification/report.php'),
            get_string('report_title', local_notification::PLUGINNAME),
            ['class' => 'btn btn-info']
        )
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_notification/enableemail',
        get_string('enableemail', local_notification::PLUGINNAME),
        get_string('enableemail_desc', local_notification::PLUGINNAME),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_notification/enablepopup',
        get_string('enablepopup', local_notification::PLUGINNAME),
        get_string('enablepopup_desc', local_notification::PLUGINNAME),
        0
    ));

    $ADMIN->add('localplugins', $settings);
}
