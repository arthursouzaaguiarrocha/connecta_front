<div class="page-header">
  <h1><i class="bi bi-box-arrow-in-right"></i> Entrar em um grupo</h1>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=groups">← Voltar</a>
</div>

<div class="card" style="max-width:520px">
  <?php if (!empty($error)): ?>
    <div class="alert" style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem">
      <?= e($error) ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert" style="background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem">
      <?= e($success) ?>
    </div>
  <?php endif; ?>

  <?php if ($preview && empty($success)): ?>
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;padding:1rem;background:var(--bg-muted,#f8fafc);border-radius:10px">
      <div class="chat-conv-avatar" style="background:<?= e($preview['color'] ?? '#0D9488') ?>;width:48px;height:48px;font-size:1.2rem;display:flex;align-items:center;justify-content:center;border-radius:12px;color:#fff">
        <i class="bi bi-people"></i>
      </div>
      <div>
        <b style="font-size:1.05rem"><?= e($preview['name']) ?></b>
        <?php if (!empty($preview['tag'])): ?>
          <span class="badge badge-primary" style="margin-left:.35rem"><?= e($preview['tag']) ?></span>
        <?php endif; ?>
        <div class="text-xs muted" style="margin-top:.2rem"><?= e($preview['description'] ?? 'Sem descrição') ?></div>
      </div>
    </div>
  <?php endif; ?>

  <?php if (empty($success)): ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label>Código de convite *</label>
      <input class="form-control" name="code" value="<?= e($_POST['code'] ?? $_GET['code'] ?? '') ?>"
             placeholder="Ex: A1B2C3D4" style="text-transform:uppercase;letter-spacing:2px;font-weight:600;font-size:1.1rem" required maxlength="12">
      <small class="muted">Peça o código ao administrador do grupo</small>
    </div>
    <div class="form-group">
      <label>Mensagem (opcional)</label>
      <textarea class="form-control" name="message" rows="2" placeholder="Olá, gostaria de entrar no grupo..."><?= e($_POST['message'] ?? '') ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit">
      <i class="bi bi-send"></i> Solicitar entrada
    </button>
  </form>
  <?php else: ?>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=groups">Voltar aos grupos</a>
  <?php endif; ?>
</div>
