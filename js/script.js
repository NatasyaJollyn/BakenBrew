// =============================================
// BAKE'N BREW - Main JavaScript
// =============================================

document.addEventListener('DOMContentLoaded', () => {

 // ----- Navbar Active Link -----
 setActiveNavLink();

 // ----- Scroll Fade-in Animation -----
 initFadeInObserver();

 // ----- Order Form + Table (form.html only) -----
 if (document.getElementById('orderForm')) {
  initOrderForm();
 }

 // ----- Gallery Lightbox (gallery.html only) -----
 if (document.querySelector('.gallery-item')) {
  initLightbox();
 }

 // ----- Product Filter (product.html only) -----
 if (document.querySelector('.filter-btn')) {
  initProductFilter();
 }

});

/* =============================================
  ACTIVE NAV LINK
  ============================================= */
function setActiveNavLink() {
 const currentPage = window.location.pathname.split('/').pop() || 'index.php';
 const navLinks = document.querySelectorAll('.nav-link');
 navLinks.forEach(link => {
  const href = link.getAttribute('href');
  if (href === currentPage || (currentPage === '' && href === 'index.php')) {
   link.classList.add('active');
  }
 });
}

/* =============================================
  FADE-IN INTERSECTION OBSERVER
  ============================================= */
function initFadeInObserver() {
 const elements = document.querySelectorAll('.fade-in-up');
 const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
   if (entry.isIntersecting) {
    entry.target.classList.add('visible');
    observer.unobserve(entry.target);
   }
  });
 }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

 elements.forEach(el => observer.observe(el));
}

/* =============================================
  ORDER FORM & TABLE
  ============================================= */
let orders = [];
let orderCount = 0;

function initOrderForm() {
 const form  = document.getElementById('orderForm');
 const tableBody = document.getElementById('orderTableBody');
 const emptyState = document.getElementById('emptyState');
 const totalOrders = document.getElementById('totalOrders');
 const exportBtn  = document.getElementById('exportBtn');
 const clearBtn  = document.getElementById('clearBtn');

 // Restore orders from sessionStorage
 const saved = sessionStorage.getItem('bnbOrders');
 if (saved) {
  orders = JSON.parse(saved);
  orderCount = orders.length;
  renderTable();
 }

 form.addEventListener('submit', (e) => {
  e.preventDefault();
  if (!form.checkValidity()) {
   form.classList.add('was-validated');
   return;
  }

  const nama  = document.getElementById('nama').value.trim();
  const email  = document.getElementById('email').value.trim();
  const produk = document.getElementById('produk').value;
  const jumlah = parseInt(document.getElementById('jumlah').value);
  const catatan = document.getElementById('catatan').value.trim() || '-';

  // Send via AJAX to save to MySQL database
  const formData = new URLSearchParams();
  formData.append('action', 'add_order');
  formData.append('nama', nama);
  formData.append('email', email);
  formData.append('produk', produk);
  formData.append('jumlah', jumlah);
  formData.append('catatan', catatan);

  fetch('form.php', {
   method: 'POST',
   headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
    'X-Requested-With': 'XMLHttpRequest'
   },
   body: formData.toString()
  })
  .then(response => response.json())
  .then(data => {
   if (data.success) {
    const order = {
     id:   ++orderCount,
     nama,
     email,
     produk,
     jumlah,
     catatan,
     waktu:  new Date().toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' })
    };

    orders.push(order);
    sessionStorage.setItem('bnbOrders', JSON.stringify(orders));
    renderTable();
    showToast(` Pesanan ${nama} berhasil ditambahkan!`);

    form.reset();
    form.classList.remove('was-validated');
   } else {
    showToast(`⚠️ ${data.message}`);
   }
  })
  .catch(err => {
   console.error('Error:', err);
   showToast('⚠️ Gagal mengirim pesanan ke server.');
  });
 });

 if (exportBtn) {
  exportBtn.addEventListener('click', exportCSV);
 }

 if (clearBtn) {
  clearBtn.addEventListener('click', () => {
   if (orders.length === 0) return;
   if (confirm('Yakin ingin menghapus semua data pesanan?')) {
    orders = [];
    orderCount = 0;
    sessionStorage.removeItem('bnbOrders');
    renderTable();
    showToast('️ Semua data pesanan telah dihapus.');
   }
  });
 }

 function renderTable() {
  tableBody.innerHTML = '';

  if (orders.length === 0) {
   emptyState.style.display = 'block';
   if (totalOrders) totalOrders.textContent = '0';
   return;
  }

  emptyState.style.display = 'none';
  if (totalOrders) totalOrders.textContent = orders.length;

  orders.forEach((o, idx) => {
   const row = document.createElement('tr');
   row.innerHTML = `
    <td><span class="fw-semibold" style="color:var(--brown-dark)">${o.id}</span></td>
    <td>
     <div class="fw-semibold" style="color:var(--text-dark)">${escHtml(o.nama)}</div>
     <div style="font-size:0.78rem;color:var(--text-mid)">${escHtml(o.email)}</div>
    </td>
    <td><span style="color:var(--brown-dark);font-weight:500">${escHtml(o.produk)}</span></td>
    <td><span class="badge" style="background:var(--brown-dark);color:var(--cream);border-radius:20px;padding:.3rem .8rem">${o.jumlah} pcs</span></td>
    <td style="font-size:0.85rem">${escHtml(o.catatan)}</td>
    <td style="font-size:0.78rem;color:var(--text-mid)">${o.waktu}</td>
    <td><button class="delete-btn" data-idx="${idx}"> Hapus</button></td>
   `;
   tableBody.appendChild(row);
  });

  // Delete buttons
  tableBody.querySelectorAll('.delete-btn').forEach(btn => {
   btn.addEventListener('click', () => {
    const i = parseInt(btn.getAttribute('data-idx'));
    const deletedName = orders[i].nama;
    orders.splice(i, 1);
    sessionStorage.setItem('bnbOrders', JSON.stringify(orders));
    renderTable();
    showToast(`️ Pesanan ${deletedName} dihapus.`);
   });
  });
 }

 function exportCSV() {
  if (orders.length === 0) {
   showToast('️ Tidak ada data untuk diekspor.'); return;
  }
  const headers = ['No', 'Nama', 'Email', 'Produk', 'Jumlah', 'Catatan', 'Waktu'];
  const rows  = orders.map(o => [o.id, o.nama, o.email, o.produk, o.jumlah, o.catatan, o.waktu]);
  const csv   = [headers, ...rows].map(r => r.map(v => `"${v}"`).join(',')).join('\n');
  const blob  = new Blob([csv], { type: 'text/csv' });
  const url   = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href = url; a.download = 'pesanan_bakenbrew.csv'; a.click();
  URL.revokeObjectURL(url);
  showToast(' Data berhasil diekspor sebagai CSV!');
 }

 function renderTable_init() { renderTable(); }
 renderTable_init();
}

/* =============================================
  GALLERY LIGHTBOX
  ============================================= */
function initLightbox() {
 const overlay = document.getElementById('lightboxOverlay');
 const lbImg  = document.getElementById('lightboxImg');
 const lbClose = document.getElementById('lightboxClose');
 const lbCaption = document.getElementById('lightboxCaption');

 if (!overlay) return;

 document.querySelectorAll('.gallery-item').forEach(item => {
  item.addEventListener('click', () => {
   const src   = item.querySelector('img').src;
   const caption = item.getAttribute('data-caption') || '';
   lbImg.src   = src;
   if (lbCaption) lbCaption.textContent = caption;
   overlay.classList.add('active');
   document.body.style.overflow = 'hidden';
  });
 });

 lbClose.addEventListener('click', closeLightbox);
 overlay.addEventListener('click', (e) => { if (e.target === overlay) closeLightbox(); });
 document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLightbox(); });

 function closeLightbox() {
  overlay.classList.remove('active');
  document.body.style.overflow = '';
 }
}

/* =============================================
  PRODUCT FILTER
  ============================================= */
function initProductFilter() {
 const filterBtns  = document.querySelectorAll('.filter-btn');
 const productCards = document.querySelectorAll('.product-item');

 function applyFilter(filter) {
  filterBtns.forEach(b => {
   if (b.getAttribute('data-filter') === filter) {
    b.classList.add('active');
   } else {
    b.classList.remove('active');
   }
  });

  productCards.forEach(card => {
   const cat = card.getAttribute('data-category');
   if (filter === 'all' || cat === filter) {
    card.style.display = 'block';
    card.style.animation = 'fadeIn 0.4s ease forwards';
   } else {
    card.style.display = 'none';
   }
  });
 }

 filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
   const filter = btn.getAttribute('data-filter');
   applyFilter(filter);
  });
 });

 function checkHash() {
  const hash = window.location.hash.substring(1);
  if (hash === 'bakery' || hash === 'coffee' || hash === 'non-coffee') {
   applyFilter(hash.replace('-', '')); // non-coffee uses data-category="noncoffee"
   const targetEl = document.getElementById(hash);
   if (targetEl) {
    setTimeout(() => {
     targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 200);
   }
  } else if (hash) {
   // Reset to all first to ensure target product is visible
   applyFilter('all');
   const targetEl = document.getElementById(hash);
   if (targetEl) {
    setTimeout(() => {
     targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
     // Premium subtle visual highlight feedback
     targetEl.style.transition = 'outline 0.3s ease';
     targetEl.style.outline = '2px solid var(--accent-gold)';
     targetEl.style.outlineOffset = '4px';
     setTimeout(() => {
      targetEl.style.outline = '2px solid transparent';
     }, 2500);
    }, 300);
   }
  }
 }

 window.addEventListener('hashchange', checkHash);
 // Small delay to allow document rendering and images load before scroll
 setTimeout(checkHash, 150);
}

/* =============================================
  TOAST NOTIFICATION
  ============================================= */
function showToast(message) {
 let toast = document.getElementById('successToast');
 if (!toast) {
  toast = document.createElement('div');
  toast.id = 'successToast';
  toast.className = 'success-toast';
  document.body.appendChild(toast);
 }
 toast.textContent = message;
 toast.classList.add('show');
 setTimeout(() => toast.classList.remove('show'), 3200);
}

/* =============================================
  HELPERS
  ============================================= */
function escHtml(str) {
 const div = document.createElement('div');
 div.appendChild(document.createTextNode(str));
 return div.innerHTML;
}

/* CSS keyframe inject for filter animation */
const style = document.createElement('style');
style.textContent = `@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }`;
document.head.appendChild(style);
