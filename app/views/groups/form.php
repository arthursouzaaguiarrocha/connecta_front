<div class="page-header">
  <h1><?= $id ? 'Editar grupo' : 'Novo grupo' ?></h1>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=groups">← Voltar</a>
</div>
<div class="card" style="max-width:560px">
  <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
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
