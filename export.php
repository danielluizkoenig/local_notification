<?php
/**
 * Exportação de relatórios de notificações
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */

require_once('../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->libdir . '/excellib.class.php');
require_login();

$notificationid = required_param('id', PARAM_INT);
$format = required_param('format', PARAM_ALPHA);
$datefrom_str = optional_param('datefrom', '', PARAM_TEXT);
$dateto_str = optional_param('dateto', '', PARAM_TEXT);
$datefrom = $datefrom_str ? strtotime($datefrom_str) : 0;
$dateto = $dateto_str ? strtotime($dateto_str) : 0;
$accessed = optional_param('accessed', '', PARAM_ALPHA);
$sort = optional_param('sort', 'delivered', PARAM_ALPHA);
$dir = optional_param('dir', 'desc', PARAM_ALPHA);

// Buscar dados da notificação
$notification = $DB->get_record_sql(
    "SELECT n.*, c.fullname as course_name, nq.description as type_name
     FROM {notification} n
     JOIN {course} c ON c.id = n.course_id
     LEFT JOIN {notification_query} nq ON nq.id = n.notification_query_id
     WHERE n.id = ?", [$notificationid]
);

// Buscar dados para exportação
$sql = "SELECT nh.user_id, nh.delivered, nh.timeaccess, u.firstname, u.lastname, u.email
        FROM {notification_history} nh
        JOIN {user} u ON u.id = nh.user_id
        WHERE nh.notification_id = ?";

$params = [$notificationid];

if ($datefrom > 0) {
    $sql .= " AND nh.delivered >= ?";
    $params[] = $datefrom;
}

if ($dateto > 0) {
    $sql .= " AND nh.delivered <= ?";
    $params[] = $dateto + 86399;
}

if ($accessed === 'yes') {
    $sql .= " AND nh.timeaccess IS NOT NULL";
} elseif ($accessed === 'no') {
    $sql .= " AND nh.timeaccess IS NULL";
}

// Ordenação
if ($sort === 'days_diff') {
    $sql .= " ORDER BY (nh.timeaccess - nh.delivered) " . (($dir === 'asc') ? 'ASC' : 'DESC');
} else {
    $order_field = ($sort === 'timeaccess') ? 'nh.timeaccess' : 'nh.delivered';
    $order_dir = ($dir === 'asc') ? 'ASC' : 'DESC';
    $sql .= " ORDER BY $order_field $order_dir";
}
$records = $DB->get_records_sql($sql, $params);

$filename = 'notification_report_' . $notificationid . '_' . date('Y-m-d');

if ($format === 'csv') {
    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);
    
    // Cabeçalhos
    $headers = array(
        get_string('fullname'),
        get_string('email'),
        get_string('sent_date', 'local_notification'),
        get_string('first_access_after', 'local_notification'),
        get_string('days_difference', 'local_notification')
    );
    $csvexport->add_data($headers);
    
    // Dados
    foreach ($records as $record) {
        $days_diff = '-';
        if ($record->timeaccess) {
            $diff_seconds = $record->timeaccess - $record->delivered;
            $days_diff = floor($diff_seconds / 86400);
        }
        
        $row = array(
            fullname($record),
            $record->email,
            userdate($record->delivered, '%d/%m/%Y %H:%M'),
            $record->timeaccess ? userdate($record->timeaccess, '%d/%m/%Y %H:%M') : '-',
            $days_diff
        );
        $csvexport->add_data($row);
    }
    
    $csvexport->download_file();
    
} elseif ($format === 'excel') {
    $workbook = new MoodleExcelWorkbook($filename);
    $worksheet = $workbook->add_worksheet(get_string('report_details_title', 'local_notification'));
    
    // Cabeçalhos
    $headers = array(
        get_string('fullname'),
        get_string('email'),
        get_string('sent_date', 'local_notification'),
        get_string('first_access_after', 'local_notification'),
        get_string('days_difference', 'local_notification')
    );
    
    $row = 0;
    foreach ($headers as $col => $header) {
        $worksheet->write_string($row, $col, $header);
    }
    
    // Dados
    $row = 1;
    foreach ($records as $record) {
        $days_diff = '-';
        if ($record->timeaccess) {
            $diff_seconds = $record->timeaccess - $record->delivered;
            $days_diff = floor($diff_seconds / 86400);
        }
        
        $worksheet->write_string($row, 0, fullname($record));
        $worksheet->write_string($row, 1, $record->email);
        $worksheet->write_string($row, 2, userdate($record->delivered, '%d/%m/%Y %H:%M'));
        $worksheet->write_string($row, 3, $record->timeaccess ? userdate($record->timeaccess, '%d/%m/%Y %H:%M') : '-');
        $worksheet->write_string($row, 4, $days_diff);
        $row++;
    }
    
    $workbook->close();
}