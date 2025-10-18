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

require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir . '/moodlelib.php';

/**
 * Copia um arquivo da área permanente para a área draft
 *
 * @param int $contextId é o contexto do usuário
 * @param int $contextCourse é o contexto do curso
 * @param int $draftitemid é um id draft nunca usado ou null
 * @param int $draftitemid é o id do item permanente
 * @return void
 */
function copyPermanentFileToDraftArea($contextUser, $contextCourse, $file, $itemid, $draftitemid = null) {
    $fs = get_file_storage();
    $filename = $file->get_filename();
    $oldfile = $fs->get_file(
        $contextCourse,
        local_notification::PLUGINNAME,
        'notification_file',
        $draftitemid ?? $itemid,
        '/',
        $filename
    );

    $newfileinfo = array(
        'component' => 'user',
        'filearea' => 'draft',
        'itemid' => $itemid,
        'contextid' => $contextUser,
        'filepath' => '/',
        'filename' => $filename
    );
    $newcontextid = $newfileinfo['contextid'];
    $newcomponent = $newfileinfo['component'];
    $newfilearea = $newfileinfo['filearea'];
    $newitemid = $newfileinfo['itemid'];
    $newfilepath = $newfileinfo['filepath'];
    $newfilename = $newfileinfo['filename'];

    if (!$fs->file_exists($newcontextid, $newcomponent, $newfilearea, $newitemid, $newfilepath, $newfilename)) {
        $fs->create_file_from_storedfile($newfileinfo, $oldfile);
    }
}

/**
 * Retorna as urls de uma string apontando para os arquivos draftfile
 *
 * @param string $content é o conteúdo do form tinymce
 * @param int $contextId é o contexto do usuário
 * @return string
 */
function setDraftfileUrl($content, $contextId){
    global $CFG;
    preg_match_all('/(https?:\/\/[^\s]+)/', $content, $matches);
    $urls = $matches[0];
    foreach ($urls as $url) {
        $wwwroot = $CFG->wwwroot;
        $pluginfileurlpattern = '/' . preg_quote($wwwroot, '/') .
            '\/pluginfile\.php\/([0-9]+)\/'.local_notification::PLUGINNAME.'\/notification_file\/([0-9]+)\/([^\/]+)/';
        if (preg_match($pluginfileurlpattern, $url, $matches)) {
            $itemid = $matches[2];
            $filename = $matches[3];
            $draftfileurl = $wwwroot . "/draftfile.php/$contextId/user/draft/{$itemid}/$filename";
            $content = str_replace($url, $draftfileurl, $content);
        }
    }
    return $content;
}

/**
 * Retorna @@PLUGINFILE@@ nas urls de arquivos do form tinymce
 *
 * @param string $content é o conteúdo do form tinymce
 * @return array
 */
function setDraftfileUrlToPluginfileUrl($content){
    global $CFG;
    preg_match_all('/(https?:\/\/[^\s]+)/', $content, $matches);
    $urls = $matches[0];
    foreach ($urls as $url) {
        $wwwroot = $CFG->wwwroot;
        $draftfileurlpattern = '/' . preg_quote($wwwroot, '/') . '\/draftfile\.php\/[0-9]+\/user\/draft\/[0-9]+\//';
        $newUrl = preg_replace($draftfileurlpattern, '@@PLUGINFILE@@/', $url);
        $content = str_replace($url, $newUrl, $content);
    }
    return $content;
}
