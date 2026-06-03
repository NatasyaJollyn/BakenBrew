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

  // ----- Real-time Polling & Order Sync -----
  startStoreStatusPolling();
  if (document.getElementById('orderForm')) {
    startOrdersSyncPolling();
  }
});

/* =============================================
   REAL-TIME STORE STATUS POLLING
   ============================================= */
function startStoreStatusPolling() {
  const checkStatus = () => {
    fetch('config/get_store_status.php')
      .then(response => response.json())
      .then(data => {
        window.storeStatus = data.status;
        
        const badge = document.querySelector('.navbar-brand .badge');
        if (badge) {
          const oldText = badge.textContent.trim();
          const newText = data.status === 'open' ? 'Buka' : 'Tutup';
          
          if (oldText !== newText || !badge.classList.contains('badge-flip-active')) {
            if (data.status === 'open') {
              badge.textContent = 'Buka';
              badge.className = 'badge bg-success ms-2';
            } else {
              badge.textContent = 'Tutup';
              badge.className = 'badge bg-danger ms-2';
            }
            
            // Trigger 3D Flip Animation
            badge.classList.remove('badge-flip-active');
            void badge.offsetWidth; // force reflow/repaint
            badge.classList.add('badge-flip-active');
          }
        }
        
        // Handle index.php hero alert banner dynamically
        const heroContent = document.querySelector('.hero-content');
        if (heroContent) {
          let homeBanner = document.getElementById('home-closed-banner');
          if (data.status === 'closed') {
            if (!homeBanner) {
              homeBanner = document.createElement('div');
              homeBanner.id = 'home-closed-banner';
              homeBanner.className = 'alert alert-danger d-flex align-items-center gap-2 mb-3 fade-in-up visible';
              homeBanner.style.cssText = 'border-radius: var(--radius-sm); font-size: 0.85rem; background-color: rgba(220, 53, 69, 0.15); border: 1px solid rgba(220, 53, 69, 0.25); color: #FAF6F0; padding: 0.5rem 1rem; font-family: "Poppins", sans-serif; width: fit-content;';
              homeBanner.innerHTML = `<i class="bi bi-shop"></i> Kami sedang Tutup. Pemesanan dinonaktifkan sementara.`;
              heroContent.insertBefore(homeBanner, heroContent.querySelector('.hero-badge'));
            }
          } else {
            if (homeBanner) {
              homeBanner.remove();
            }
          }
        }
        
        // Handle form.php locking/unlocking dynamically
        const form = document.getElementById('orderForm');
        if (form) {
          const formInputs = form.querySelectorAll('input, select, textarea, button');
          let alertBanner = document.querySelector('.form-table-section .alert-danger');
          
          if (data.status === 'closed') {
            formInputs.forEach(input => input.disabled = true);
            
            if (!alertBanner) {
              alertBanner = document.createElement('div');
              alertBanner.className = 'alert alert-danger d-flex align-items-center gap-3 mb-4 fade-in-up';
              alertBanner.style.cssText = 'border-radius: 12px; border: 1px solid #f5c2c7; background-color: #f8d7da; color: #842029; padding: 1rem 1.5rem;';
              alertBanner.innerHTML = `
                <i class="bi bi-shop" style="font-size: 1.8rem; color: #842029;"></i>
                <div>
                  <h5 class="alert-heading fw-bold mb-1" style="font-size: 1rem; margin: 0;">Maaf, Toko Kami Sedang Tutup</h5>
                  <p class="mb-0" style="font-size: 0.82rem; margin: 0; opacity: 0.9;">Saat ini kami tidak menerima pesanan baru sementara waktu. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                </div>
              `;
              const container = form.closest('.col-lg-7');
              if (container) {
                container.insertBefore(alertBanner, form.closest('.order-form-card'));
              }
            }
          } else {
            if (alertBanner) {
              alertBanner.remove();
            }
            formInputs.forEach(input => input.disabled = false);
          }
        }
      })
      .catch(err => console.error('Status check failed:', err));
  };

  checkStatus();
  setInterval(checkStatus, 3000);

  // Hook global click event on order links when closed
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[href*="form.php"]');
    if (link && window.storeStatus === 'closed') {
      e.preventDefault();
      showStoreClosedModal();
    }
  });
}

/* =============================================
   AESTHETIC STORE CLOSED POP-UP MODAL
   ============================================= */
function showStoreClosedModal() {
  const existing = document.getElementById('storeClosedModal');
  if (existing) existing.remove();

  const modal = document.createElement('div');
  modal.id = 'storeClosedModal';
  modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); background-color: rgba(75, 46, 43, 0.45); opacity: 0; transition: opacity 0.3s ease;';
  
  modal.innerHTML = `
    <div style="background: #ffffff; border-radius: 20px; border: 1px solid #D6BFAF; box-shadow: 0 15px 40px rgba(0,0,0,0.15); padding: 2.5rem 2rem; max-width: 440px; width: 90%; text-align: center; transform: scale(0.8); transition: transform 0.3s ease; font-family: 'Poppins', sans-serif;">
      <div style="width: 72px; height: 72px; background: #f8d7da; color: #d32f2f; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 2.2rem; box-shadow: 0 4px 10px rgba(211, 47, 47, 0.15);">
        <i class="bi bi-shop"></i>
      </div>
      <h4 style="font-family: 'Playfair Display', serif; color: #4B2E2B; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 1.5rem;">Toko Sedang Tutup</h4>
      <p style="color: #6c757d; font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.8rem;">
        Maaf, saat ini Bake'n Brew sedang tidak menerima pesanan baru secara online. Silakan kunjungi kami kembali saat jam operasional atau hubungi kontak kami.
      </p>
      <div style="display: flex; gap: 12px; justify-content: center;">
        <a href="contact.php" style="background: transparent; color: #4B2E2B; border: 1.5px solid #4B2E2B; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 600; text-decoration: none; font-size: 0.85rem; transition: all 0.2s;">Hubungi Kami</a>
        <button id="closeStoreClosedModal" style="background: #4B2E2B; color: #FAF6F0; border: none; padding: 0.6rem 1.8rem; border-radius: 30px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;">Tutup</button>
      </div>
    </div>
  `;

  document.body.appendChild(modal);

  setTimeout(() => {
    modal.style.opacity = '1';
    modal.querySelector('div').style.transform = 'scale(1)';
  }, 10);

  const close = () => {
    modal.style.opacity = '0';
    modal.querySelector('div').style.transform = 'scale(0.8)';
    setTimeout(() => {
      modal.remove();
    }, 300);
  };
  
  modal.querySelector('#closeStoreClosedModal').addEventListener('click', close);
  modal.addEventListener('click', (e) => {
    if (e.target === modal) close();
  });
}

/* =============================================
   REAL-TIME ACTIVE ORDERS SYNCHRONIZATION
   ============================================= */
function startOrdersSyncPolling() {
  setInterval(() => {
    if (orders.length === 0) return;
    
    // Collect all database IDs (db_id)
    const dbIds = orders.filter(o => o.db_id).map(o => o.db_id);
    if (dbIds.length === 0) return;
    
    fetch(`config/check_active_orders.php?ids=${dbIds.join(',')}`)
      .then(response => response.json())
      .then(data => {
        const activeIds = data.active_ids || [];
        
        // Find if any of our orders are no longer active
        let changed = false;
        const remainingOrders = [];
        
        orders.forEach(o => {
          if (!o.db_id || activeIds.includes(parseInt(o.db_id))) {
            remainingOrders.push(o);
          } else {
            changed = true;
            // Visual effect: find row and animate removal
            const rows = document.querySelectorAll('#orderTableBody tr');
            rows.forEach(row => {
              const deleteBtn = row.querySelector('.delete-btn');
              if (deleteBtn) {
                const idx = parseInt(deleteBtn.getAttribute('data-idx'));
                if (orders[idx] && orders[idx].db_id === o.db_id) {
                  row.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                  row.style.opacity = '0';
                  row.style.transform = 'translateX(-20px)';
                  setTimeout(() => {
                    row.remove();
                  }, 500);
                }
              }
            });
          }
        });
        
        if (changed) {
          orders = remainingOrders;
          sessionStorage.setItem('bnbOrders', JSON.stringify(orders));
          setTimeout(() => {
            renderTable();
          }, 600);
        }
      })
      .catch(err => console.error('Orders sync failed:', err));
  }, 3000);
}

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
     db_id: data.id,
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

  const sections = {
   bakery: {
    header: document.getElementById('bakery'),
    row: document.getElementById('bakery') ? document.getElementById('bakery').nextElementSibling : null
   },
   coffee: {
    header: document.getElementById('coffee'),
    row: document.getElementById('coffee') ? document.getElementById('coffee').nextElementSibling : null
   },
   noncoffee: {
    header: document.getElementById('non-coffee'),
    row: document.getElementById('non-coffee') ? document.getElementById('non-coffee').nextElementSibling : null
   }
  };

  function applyFilter(filter) {
   filterBtns.forEach(b => {
    if (b.getAttribute('data-filter') === filter) {
     b.classList.add('active');
    } else {
     b.classList.remove('active');
    }
   });

   // Toggle category section headers and rows visibility
   for (const key in sections) {
    const sec = sections[key];
    if (sec.header && sec.row) {
     if (filter === 'all' || key === filter) {
      sec.header.style.display = '';
      sec.row.style.display = '';
     } else {
      sec.header.style.display = 'none';
      sec.row.style.display = 'none';
     }
    }
   }

   productCards.forEach(card => {
    const cat = card.getAttribute('data-category');
    if (filter === 'all' || cat === filter) {
     card.style.display = '';
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
