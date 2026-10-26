<div class="page-header">
  <h1><?= $id ? 'Editar evento' : 'Novo evento' ?></h1>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=agenda">← Voltar</a>
</div>
<div class="card" style="max-width:560px">
  <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label>Título *</label>
      <input class="form-control" name="title" value="<?= e($event['title']) ?>" required>
    </div>
    <div class="form-group">
      <label>Descrição</label>
      <textarea class="form-control" name="description" rows="2"><?= e($event['description'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Data</label>
      <input class="form-control" type="date" name="event_date" value="<?= e($event['event_date']) ?>" required>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div class="form-group">
        <label>Início</label>
        <input class="form-control" type="time" name="start_time" value="<?= e(substr($event['start_time'],0,5)) ?>" required>
      </div>
      <div class="form-group">
        <label>Fim</label>
        <input class="form-control" type="time" name="end_time" value="<?= e(substr($event['end_time'] ?? '',0,5)) ?>">
      </div>
    </div>
    <div class="form-group">
      <label>Local</label>
      <input class="form-control" name="location" value="<?= e($event['location'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Categoria</label>
      <select class="form-control" name="category">
        <?php foreach (['Trabalho','Networking','Estudo','Pessoal','Outro'] as $cat): ?>
          <option value="<?= $cat ?>" <?= ($event['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button class="btn btn-primary" type="submit"><?= $id ? 'Salvar' : 'Criar evento' ?></button>
  </form>
</div>
