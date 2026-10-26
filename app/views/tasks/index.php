<?php
$priorityBadge = [
    'alta' => ['badge-danger', 'ALTA PRIORIDADE'],
    'media' => ['badge-warning', 'MÉDIA PRIORIDADE'],
    'baixa' => ['badge-neutral', 'BAIXA PRIORIDADE'],
];
$statusBadge = [
    'concluida' => ['badge-success', 'CONCLUÍDA'],
    'pendente' => ['badge-info', 'PENDENTE'],
];
?>
<div class="page-header">
  <h1>TAREFAS</h1>
  <div class="actions">
    <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=tasks/form"><i class="bi bi-plus-lg"></i> Criar Tarefa</a>
  </div>
</div>

<?php if (!$tasks): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-check2-square"></i></div>
      <h3><?= $search ? 'Nenhuma tarefa encontrada' : 'Nenhuma tarefa ainda' ?></h3>
      <p>Crie tarefas para organizar seu dia a dia.</p>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=tasks/form"><i class="bi bi-plus-lg"></i> Criar Tarefa</a>
    </div>
  </div>
<?php else: ?>
  <div class="tasks-grid">
    <?php foreach ($tasks as $t):
      $pb = $priorityBadge[$t['priority']] ?? $priorityBadge['media'];
      $sb = $statusBadge[$t['status']] ?? $statusBadge['pendente'];
    ?>
      <article class="task-card">
        <div class="task-card-top">
          <span class="badge <?= $t['status'] === 'concluida' ? $sb[0] : $pb[0] ?>">
            <?= $t['status'] === 'concluida' ? $sb[1] : $pb[1] ?>
          </span>
          <div style="display:flex;gap:.25rem">
            <a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/index.php?url=tasks/form&id=<?= (int)$t['id'] ?>"><i class="bi bi-pencil"></i></a>
            <form method="post" action="<?= BASE_URL ?>/index.php?url=tasks/delete" class="delete-form">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
              <button class="btn btn-ghost btn-sm" style="color:var(--danger)"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </div>
        <div class="title" style="<?= $t['status']==='concluida'?'text-decoration:line-through;opacity:.7':'' ?>">
          <?= e($t['title']) ?>
        </div>
        <?php if ($t['due_date']): ?>
          <div class="due"><i class="bi bi-calendar-event"></i> <?= date('d M Y', strtotime($t['due_date'])) ?><?= $t['due_time'] ? ' · ' . substr($t['due_time'],0,5) : '' ?></div>
        <?php endif; ?>
        <div class="task-actions">
          <form method="post" action="<?= BASE_URL ?>/index.php?url=tasks/toggle">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
            <button class="btn btn-outline btn-sm" type="submit">
              <i class="bi bi-<?= $t['status'] === 'concluida' ? 'arrow-counterclockwise' : 'check2' ?>"></i>
              <?= $t['status'] === 'concluida' ? 'Reabrir' : 'Concluir' ?>
            </button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
