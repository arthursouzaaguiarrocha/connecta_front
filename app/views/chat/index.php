<div class="page-header">
  <h1><i class="bi bi-chat-dots"></i> CHAT</h1>
</div>

<div class="chat-shell">
  <!-- Lista de conversas (grupos) -->
  <aside class="chat-sidebar">
    <div class="chat-sidebar-header">
      <span class="fw-600">Conversas</span>
      <a href="<?= BASE_URL ?>/index.php?url=groups/form" class="btn btn-ghost btn-sm" title="Novo grupo"><i class="bi bi-plus-lg"></i></a>
    </div>
    <div class="chat-conversations">
      <?php if (!$groups): ?>
        <div class="empty-state" style="padding:2rem 1rem">
          <div class="empty-icon"><i class="bi bi-chat"></i></div>
          <p class="text-sm">Crie um grupo para começar a conversar</p>
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/index.php?url=groups/form">Criar grupo</a>
        </div>
      <?php else: ?>
        <?php foreach ($groups as $g):
          $iconMap = ['chart'=>'bi-graph-up','rocket'=>'bi-rocket-takeoff','book'=>'bi-book','users'=>'bi-people'];
          $bi = $iconMap[$g['icon'] ?? 'users'] ?? 'bi-people';
          $isActive = $groupId && (int)$g['id'] === (int)$groupId;
        ?>
          <a href="?url=chat&group=<?= (int)$g['id'] ?>" class="chat-conv-item <?= $isActive ? 'active' : '' ?>">
            <div class="chat-conv-avatar" style="background:<?= e($g['color'] ?? '#0D9488') ?>">
              <i class="bi <?= $bi ?>"></i>
            </div>
            <div class="chat-conv-info">
              <div class="chat-conv-top">
                <b><?= e($g['name']) ?></b>
                <?php if ($g['last_message_at']): ?>
                  <span class="text-xs muted"><?= date('H:i', strtotime($g['last_message_at'])) ?></span>
                <?php endif; ?>
              </div>
              <p class="chat-conv-preview"><?= e($g['last_message'] ?? 'Sem mensagens') ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </aside>

  <!-- Área de mensagens -->
  <section class="chat-main">
    <?php if (!$activeGroup): ?>
      <div class="chat-empty">
        <i class="bi bi-chat-square-text" style="font-size:3rem;opacity:.3"></i>
        <h3>Selecione uma conversa</h3>
        <p class="muted">Escolha um grupo à esquerda ou crie um novo.</p>
      </div>
    <?php else: ?>
      <header class="chat-main-header">
        <div style="display:flex;align-items:center;gap:.75rem">
          <div class="chat-conv-avatar" style="background:<?= e($activeGroup['color'] ?? '#0D9488') ?>;width:40px;height:40px;font-size:1rem">
            <i class="bi bi-people"></i>
          </div>
          <div>
            <b><?= e($activeGroup['name']) ?></b>
            <?php if ($activeGroup['tag']): ?>
              <span class="badge badge-primary" style="margin-left:.35rem"><?= e($activeGroup['tag']) ?></span>
            <?php endif; ?>
            <div class="text-xs muted">Grupo · <?= e($activeGroup['description'] ?? '') ?></div>
          </div>
        </div>
        <a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/index.php?url=groups/form&id=<?= (int)$activeGroup['id'] ?>" title="Editar grupo"><i class="bi bi-three-dots-vertical"></i></a>
      </header>

      <div class="chat-messages" id="chat-messages">
        <?php if (!$messages): ?>
          <div class="chat-day-divider">Nenhuma mensagem ainda. Diga olá! 👋</div>
        <?php else: ?>
          <?php
          $lastDate = '';
          foreach ($messages as $msg):
            $msgDate = date('Y-m-d', strtotime($msg['created_at']));
            if ($msgDate !== $lastDate):
              $lastDate = $msgDate;
              $label = $msgDate === date('Y-m-d') ? 'Hoje' : date('d/m/Y', strtotime($msgDate));
          ?>
            <div class="chat-day-divider"><?= $label ?></div>
          <?php endif;
            $mine = (int)$msg['user_id'] === (int)$currentUserId;
          ?>
            <div class="chat-bubble-row <?= $mine ? 'mine' : '' ?>">
              <?php if (!$mine): ?>
                <div class="chat-bubble-avatar"><?= e(mb_strtoupper(mb_substr($msg['user_name'], 0, 1))) ?></div>
              <?php endif; ?>
              <div class="chat-bubble">
                <?php if (!$mine): ?>
                  <div class="chat-bubble-name"><?= e($msg['user_name']) ?></div>
                <?php endif; ?>
                <div class="chat-bubble-text"><?= nl2br(e($msg['body'])) ?></div>
                <div class="chat-bubble-time"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <form class="chat-composer" method="post" action="<?= BASE_URL ?>/index.php?url=chat/send" id="chat-form">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="group_id" value="<?= (int)$activeGroup['id'] ?>">
        <input type="text" name="body" id="chat-input" class="form-control" placeholder="Escreva uma mensagem..." autocomplete="off" required>
        <button type="submit" class="btn btn-primary" title="Enviar"><i class="bi bi-send-fill"></i></button>
      </form>
    <?php endif; ?>
  </section>
</div>
