<?php
/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
*/
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/notification:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'frontpage' => CAP_PREVENT,
            'guest' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'student' => CAP_PREVENT,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_PREVENT,
            'manager' => CAP_ALLOW,
        ]
    ],
    'local/notification:manage' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ]
    ],
];
