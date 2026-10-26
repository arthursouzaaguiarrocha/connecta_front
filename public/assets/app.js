$(function () {
  // Mobile sidebar
  $('#menu-toggle').on('click', function () {
    $('#sidebar').toggleClass('open');
    $('#sidebar-overlay').toggleClass('open');
  });
  $('#sidebar-overlay').on('click', function () {
    $('#sidebar').removeClass('open');
    $(this).removeClass('open');
  });

  // Confirm deletes
  $(document).on('submit', '.delete-form', function (e) {
    if (!window.confirm('Tem certeza que deseja excluir?')) {
      e.preventDefault();
    }
  });

  // Theme toggle (client-side preview)
  $('#theme-toggle').on('click', function () {
    var html = document.documentElement;
    var current = html.getAttribute('data-theme') || 'light';
    var next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    $('#sidebar').toggleClass('dark', next === 'dark');
    $(this).find('i').attr('class', next === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars');
  });

  // Chat: auto-scroll to bottom
  var $messages = $('#chat-messages');
  if ($messages.length) {
    $messages.scrollTop($messages[0].scrollHeight);
  }

  // Chat: focus input
  $('#chat-input').focus();
});
