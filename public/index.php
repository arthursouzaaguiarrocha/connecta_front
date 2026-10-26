<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Contact.php';
require_once __DIR__ . '/../app/models/Event.php';
require_once __DIR__ . '/../app/models/Task.php';
require_once __DIR__ . '/../app/models/Group.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ContactsController.php';
require_once __DIR__ . '/../app/controllers/AgendaController.php';
require_once __DIR__ . '/../app/controllers/TasksController.php';
require_once __DIR__ . '/../app/controllers/GroupsController.php';
require_once __DIR__ . '/../app/controllers/SettingsController.php';
require_once __DIR__ . '/../app/controllers/ChatController.php';

$url = trim($_GET['url'] ?? '', '/');

try {
    Database::connect();

    switch ($url) {
        case '':
            (new HomeController())->index();
            break;
        case 'login':
            (new AuthController())->login();
            break;
        case 'register':
            (new AuthController())->register();
            break;
        case 'logout':
            (new AuthController())->logout();
            break;
        case 'contacts':
            (new ContactsController())->index();
            break;
        case 'contacts/form':
            (new ContactsController())->form();
            break;
        case 'contacts/delete':
            (new ContactsController())->delete();
            break;
        case 'agenda':
            (new AgendaController())->index();
            break;
        case 'agenda/form':
            (new AgendaController())->form();
            break;
        case 'agenda/delete':
            (new AgendaController())->delete();
            break;
        case 'tasks':
            (new TasksController())->index();
            break;
        case 'tasks/form':
            (new TasksController())->form();
            break;
        case 'tasks/delete':
            (new TasksController())->delete();
            break;
        case 'tasks/toggle':
            (new TasksController())->toggle();
            break;
        case 'groups':
            (new GroupsController())->index();
            break;
        case 'groups/form':
            (new GroupsController())->form();
            break;
        case 'groups/delete':
            (new GroupsController())->delete();
            break;
        case 'chat':
            (new ChatController())->index();
            break;
        case 'chat/send':
            (new ChatController())->send();
            break;
        case 'settings':
            (new SettingsController())->index();
            break;
        case 'settings/save':
            (new SettingsController())->save();
            break;
        default:
            http_response_code(404);
            echo '<h1>Página não encontrada</h1><p><a href="' . BASE_URL . '/index.php">Voltar</a></p>';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Erro</h1><pre style="white-space:pre-wrap;font-size:13px">' . htmlspecialchars($e->getMessage()) . '</pre>';
}
