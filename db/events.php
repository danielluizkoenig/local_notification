<?php
/**
 * Local notification plugin events.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => '\core\event\course_viewed',
        'callback' => 'local_notification\observer::course_viewed',
    ),
);