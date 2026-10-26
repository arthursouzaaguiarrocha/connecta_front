<div class="page-header">
  <h1>GRUPOS</h1>
  <div class="actions">
    <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=groups/form"><i class="bi bi-plus-lg"></i> Criar Grupo</a>
  </div>
</div>

<div class="stats-row">
  <div class="stat-card">
    <div class="stat-icon"><i class="bi bi-collection"></i></div>
    <div>
      <div class="stat-value"><?= (int)$totalGroups ?></div>
      <div class="stat-label">Total de Grupos</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:#DBEAFE;color:#2563EB"><i class="bi bi-chat-dots"></i></div>
    <div>
      <div class="stat-value"><?= (int)$messagesToday ?></div>
      <div class="stat-label">Mensagens de Hoje</div>
    </div>
  </div>
</div>

<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap">
  <?php
  $tags = ['', 'Faturamento', 'Marketing', 'Educação', 'VIP'];
  foreach ($tags as $t):
    $label = $t === '' ? 'Todos' : $t;
    $isActive = ($tag ?? '') === $t;
  ?>
    <a class="btn btn-sm <?= $isActive ? 'btn-primary' : 'btn-outline' ?>"
       href="?url=groups<?= $t ? '&tag='.urlencode($t) : '' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<?php if (!$groups): ?>
  <div class="card">
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-collection"></i></div>
      <h3>Nenhum grupo ainda</h3>
      <p>Crie grupos para organizar contatos e mensagens.</p>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=groups/form"><i class="bi bi-plus-lg"></i> Criar Grupo</a>
    </div>
  </div>
<?php else: ?>
  <div class="groups-list">
    <?php foreach ($groups as $g):
      $iconMap = ['chart'=>'bi-graph-up','rocket'=>'bi-rocket-takeoff','book'=>'bi-book','users'=>'bi-people'];
      $bi = $iconMap[$g['icon'] ?? 'users'] ?? 'bi-people';
    ?>
      <article class="group-item">
        <div class="group-icon" style="background:<?= e($g['color'] ?? '#0D9488') ?>">
          <i class="bi <?= $bi ?>"></i>
        </div>
        <div class="group-body">
          <h4>
            <?= e($g['name']) ?>
            <?php if ($g['tag']): ?>
              <span class="badge badge-primary" style="margin-left:.35rem"><?= e($g['tag']) ?></span>
            <?php endif; ?>
          </h4>
          <p><?= e($g['last_message'] ?? $g['description'] ?? 'Sem mensagens ainda') ?></p>
          <div class="group-meta">
            <?php if ($g['last_message_at']): ?>
              <span><?= date('H:i', strtotime($g['last_message_at'])) ?></span>
            <?php endif; ?>
            <span>· <?= (int)$g['member_count'] ?> membros</span>
          </div>
        </div>
        <div style="display:flex;gap:.35rem;align-items:center">
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?url=chat&group=<?= (int)$g['id'] ?>" title="Abrir chat"><i class="bi bi-chat-dots"></i></a>
          <a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/index.php?url=groups/form&id=<?= (int)$g['id'] ?>"><i class="bi bi-pencil"></i></a>
          <form method="post" action="<?= BASE_URL ?>/index.php?url=groups/delete" class="delete-form">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
            <button class="btn btn-ghost btn-sm" style="color:var(--danger)"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
