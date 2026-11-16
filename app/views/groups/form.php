<div class="page-header">
  <h1><?= $id ? 'Editar grupo' : 'Novo grupo' ?></h1>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=groups">← Voltar</a>
</div>

<?php if (!empty($error)): ?>
  <div class="alert" style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;max-width:560px">
    <?= e($error) ?>
  </div>
<?php endif; ?>
<?php if (!empty($success)): ?>
  <div class="alert" style="background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;max-width:560px">
    <?= e($success) ?>
  </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr;gap:1.5rem;max-width:720px">
  <!-- Formulário principal -->
  <div class="card">
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="save">
      <div class="form-group">
        <label>Nome *</label>
        <input class="form-control" name="name" value="<?= e($group['name']) ?>" required>
      </div>
      <div class="form-group">
        <label>Descrição</label>
        <textarea class="form-control" name="description" rows="2"><?= e($group['description'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Tag</label>
        <select class="form-control" name="tag">
          <option value="">—</option>
          <?php foreach (['Faturamento','Marketing','Educação','VIP','Lista'] as $t): ?>
            <option value="<?= $t ?>" <?= ($group['tag']??'')===$t?'selected':'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="form-group">
          <label>Cor</label>
          <input class="form-control" type="color" name="color" value="<?= e($group['color'] ?? '#0D9488') ?>">
        </div>
        <div class="form-group">
          <label>Ícone</label>
          <select class="form-control" name="icon">
            <option value="users" <?= ($group['icon']??'')==='users'?'selected':'' ?>>Usuários</option>
            <option value="chart" <?= ($group['icon']??'')==='chart'?'selected':'' ?>>Gráfico</option>
            <option value="rocket" <?= ($group['icon']??'')==='rocket'?'selected':'' ?>>Foguete</option>
            <option value="book" <?= ($group['icon']??'')==='book'?'selected':'' ?>>Livro</option>
          </select>
        </div>
      </div>
      <button class="btn btn-primary" type="submit"><?= $id ? 'Salvar' : 'Criar grupo' ?></button>
    </form>
  </div>

  <?php if ($id): ?>
  <!-- Código de convite -->
  <div class="card">
    <h3 style="margin:0 0 .75rem;font-size:1rem"><i class="bi bi-key"></i> Código de convite</h3>
    <p class="muted text-sm" style="margin-bottom:1rem">Compartilhe este código com outras pessoas para que elas peçam entrada no grupo.</p>
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
      <div style="font-size:1.5rem;font-weight:800;letter-spacing:3px;font-family:monospace;background:var(--bg-muted,#f1f5f9);padding:.6rem 1.2rem;border-radius:10px;border:2px dashed #94a3b8">
        <?= e($group['invite_code'] ?? '—') ?>
      </div>
      <form method="post" style="display:inline">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="regenerate_code">
        <button type="submit" class="btn btn-ghost btn-sm" title="Gerar novo código" onclick="return confirm('Gerar um novo código? O anterior deixará de funcionar.')">
          <i class="bi bi-arrow-clockwise"></i> Regenerar
        </button>
      </form>
    </div>
    <p class="text-xs muted" style="margin-top:.75rem">
      Link direto: <code><?= e((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . '/index.php?url=groups/join&code=' . ($group['invite_code'] ?? '')) ?></code>
    </p>
  </div>

  <!-- Pedidos pendentes -->
  <?php if (!empty($requests)): ?>
  <div class="card">
    <h3 style="margin:0 0 .75rem;font-size:1rem"><i class="bi bi-person-plus"></i> Pedidos de entrada (<?= count($requests) ?>)</h3>
    <div style="display:flex;flex-direction:column;gap:.75rem">
      <?php foreach ($requests as $req): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.75rem;background:var(--bg-muted,#f8fafc);border-radius:10px;flex-wrap:wrap">
          <div>
            <b><?= e($req['user_name']) ?></b>
            <div class="text-xs muted"><?= e($req['user_email']) ?></div>
            <?php if ($req['message']): ?>
              <div class="text-sm" style="margin-top:.25rem;font-style:italic">"<?= e($req['message']) ?>"</div>
            <?php endif; ?>
            <div class="text-xs muted"><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></div>
          </div>
          <div style="display:flex;gap:.5rem">
            <form method="post" style="display:inline">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="action" value="respond_request">
              <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>">
              <input type="hidden" name="response" value="accept">
              <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg"></i> Aceitar</button>
            </form>
            <form method="post" style="display:inline">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="action" value="respond_request">
              <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>">
              <input type="hidden" name="response" value="reject">
              <button type="submit" class="btn btn-ghost btn-sm" style="color:#b91c1c"><i class="bi bi-x-lg"></i> Recusar</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Membros -->
  <?php if (!empty($members)): ?>
  <div class="card">
    <h3 style="margin:0 0 .75rem;font-size:1rem"><i class="bi bi-people"></i> Membros (<?= count($members) ?>)</h3>
    <div style="display:flex;flex-direction:column;gap:.5rem">
      <?php foreach ($members as $m): ?>
        <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem 0;border-bottom:1px solid var(--border,#e2e8f0)">
          <div class="avatar" style="width:36px;height:36px;font-size:.85rem;background:#0D9488;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center">
            <?= e(mb_strtoupper(mb_substr($m['name'], 0, 1))) ?>
          </div>
          <div style="flex:1">
            <b><?= e($m['name']) ?></b>
            <?php if ($m['role'] === 'admin'): ?>
              <span class="badge badge-primary" style="margin-left:.35rem;font-size:.7rem">Admin</span>
            <?php endif; ?>
            <div class="text-xs muted"><?= e($m['email']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
