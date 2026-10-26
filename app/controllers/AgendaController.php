<?php
class AgendaController extends Controller
{
    private Event $events;

    public function __construct()
    {
        $this->events = new Event(Database::connect());
    }

    public function index(): void
    {
        $year  = (int) ($_GET['y'] ?? date('Y'));
        $month = (int) ($_GET['m'] ?? date('n'));
        $day   = $_GET['d'] ?? date('Y-m-d');

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
            $day = date('Y-m-d');
        }

        $monthEvents = $this->events->byMonth((int) $_SESSION['user_id'], $year, $month);
        $dayEvents   = $this->events->byDate((int) $_SESSION['user_id'], $day);

        // Map days that have events
        $eventDays = [];
        foreach ($monthEvents as $ev) {
            $eventDays[$ev['event_date']] = true;
        }

        $this->appView('agenda/index', [
            'title' => 'Agenda',
            'activeMenu' => 'agenda',
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'dayEvents' => $dayEvents,
            'eventDays' => $eventDays,
            'monthName' => [
                1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'
            ][$month],
        ]);
    }

    public function form(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;
        $error = '';
        $event = [
            'title' => '', 'description' => '', 'event_date' => date('Y-m-d'),
            'start_time' => '09:00', 'end_time' => '', 'location' => '',
            'category' => 'Trabalho',
        ];

        if ($id) {
            $found = $this->events->find($id, (int) $_SESSION['user_id']);
            if (!$found) $this->redirect('agenda');
            $event = $found;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_valid($_POST['csrf'] ?? '')) {
                $error = 'Sessão expirada.';
            } else {
                $v = [
                    'title' => trim($_POST['title'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'event_date' => $_POST['event_date'] ?? date('Y-m-d'),
                    'start_time' => $_POST['start_time'] ?? '09:00',
                    'end_time' => $_POST['end_time'] ?? null,
                    'location' => trim($_POST['location'] ?? ''),
                    'category' => $_POST['category'] ?? 'Trabalho',
                ];
                if ($v['title'] === '') {
                    $error = 'Título é obrigatório.';
                    $event = $v;
                } else {
                    $this->events->save($v, (int) $_SESSION['user_id'], $id);
                    $this->redirect('agenda');
                }
            }
        }

        $this->appView('agenda/form', [
            'title' => $id ? 'Editar evento' : 'Novo evento',
            'activeMenu' => 'agenda',
            'event' => $event,
            'id' => $id,
            'error' => $error,
        ]);
    }

    public function delete(): void
    {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid($_POST['csrf'] ?? '')) {
            $this->events->delete((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
        }
        $this->redirect('agenda');
    }
}
