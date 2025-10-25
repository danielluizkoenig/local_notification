# Plugin Local Notification - Moodle

Plugin para gerenciamento de notificações automáticas baseadas em queries SQL personalizadas.

## Versão
- **Versão**: 2.0
- **Compatibilidade**: Moodle 4.2+
- **Autor**: SENAI Soluções Digitais - SC

## Funcionalidades

### Principais
- Criação de notificações automáticas por e-mail e pop-up
- Queries SQL personalizadas para seleção de usuários
- Agendamento baseado em dias após início do curso ou datas específicas
- Relatórios de envio e acompanhamento
- Anexos em notificações
- Controle de duplicação de envios

### Tipos de Notificação
- **E-mail**: Notificações enviadas por e-mail
- **Pop-up**: Mensagens do sistema (notificações internas)

## Estrutura do Plugin

```
local/notification/
├── amd/src/                    # JavaScript (AMD modules)
├── classes/                    # Classes PHP
│   ├── helper/                 # Classes auxiliares
│   ├── task/                   # Tarefas agendadas
│   └── forms/                  # Formulários
├── db/                         # Definições de banco
├── lang/                       # Arquivos de idioma
├── templates/                  # Templates Mustache
└── *.php                       # Páginas principais
```

## Instalação

1. Extrair o plugin em `moodle/local/notification/`
2. Acessar a administração do Moodle para completar a instalação
3. Configurar permissões em **Administração do site > Usuários > Permissões**

## Configuração

### Configurações Globais
- **Administração do site > Plugins > Plugins locais > Notificações**
- Habilitar/desabilitar notificações por e-mail
- Habilitar/desabilitar notificações por pop-up

### Gerenciar Queries
- Criar queries SQL personalizadas
- Usar variáveis: `%%COURSEID%%`, `%%TIME%%`
- Colunas obrigatórias: `id`, `nome_completo`, `email`

### Configurar Notificações
- Selecionar curso e tipo de query
- Definir tempo (dias ou datas específicas)
- Configurar assunto e conteúdo
- Anexar arquivos (máx. 15MB)

## Variáveis Disponíveis

### No Conteúdo das Mensagens
- `%%ALUNO_NOME%%` - Nome do aluno
- `%%ALUNO_EMAIL%%` - E-mail do aluno

### Nas Queries SQL
- `%%COURSEID%%` - ID do curso
- `%%TIME%%` - Dias configurados na notificação

## Query Padrão

```sql
SELECT CONCAT(u.firstname, ' ', u.lastname) AS nome_completo,
       u.id,
       u.email,
       DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP()), FROM_UNIXTIME(c.startdate)) as data,
       en.courseid
FROM {course} c
JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
JOIN {role_assignments} ra ON ctx.id = ra.contextid
JOIN {user_enrolments} ue ON ue.userid = ra.userid AND ue.status = 0
JOIN {enrol} en ON ue.enrolid = en.id AND en.courseid = ctx.instanceid
JOIN {user} u ON ra.userid = u.id
JOIN {role} r ON ra.roleid = r.id AND ra.roleid = 5
WHERE c.id = %%COURSEID%% AND DATEDIFF(FROM_UNIXTIME(UNIX_TIMESTAMP()), FROM_UNIXTIME(c.startdate)) = %%TIME%%
```

## Tarefas Agendadas

- **notification_task**: Envio de notificações
- **send_popup_notification_task**: Envio de pop-ups
- **deactivate_notifications_task**: Inativação de notificações antigas

## Permissões

- `local/notification:view` - Visualizar notificações

## Relatórios

### Relatório Principal
- Lista notificações por curso
- Total de envios e último envio
- Filtros por status e datas

### Detalhes da Notificação
- Lista de estudantes que receberam
- Data de envio e primeiro acesso
- Exportação CSV/Excel

## Banco de Dados

### Tabelas Criadas
- `notification` - Configurações de notificações
- `notification_query` - Queries SQL personalizadas
- `notification_history` - Histórico de envios
- `notification_files` - Arquivos anexados

## Desenvolvimento

### JavaScript
- Usa módulos AMD do Moodle
- Modal nativa com `core/modal_factory`
- Validação de formulários

### PHP
- Segue padrões do Moodle
- Classes organizadas por namespace
- Uso de APIs nativas (DB, File Storage, etc.)

## Suporte

Para suporte técnico, entre em contato com:
**SENAI Soluções Digitais - SC**: sd-tribo-ava@sc.senai.br