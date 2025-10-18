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

require_once($CFG->libdir . '/formslib.php');

class form_query extends moodleform
{
    public $action;
    public $notificationQuery;

    function __construct($actionurl, $action, $notificationQuery)
    {
        $this->action = $action;
        $this->notificationQuery = $notificationQuery;
        parent::__construct($actionurl);
    }
    public function definition()
    {
        $mform = $this->_form;

        if ($this->action == 'edit') {
            $mform->setDefault('description', $this->notificationQuery->description);
            $mform->setDefault('query', $this->notificationQuery->query);
        }

        $mform->addElement(
            'text',
            'description',
            get_string('n_description', local_notification::PLUGINNAME),
            ['maxlength' => 255, 'size' => 255]
        );
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', get_string('required'), 'required');
        $mform->addElement('html', get_string('nq_htmlvars', local_notification::PLUGINNAME));
        $mform->addElement(
            'textarea',
            'query',
            get_string('n_query', local_notification::PLUGINNAME),
            ['rows' => '15', 'cols' => '50', 'autosave' => 'false']
        );
        $mform->addRule('query', get_string('required'), 'required');

        $this->add_action_buttons(true,  get_string('submit'));
    }


    function validation($data, $files)
    {
        $errors = [];
        $query = $data['query'];
        if (!empty($query)) {
            if (strpos($query, '%%COURSEID%%')) {
                if (strpos($query, 'nome_completo')) {
                    $query = str_replace("%%COURSEID%%", 1, $query);
                    $query = str_replace("%%TIME%%", 1, $query);
                    try {
                        global $DB;
                        $DB->get_records_sql($query);
                    } catch (Exception $e) {
                        $errors['query'] = get_string('nq_invalidquery', local_notification::PLUGINNAME, $query);
                    }
                } else {
                    $errors['query'] = get_string('nq_missing_nomecompleto', local_notification::PLUGINNAME, $query);
                }
            } else {
                $errors['query'] = get_string('nq_missing_courseid', local_notification::PLUGINNAME, $query);
            }
        } else {
            $errors['query'] = get_string('nq_query_required', local_notification::PLUGINNAME);
        }

        return $errors;
    }
}
