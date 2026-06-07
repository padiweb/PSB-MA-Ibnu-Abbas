/**
 * PMB Ma'had Aly Ibnu Abbas - Main JS
 */
(function() {
  'use strict';

  // ── Navbar scroll effect ──────────────────────────────────
  const nav = document.getElementById('mainNav');
  if (nav) {
    window.addEventListener('scroll', function() {
      nav.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });
  }

  // ── Smooth scroll for anchor links ───────────────────────
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', function(e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      const offset = 80;
      window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
    });
  });

  // ── Fade-up animation on scroll ──────────────────────────
  const fadeEls = document.querySelectorAll('.fade-up');
  if (fadeEls.length) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    fadeEls.forEach(el => obs.observe(el));
  }

  // ── Admin sidebar mobile toggle ───────────────────────────
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar       = document.querySelector('.admin-sidebar');
  const sidebarOverlay= document.getElementById('sidebarOverlay');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('show');
      if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
    });
    if (sidebarOverlay) {
      sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
      });
    }
  }

  // ── Multi-step form ──────────────────────────────────────
  let currentStep = 1;
  const totalSteps = document.querySelectorAll('.form-step').length;

  function showStep(step) {
    document.querySelectorAll('.form-step').forEach((el, idx) => {
      el.classList.toggle('active', idx + 1 === step);
    });
    document.querySelectorAll('.step-ind').forEach((el, idx) => {
      const num = idx + 1;
      el.classList.remove('active', 'done');
      if (num === step)      el.classList.add('active');
      else if (num < step)   el.classList.add('done');
    });
    // Update step progress label
    const lbl = document.getElementById('stepLabel');
    if (lbl) lbl.textContent = `Langkah ${step} dari ${totalSteps}`;
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Next/prev buttons
  document.querySelectorAll('[data-step-next]').forEach(btn => {
    btn.addEventListener('click', function() {
      if (!validateStep(currentStep)) return;
      if (currentStep < totalSteps) showStep(currentStep + 1);
    });
  });
  document.querySelectorAll('[data-step-prev]').forEach(btn => {
    btn.addEventListener('click', function() {
      if (currentStep > 1) showStep(currentStep - 1);
    });
  });

  function validateStep(step) {
    const stepEl = document.querySelector(`.form-step:nth-child(${step})`);
    if (!stepEl) return true;
    let valid = true;
    stepEl.querySelectorAll('[required]').forEach(el => {
      el.classList.remove('is-invalid');
      if (!el.value.trim()) {
        el.classList.add('is-invalid');
        valid = false;
      }
    });
    if (!valid) {
      const first = stepEl.querySelector('.is-invalid');
      if (first) first.focus();
    }
    return valid;
  }

  // ── File upload drag & drop ───────────────────────────────
  document.querySelectorAll('.upload-area').forEach(area => {
    const input = area.querySelector('input[type="file"]');
    if (!input) return;

    area.addEventListener('click', () => input.click());
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', e => {
      e.preventDefault();
      area.classList.remove('dragover');
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        handleFileSelect(input, area);
      }
    });
    input.addEventListener('change', () => handleFileSelect(input, area));
  });

  function handleFileSelect(input, area) {
    const file = input.files[0];
    if (!file) return;
    const maxSize = 5 * 1024 * 1024;
    const allowed = ['image/jpeg','image/jpg','image/png','application/pdf'];
    if (file.size > maxSize) {
      showToast('Ukuran file maksimum 5 MB', 'error');
      input.value = '';
      return;
    }
    if (!allowed.includes(file.type)) {
      showToast('Format file tidak valid (JPG, PNG, PDF saja)', 'error');
      input.value = '';
      return;
    }
    area.classList.add('uploaded');
    const nameEl = area.querySelector('.upload-filename');
    if (nameEl) nameEl.textContent = file.name;
    const sizeEl = area.querySelector('.upload-filesize');
    if (sizeEl) sizeEl.textContent = formatBytes(file.size);
    const iconEl = area.querySelector('.upload-icon');
    if (iconEl) { iconEl.className = 'bi bi-check-circle-fill text-success upload-icon'; }
  }

  // ── Toast notifications ───────────────────────────────────
  function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer') || createToastContainer();
    const id    = 'toast_' + Date.now();
    const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill', warning: 'bi-exclamation-triangle-fill' };
    const colors= { success: 'text-success', error: 'text-danger', info: 'text-primary', warning: 'text-warning' };
    const el = document.createElement('div');
    el.id = id;
    el.className = 'toast align-items-center border-0 shadow';
    el.setAttribute('role', 'alert');
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body d-flex align-items-center gap-2">
          <i class="bi ${icons[type] || icons.info} ${colors[type] || colors.info}"></i>
          <span>${escapeHtml(message)}</span>
        </div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    container.appendChild(el);
    const toast = new bootstrap.Toast(el, { delay: 4000 });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  }

  function createToastContainer() {
    const c = document.createElement('div');
    c.id = 'toastContainer';
    c.className = 'toast-container position-fixed top-0 end-0 p-3';
    c.style.zIndex = '9999';
    document.body.appendChild(c);
    return c;
  }

  window.showToast = showToast;

  // ── AJAX helpers ──────────────────────────────────────────
  window.pmb = {
    post: async function(url, data) {
      const resp = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(data)
      });
      return resp.json();
    },
    get: async function(url) {
      const resp = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      return resp.json();
    }
  };

  // ── Real-time search (admin table) ────────────────────────
  const searchInput = document.getElementById('tableSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const q   = this.value.toLowerCase();
      const rows= document.querySelectorAll('.table-searchable tbody tr');
      rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  // ── Confirm delete dialogs ────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm || 'Yakin ingin menghapus?')) {
        e.preventDefault();
      }
    });
  });

  // ── Formatters ────────────────────────────────────────────
  function formatBytes(bytes) {
    if (bytes < 1024)       return bytes + ' B';
    if (bytes < 1048576)    return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }
  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  // ── Auto-dismiss alerts ───────────────────────────────────
  document.querySelectorAll('.alert-dismissible').forEach(alert => {
    setTimeout(() => {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      if (bsAlert) bsAlert.close();
    }, 6000);
  });

  // Init first step if form exists
  if (totalSteps > 0) showStep(1);

})();
