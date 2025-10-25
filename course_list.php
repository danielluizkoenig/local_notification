<?php

/**
 * Local notification plugin.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */


use local_notification\local_notification;
use local_notification\helper\notification_manager;

require_once "../../config.php";
require_login();
global $DB, $OUTPUT, $PAGE;


$courseid = required_param('id', PARAM_INT);
$action = optional_param('action', 'list', PARAM_ALPHANUMEXT);
$notificationid = optional_param('notification', 0, PARAM_INT);

$baseurl = new moodle_url(local_notification::URL_COURSELIST, ['id' => $courseid, 'action' => 'list']);

if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    print_error('coursemisconf');
}
require_login($course);
$context = context_course::instance($course->id);
require_login($course, false);
require_capability('local/notification:view', $context);

if ($action === 'delete' && $notificationid) {
    notification_manager::delete_notification($notificationid, $context, $baseurl);
    exit;
}

if ($action === 'toggle' && $notificationid) {
    notification_manager::toggle_notification($notificationid, $baseurl);
    exit;
}

if ($action === 'duplicate' && $notificationid) {
    notification_manager::duplicate_notification($notificationid, $baseurl);
    exit;
}

if ($action === 'details' && $notificationid) {
    $notification = notification_manager::get_notification_details($notificationid);
    if ($notification) {
        $query = str_replace(
            ['%%COURSEID%%', '%%TIME%%'],
            [$notification->course_id, $notification->time],
            $notification->query
        );

        
        $targetusers = $DB->get_records_sql($query);
        $recipient_data = [];
        foreach ($targetusers as $u) {
            $recipient_data[$u->id] = [
                'nome_completo' => $u->nome_completo,
                'data' => $u->data
            ];
        }
        
        $table = new html_table();
        $table->head = [
            get_string('student', local_notification::PLUGINNAME),
            get_string('date_days', local_notification::PLUGINNAME)
        ];
        $table->attributes['class'] = 'table table-sm';
        $table->id = 'course_notification_table';
        $table->data = [];
        
        foreach ($targetusers as $student) {
            $is_recipient = array_key_exists($student->id, $recipient_data);
            $rowclass = $is_recipient ? 'bold' : '';
            $displaydata = $is_recipient ? $recipient_data[$student->id]['data'] : '-';

            $row = new html_table_row([
                $student->nome_completo,
                $displaydata
            ]);
            $row->attributes['class'] = $rowclass;
            $table->data[] = $row;
        }

        echo html_writer::table($table);
    }

    exit;
}

$PAGE->set_url($baseurl);
$PAGE->set_title(get_string('pluginname', local_notification::PLUGINNAME) . ': ' . format_string($course->fullname));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('n_list', local_notification::PLUGINNAME));


echo html_writer::link(
    new moodle_url(local_notification::URL_COURSEFORM, ['action' => 'new', 'id' => $courseid]),
    get_string('n_add', local_notification::PLUGINNAME),
    ['class' => 'btn btn-primary mb-2']
);


$table = new html_table();
$table->head = [
    get_string('n_typenotification', local_notification::PLUGINNAME),
    get_string('n_subject', local_notification::PLUGINNAME),
    get_string('n_time', local_notification::PLUGINNAME),
    get_string('actions')
];
$table->id = 'notification_table';

$sql = "SELECT n.id, n.time, n.subject, nq.description, n.notification_query_id,
        n.status
        FROM {notification} n 
        JOIN {notification_query} nq ON nq.id = n.notification_query_id
        WHERE n.course_id = :courseid";
$params = [
    'courseid' => $courseid,
    'active' => '<span class="green">' . get_string('active', 'core') . '</span>',
    'inactive' => '<span class="red">' . get_string('inactive', 'core') . '</span>'
];

foreach ($DB->get_records_sql($sql, $params) as $register) {
    $toggle_url = new moodle_url(local_notification::URL_COURSELIST, [
        'action' => 'toggle',
        'id' => $courseid,
        'notification' => $register->id,
        'sesskey' => sesskey()
    ]);

    if ($register->status == 0) {
        $statusicon = $OUTPUT->pix_icon('i/show', get_string('enable', 'core'));
        $statuslink = html_writer::link($toggle_url, $statusicon, [
            'title' => get_string('enable'),
            'class' => 'toggle-status'
        ]);
    } else {
        $statusicon = $OUTPUT->pix_icon('i/hide', get_string('disable', 'core'));
        $statuslink = html_writer::link($toggle_url, $statusicon, [
            'title' => get_string('disable'),
            'class' => 'toggle-status'
        ]);
    }

    $actions = [
        $statuslink,
        html_writer::link(
            new moodle_url(local_notification::URL_REPORT, ['action' => 'report', 'courseid' => $courseid, 'notificationid' => $register->id]),
            $OUTPUT->pix_icon('i/report', get_string('report'))
        ),
        html_writer::link(
            new moodle_url(local_notification::URL_COURSEFORM, ['action' => 'edit', 'id' => $courseid, 'notification' => $register->id]),
            $OUTPUT->pix_icon('i/edit', get_string('update'))
        ),
        html_writer::link(
            new moodle_url(local_notification::URL_COURSELIST, ['action' => 'duplicate', 'id' => $courseid, 'notification' => $register->id]),
            $OUTPUT->pix_icon('t/copy', get_string('duplicate')),
            ['onclick' => 'return confirm(\'' . addslashes(get_string('areyousureduplicate', local_notification::PLUGINNAME)) . '\');']
        ),
        html_writer::link(
            new moodle_url(local_notification::URL_COURSELIST, ['action' => 'delete', 'id' => $courseid, 'notification' => $register->id]),
            $OUTPUT->pix_icon('i/delete', get_string('delete')),
            ['onclick' => 'return confirm(\'' . addslashes(get_string('areyousure')) . '\');']
        )
    ];

    $detail_url = new moodle_url(local_notification::URL_COURSELIST, ['action' => 'details', 'id' => $courseid, 'notification' => $register->id]);
    $subject = html_writer::link($detail_url, $register->subject, ['class' => 'modal-usuarios', 'data-toggle' => 'modal', 'data-target' => '#modal-usuarios']);

    $time = $register->notification_query_id == 5 ? userdate($course->enddate, '%d/%m/%Y') :
        $register->time . (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', substr($register->time, 0, 10)) ? '' : ' ' . get_string('n_day', local_notification::PLUGINNAME));

    $table->data[] = [$register->description, $subject, $time, implode(' ', $actions)];
}

if (empty($table->data)) {
    $table->data[] = [
        html_writer::span(get_string('nothingtodisplay', 'core'), 'text-muted'),
        '',
        '',
        '',
        ''
    ];
}

echo html_writer::table($table);
echo $OUTPUT->render_from_template(local_notification::PLUGINNAME . '/modal', []);
$PAGE->requires->js_call_amd('local_notification/notification', 'init');
echo $OUTPUT->footer();
