<div class="page-header">
  <h1>GRUPOS</h1>
  <div class="actions" style="display:flex;gap:.5rem;flex-wrap:wrap">
    <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=groups/join"><i class="bi bi-box-arrow-in-right"></i> Entrar com código</a>
    <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=groups/form"><i class="bi bi-plus-lg"></i> Criar Grupo</a>
  </div>
</div>

<?php if (!empty($pendingRequests)): ?>
<div class="card" style="margin-bottom:1.25rem;border-left:4px solid #0D9488">
  <h3 style="margin:0 0 .75rem;font-size:1rem"><i class="bi bi-bell"></i> Pedidos de entrada pendentes (<?= count($pendingRequests) ?>)</h3>
  <div style="display:flex;flex-direction:column;gap:.6rem">
    <?php foreach ($pendingRequests as $req): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;padding:.5rem 0;border-bottom:1px solid var(--border,#e2e8f0)">
        <div>
          <b><?= e($req['user_name']) ?></b> quer entrar em <b><?= e($req['group_name']) ?></b>
          <div class="text-xs muted"><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></div>
        </div>
        <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?url=groups/form&id=<?= (int)$req['group_id'] ?>">
          Ver e responder
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

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
      <p>Crie grupos ou entre em um grupo com código de convite.</p>
      <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap">
        <a class="btn btn-primary" href="<?= BASE_URL ?>/index.php?url=groups/form"><i class="bi bi-plus-lg"></i> Criar Grupo</a>
        <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=groups/join"><i class="bi bi-box-arrow-in-right"></i> Entrar com código</a>
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="groups-list">
    <?php foreach ($groups as $g):
      $iconMap = ['chart'=>'bi-graph-up','rocket'=>'bi-rocket-takeoff','book'=>'bi-book','users'=>'bi-people'];
      $bi = $iconMap[$g['icon'] ?? 'users'] ?? 'bi-people';
      $isOwner = ($g['my_role'] ?? '') === 'admin' && (int)($g['user_id'] ?? 0) === (int)($_SESSION['user_id'] ?? 0);
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
            <?php if (($g['my_role'] ?? '') === 'admin'): ?>
              <span class="badge" style="margin-left:.35rem;background:#fef3c7;color:#b45309;font-size:.7rem">Admin</span>
            <?php endif; ?>
          </h4>
          <p><?= e($g['last_message'] ?? $g['description'] ?? 'Sem mensagens ainda') ?></p>
          <div class="group-meta">
            <?php if ($g['last_message_at']): ?>
              <span><?= date('H:i', strtotime($g['last_message_at'])) ?></span>
            <?php endif; ?>
            <span>· <?= (int)$g['member_count'] ?> membros</span>
            <?php if (!empty($g['invite_code']) && $isOwner): ?>
              <span>· Código: <code style="font-size:.8rem"><?= e($g['invite_code']) ?></code></span>
            <?php endif; ?>
          </div>
        </div>
        <div style="display:flex;gap:.35rem;align-items:center">
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?url=chat&group=<?= (int)$g['id'] ?>" title="Abrir chat"><i class="bi bi-chat-dots"></i></a>
          <?php if ($isOwner): ?>
            <a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/index.php?url=groups/form&id=<?= (int)$g['id'] ?>"><i class="bi bi-pencil"></i></a>
            <form method="post" action="<?= BASE_URL ?>/index.php?url=groups/delete" class="delete-form">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
              <button class="btn btn-ghost btn-sm" style="color:var(--danger)"><i class="bi bi-trash"></i></button>
            </form>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
