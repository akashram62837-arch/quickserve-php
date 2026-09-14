/* QuickServe — shared front-end behaviour (no framework, plain JS) */

document.addEventListener('DOMContentLoaded', function () {

  /* Mobile nav toggle (public header) */
  var menuToggle = document.querySelector('.header-menu-toggle');
  var nav = document.querySelector('.qs-nav');
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', function () {
      nav.classList.toggle('qs-nav-open');
      menuToggle.textContent = nav.classList.contains('qs-nav-open') ? '✕' : '☰';
    });
  }

  /* Account dropdown toggle */
  var profileBtn = document.querySelector('.customer-profile-button');
  var dropdown = document.querySelector('.customer-account-dropdown');
  if (profileBtn && dropdown) {
    profileBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function () { dropdown.style.display = 'none'; });
  }

  /* Login modal open/close (public pages) */
  var loginOverlay = document.getElementById('loginOverlay');
  document.querySelectorAll('[data-open-login]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (loginOverlay) loginOverlay.style.display = 'flex';
    });
  });
  document.querySelectorAll('[data-close-login]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (loginOverlay) loginOverlay.style.display = 'none';
    });
  });
  if (loginOverlay) {
    loginOverlay.addEventListener('click', function (e) {
      if (e.target === loginOverlay) loginOverlay.style.display = 'none';
    });
  }

  /* Dashboard sidebar toggle (mobile) */
  var sidebarToggle = document.querySelector('.dash-mobile-toggle');
  var sidebar = document.querySelector('.dash-sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  /* Generic delete/confirm buttons */
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      var msg = el.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  /* Payment method selection */
  document.querySelectorAll('.pay-method-option').forEach(function (opt) {
    opt.addEventListener('click', function () {
      document.querySelectorAll('.pay-method-option').forEach(function (o) { o.classList.remove('selected'); });
      opt.classList.add('selected');
      var radio = opt.querySelector('input[type=radio]');
      if (radio) radio.checked = true;
    });
  });

  /* Filter chips on Services page (client-side quick filter via link reload handled server-side already) */
});

/* Small AJAX helper for admin/provider status-change actions (POST + reload) */
function qsPost(url, data, onDone) {
  var params = new URLSearchParams(data);
  fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: params.toString()
  })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (onDone) onDone(res);
    })
    .catch(function (err) { console.error(err); alert('Something went wrong. Please try again.'); });
}
