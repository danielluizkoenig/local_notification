<?php
/**
 * Plugin de notificações locais.
 *
 * @package    local_notification
 * @copyright  2025
 * @author     SENAI Soluções Digitais - SC <sd-tribo-ava@sc.senai.br>
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Notificações';

$string['n_list'] = 'Lista de notificações';
$string['n_add'] = 'Configurar nova notificação';
$string['n_time'] = 'Tempo em dias ou data para a notificação';
$string['n_time_help'] = 'Podem ser informadas datas no formato dd/mm/aaaa ou o número de dias. Mais de um dia pode ser informado, separados por vírgula, ex.: 1,2,3 ou 01/01/2000,02/01/2000';
$string['n_subject'] = 'Assunto';
$string['n_description'] = 'Descrição';
$string['n_type'] = 'Tipo de curso';
$string['n_typenotification'] = 'Tipo de notificação';
$string['n_new'] = 'Adicionar configuração de notificação';
$string['n_edit'] = 'Editar configuração de notificação';
$string['n_manage'] = 'Gerenciar tipos de notificações';
$string['n_admin_add'] = 'Configurar notificações';
$string['n_day'] = 'dia(s)';
$string['htmlvars'] = '<div class="row"><div class="col-9 offset-3 pl-5"><b>É possível utilizar as seguintes variáveis no assunto ou no conteúdo da mensagem:</b><br>%%ALUNO_NOME%% = nome do aluno<br>%%ALUNO_EMAIL%% = e-mail do aluno.</div></div>';
$string['selecttype'] = 'Selecione um tipo';
$string['nq_invalidquery'] = 'Query inválida: {$a}';
$string['nq_missing_nomecompleto'] = 'Adicione a coluna nome_completo na sua query: {$a}';
$string['nq_missing_courseid'] = 'Utilize a variável %%COURSEID%% na sua query para evitar o envio de notificações para usuários de outros cursos: {$a}';
$string['nq_query_required'] = 'Preencha o campo query.';
$string['n_query'] = 'Query SQL que será executada';
$string['nq_new'] = 'Adicionar novo tipo de notificação';
$string['nq_edit'] = 'Editar tipo de notificação';
$string['nq_htmlvars'] = "<div class='row'><div class='col-9 offset-3 pl-5'><b>Variáveis:</b><br>%%COURSEID%% = ID do curso configurado na notificação<br>%%TIME%% = intervalo de dias configurado na notificação. Pode ser um número negativo para dias antes do evento ou positivo para dias depois do evento.<br><b>Colunas obrigatórias que devem ser retornadas pela query:</b><br>id,username,CONCAT(firstname,' ',lastname) nome_completo,email.</div></div>";
$string['non_negative_number'] = 'Por favor, insira uma data ou um número positivo válido.';
$string['future_date'] = 'A data não pode ser anterior ao dia de hoje';
$string['local_notification:view'] = 'Permitir listar tipos de notificações locais';
$string['areyousureduplicate'] = 'Deseja duplicar esta notificação?';
$string['numbers_and_symbols_only'] = 'Conteúdo inválido, são permitidos apenas números ou datas separados por vírgula.';
$string['student'] = 'Estudante';
$string['date_days'] = 'Data/dias';
$string['help_desc'] = 'Exibe todos os usuários do curso. Os que estiverem em negrito são os usuários que receberão a notificação de acordo com os parâmetros';
$string['file_big_size'] = 'O tamanho total dos arquivos não pode exceder o limite máximo de 15 MB';
$string['content_empty'] = 'A descrição da notificação não pode estar vazia';
$string['courseend_empty'] = '<p>Esse tipo de notificação requer que o curso tenha uma data de término definida.</p>';
$string['n_duplicated'] = 'Notificação duplicada com sucesso.';
$string['error_deleting_notification'] = 'Falha ao excluir a notificação.';
$string['error_duplicating_notification'] = 'Falha ao duplicar a notificação.';
$string['changessaved'] = 'Alterações salvas com sucesso.';
$string['errorinuse'] = 'Não é possível excluir esta query porque ela está em uso por notificações.';
$string['errordeleting'] = 'Ocorreu um erro ao excluir a query.';
$string['notspecified'] = 'Não especificado';
$string['areyousure'] = 'Tem certeza de que deseja prosseguir?';
$string['enableemail'] = 'Habilitar notificações por e-mail';
$string['enableemail_desc'] = 'Se habilitado, as notificações serão enviadas por e-mail.';
$string['enablepopup'] = 'Habilitar notificações por pop-up';
$string['enablepopup_desc'] = 'Se habilitado, as notificações serão enviadas como mensagens do sistema (notificações pop-up).';
$string['managenotifications'] = 'Gerenciar queries';
$string['loadingstudents'] = 'Buscando estudantes...';
$string['errorfetchingstudents'] = 'Ocorreu um erro ao buscar os dados! Verifique se o tipo em dias ou data está correto!';
$string['days_after'] = 'Dias após a última notificação';
$string['days_after_help'] = 'Se preenchido, a notificação só será enviada se o aluno não tiver recebido a mesma notificação nos últimos X dias.';
$string['deactivate_notifications_task'] = 'Inativar notificações antigas';
$string['report_title'] = 'Relatório de Notificações por Curso';
$string['notification'] = 'Notificação';
$string['total_sent'] = 'Total Enviado';
$string['last_sent'] = 'Último Envio';
$string['no_data'] = 'Nenhum dado encontrado';
$string['all'] = 'Todos';
$string['active'] = 'Ativo';
$string['inactive'] = 'Inativo';
$string['filter'] = 'Filtrar';
$string['report_details_title'] = 'Detalhes da Notificação';
$string['sent_date'] = 'Data de Envio';
$string['first_access_after'] = 'Primeiro Acesso Após Notificação';
$string['notification_not_found'] = 'Notificação não encontrada';
$string['no_students_found'] = 'Nenhum estudante encontrado';
$string['back_to_report'] = 'Voltar ao Relatório';
$string['details'] = 'Detalhes';
$string['date_from'] = 'Data Início';
$string['date_to'] = 'Data Fim';
$string['access_filter'] = 'Filtro de Acesso';
$string['accessed'] = 'Acessou';
$string['not_accessed'] = 'Não Acessou';
$string['export_csv'] = 'Exportar CSV';
$string['export_excel'] = 'Exportar Excel';
$string['days_difference'] = 'Dias de Diferença';