<div class="page-header">
  <h1><?= $id ? 'Editar tarefa' : 'Nova tarefa' ?></h1>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?url=tasks">← Voltar</a>
</div>
<div class="card" style="max-width:560px">
  <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label>Título *</label>
      <input class="form-control" name="title" value="<?= e($task['title']) ?>" required>
    </div>
    <div class="form-group">
      <label>Descrição</label>
      <textarea class="form-control" name="description" rows="2"><?= e($task['description'] ?? '') ?></textarea>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div class="form-group">
        <label>Prioridade</label>
        <select class="form-control" name="priority">
          <option value="alta" <?= ($task['priority']??'')==='alta'?'selected':'' ?>>Alta</option>
          <option value="media" <?= ($task['priority']??'media')==='media'?'selected':'' ?>>Média</option>
          <option value="baixa" <?= ($task['priority']??'')==='baixa'?'selected':'' ?>>Baixa</option>
        </select>
      </div>
      <div class="form-group">
        <label>Status</label>
        <select class="form-control" name="status">
          <option value="pendente" <?= ($task['status']??'')==='pendente'?'selected':'' ?>>Pendente</option>
          <option value="concluida" <?= ($task['status']??'')==='concluida'?'selected':'' ?>>Concluída</option>
        </select>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
      <div class="form-group">
        <label>Data</label>
        <input class="form-control" type="date" name="due_date" value="<?= e($task['due_date'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Horário</label>
        <input class="form-control" type="time" name="due_time" value="<?= e(substr($task['due_time'] ?? '',0,5)) ?>">
      </div>
    </div>
    <button class="btn btn-primary" type="submit"><?= $id ? 'Salvar' : 'Criar tarefa' ?></button>
  </form>
</div>
