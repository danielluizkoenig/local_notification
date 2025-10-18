<?php
/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
*/
defined('MOODLE_INTERNAL') || die();


$tasks = [
   [
      'classname' => 'local_notification\task\notification_task',
      'blocking' => 0,
      'minute' => '30',
      'hour' => '23',
      'day' => '*',
      'month' => '*',
      'dayofweek' => '*',
      'disabled' => 0
   ],
   [
      'classname' => 'local_notification\task\deactivate_notifications_task',
      'blocking' => 0,
      'minute' => '0',
      'hour' => '2',
      'day' => '*',
      'month' => '*',
      'dayofweek' => '*',
      'disabled' => 0
   ]
];
