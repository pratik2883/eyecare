function toggleSidebar() {
  const el = document.getElementById('adminSidebar');
  if (el) el.classList.toggle('open');
}

function closeAllModals() {
  document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
  const overlay = document.getElementById('modal-overlay');
  if (overlay) overlay.classList.remove('active');
}

function openModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  modal.classList.add('active');
  const overlay = document.getElementById('modal-overlay');
  if (overlay) overlay.classList.add('active');
}

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-overlay')) closeAllModals();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeAllModals();
});

function confirmDelete(msg) {
  return confirm(msg || 'Are you sure?');
}

function triggerGlobalSearch(e) {
  if (e && e.key !== 'Enter') return;
  const input = document.getElementById('globalSearch');
  if (!input) return;
  const val = input.value.trim();
  if (!val) return;
  const base = input.dataset.searchUrl || '/admin/inventory';
  window.location = base + '?search=' + encodeURIComponent(val);
}

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.content : '';
}

let dragSrcEl = null;
function handleDragStart(e) {
  dragSrcEl = this;
  this.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.setData('text/plain', this.dataset.id || '');
}
function handleDragOver(e) { e.preventDefault(); this.classList.add('over'); }
function handleDragLeave(e) { this.classList.remove('over'); }
function handleDrop(e) {
  e.stopPropagation();
  if (dragSrcEl && dragSrcEl !== this) {
    const parent = this.parentNode;
    const items = Array.from(parent.children);
    const srcIdx = items.indexOf(dragSrcEl);
    const tgtIdx = items.indexOf(this);
    if (srcIdx < tgtIdx) {
      parent.insertBefore(dragSrcEl, this.nextSibling);
    } else {
      parent.insertBefore(dragSrcEl, this);
    }
  }
  this.classList.remove('over');
  return false;
}
function handleDragEnd(e) {
  this.classList.remove('dragging');
  document.querySelectorAll('.sortable-item').forEach(i => i.classList.remove('over'));
  saveSortOrder();
}
function saveSortOrder() {
  const container = document.querySelector('.sortable-grid');
  if (!container) return;
  const ids = Array.from(container.children).map(el => el.dataset.id).filter(Boolean);
  if (!ids.length) return;
  const url = container.dataset.reorderUrl;
  if (!url) return;
  const token = getCsrfToken();
  if (!token) return;
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({ order: ids })
  }).catch(() => {});
}
document.querySelectorAll('.sortable-item').forEach(item => {
  item.addEventListener('dragstart', handleDragStart);
  item.addEventListener('dragover', handleDragOver);
  item.addEventListener('dragleave', handleDragLeave);
  item.addEventListener('drop', handleDrop);
  item.addEventListener('dragend', handleDragEnd);
});

const dropZones = document.querySelectorAll('.drop-zone');
dropZones.forEach(zone => {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
  zone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    const fileInput = this.querySelector('input[type="file"]');
    if (fileInput && e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      const evt = new Event('change', { bubbles: true });
      fileInput.dispatchEvent(evt);
    }
  });
});
