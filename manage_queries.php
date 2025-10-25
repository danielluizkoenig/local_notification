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
if (!is_siteadmin()) {
    return;
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url(local_notification::URL_MANAGEQUERIES));
$PAGE->set_title(get_string('pluginname', local_notification::PLUGINNAME));
$PAGE->set_heading(get_string('pluginname', local_notification::PLUGINNAME));

$url = $PAGE->url;
$action = optional_param('action', null, PARAM_ALPHANUMEXT);

if ($action === 'delete') {
    require_sesskey();
    $id = required_param('id', PARAM_INT);

    $result = notification_manager::delete_query($id);
    redirect($url, $result['message'], null, $result['type']);
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('n_list', local_notification::PLUGINNAME));

try {
    $table = new html_table();
    $table->head = [
        get_string('n_description', local_notification::PLUGINNAME),
        get_string('courses'),
        get_string('actions')
    ];
    $table->data = [];

    $sql = "SELECT nq.id, nq.description, COUNT(n.id) AS cursos
            FROM {notification_query} nq
            LEFT JOIN {notification} n ON nq.id = n.notification_query_id
            GROUP BY nq.id, nq.description
            ORDER BY nq.description";

    $registers = $DB->get_records_sql($sql);
    foreach ($registers as $register) {
        $actions = [
            html_writer::link(
                new moodle_url(local_notification::URL_QUERY_FORM, ['action' => 'edit', 'id' => $register->id]),
                $OUTPUT->pix_icon('i/edit', get_string('update'), 'moodle'),
                ['title' => get_string('update')]
            )
        ];

        if ($register->cursos == 0) {
            $actions[] = html_writer::link(
                new moodle_url($url, ['action' => 'delete', 'id' => $register->id, 'sesskey' => sesskey()]),
                $OUTPUT->pix_icon('i/delete', get_string('delete'), 'moodle'),
                [
                    'title' => get_string('delete'),
                    'onclick' => 'return confirm(\'' . addslashes(get_string('areyousure')) . '\');'
                ]
            );
        }

        $table->data[] = [
            format_string($register->description),
            $register->cursos,
            implode(' ', $actions)
        ];
    }

    $newquery_url = new moodle_url(local_notification::URL_QUERY_FORM, ['action' => 'new']);
    echo html_writer::link($newquery_url, get_string('add'), ['class' => 'btn btn-primary mb-2']);
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
} catch (Exception $e) {
    echo $OUTPUT->notification('Error loading plugin configurations: ' . $e->getMessage(), 'notifyproblem');
}

echo $OUTPUT->footer();
