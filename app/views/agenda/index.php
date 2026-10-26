<?php
$prevM = $month - 1; $prevY = $year;
if ($prevM < 1) { $prevM = 12; $prevY--; }
$nextM = $month + 1; $nextY = $year;
if ($nextM > 12) { $nextM = 1; $nextY++; }

$firstDow = (int) date('w', strtotime("$year-$month-01"));
$daysInMonth = (int) date('t', strtotime("$year-$month-01"));
$today = date('Y-m-d');
?>
<div class="page-header">
  <div>
    <h1>AGENDA</h1>
  </div>
  <div class="actions">
    <a class="btn btn-outline btn-sm" href="?url=agenda&y=<?= $prevY ?>&m=<?= $prevM ?>&d=<?= $day ?>"><i class="bi bi-chevron-left"></i></a>
    <span class="fw-600"><?= e($monthName) ?> <?= $year ?></span>
    <a class="btn btn-outline btn-sm" href="?url=agenda&y=<?= $nextY ?>&m=<?= $nextM ?>&d=<?= $day ?>"><i class="bi bi-chevron-right"></i></a>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=agenda/form"><i class="bi bi-plus-lg"></i> Criar Evento</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:1.5rem" class="agenda-layout">
  <div>
    <div class="calendar-mini">
      <div class="calendar-header">
        <span><?= e($monthName) ?> <?= $year ?></span>
      </div>
      <div class="calendar-grid">
        <?php foreach (['D','S','T','Q','Q','S','S'] as $d): ?>
          <div class="dow"><?= $d ?></div>
        <?php endforeach; ?>
        <?php
        for ($i = 0; $i < $firstDow; $i++) echo '<div class="day other"></div>';
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $cls = 'day';
            if ($date === $today || $date === $day) $cls .= ' today';
            if (!empty($eventDays[$date])) $cls .= ' has-event';
            echo '<a class="' . $cls . '" href="?url=agenda&y=' . $year . '&m=' . $month . '&d=' . $date . '">' . $d . '</a>';
        }
        ?>
      </div>
    </div>

    <div class="sync-banner" style="margin-top:1rem;flex-direction:column;align-items:flex-start">
      <div>
        <h3><i class="bi bi-arrow-repeat"></i> Connecta Sync</h3>
        <p>Sincronize seu Google Calendar e Outlook para centralizar tudo.</p>
      </div>
      <button class="btn btn-sm" style="margin-top:.75rem;background:#fff;color:var(--primary-dark)"><i class="bi bi-link-45deg"></i> CONECTAR AGORA</button>
    </div>
  </div>

  <div>
    <div class="card-header" style="margin-bottom:1rem">
      <h2>Agenda de Hoje · <?= date('j \d\e F', strtotime($day)) ?></h2>
      <span class="badge badge-primary"><?= count($dayEvents) ?> Compromisso<?= count($dayEvents) !== 1 ? 's' : '' ?></span>
    </div>

    <?php if (!$dayEvents): ?>
      <div class="card">
        <div class="empty-state">
          <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
          <h3>Nenhum evento neste dia</h3>
          <p>Crie um novo compromisso para organizar sua agenda.</p>
          <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=agenda/form"><i class="bi bi-plus-lg"></i> Criar Evento</a>
        </div>
      </div>
    <?php else: ?>
      <div class="event-list">
        <?php foreach ($dayEvents as $ev): ?>
          <article class="event-item">
            <div class="event-time">
              <div class="hour"><?= e(substr($ev['start_time'], 0, 5)) ?></div>
              <div class="ampm"><?= (int)substr($ev['start_time'],0,2) < 12 ? 'AM' : 'PM' ?></div>
            </div>
            <div class="event-body">
              <h4><?= e($ev['title']) ?></h4>
              <?php if ($ev['description']): ?><p><?= e($ev['description']) ?></p><?php endif; ?>
              <div class="event-meta">
                <span class="badge badge-info"><?= e($ev['category']) ?></span>
                <?php if ($ev['location']): ?><span><i class="bi bi-geo-alt"></i> <?= e($ev['location']) ?></span><?php endif; ?>
              </div>
            </div>
            <div style="display:flex;gap:.35rem">
              <a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/index.php?url=agenda/form&id=<?= (int)$ev['id'] ?>"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= BASE_URL ?>/index.php?url=agenda/delete" class="delete-form">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
                <button class="btn btn-ghost btn-sm" style="color:var(--danger)"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
@media (max-width: 768px) {
  .agenda-layout { grid-template-columns: 1fr !important; }
}
</style>
