import re

filepath = r"d:\KULIAH\TUGAS\SEMESTER 4\PEMROGRAMAN WEB\EAS PEMWEB\form.php"

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Inject PHP initialization at the very top
php_init = """<?php
require_once 'config/koneksi.php';
session_start();

// Fetch Store Status
$store_status_stmt = $pdo->query("SELECT `setting_value` FROM `settings` WHERE `setting_key` = 'store_status'");
$store_status = $store_status_stmt->fetchColumn() ?: 'open';

// Handle AJAX Request to Save Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_order') {
    header('Content-Type: application/json');
    
    if ($store_status === 'closed') {
        echo json_encode(['success' => false, 'message' => 'Toko sedang tutup. Pemesanan tidak dapat diproses.']);
        exit;
    }
    
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $produk = trim($_POST['produk']);
    $jumlah = intval($_POST['jumlah']);
    $catatan = trim($_POST['catatan']);
    
    if (!empty($nama) && !empty($email) && !empty($produk) && $jumlah > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO `orders` (`customer_name`, `customer_email`, `product_name`, `quantity`, `note`, `status`) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$nama, $email, $produk, $jumlah, $catatan]);
            echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dikirim ke database.']);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ke database: ' . $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data pesanan tidak lengkap atau tidak valid.']);
        exit;
    }
}

// Fetch products grouped by category for dropdown options
try {
    $stmt = $pdo->query("SELECT * FROM `products` ORDER BY `id` ASC");
    $all_products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal memuat data produk: " . $e->getMessage());
}

$bakery_opts = [];
$coffee_opts = [];
$non_coffee_opts = [];

foreach ($all_products as $p) {
    if ($p['category'] === 'bakery') $bakery_opts[] = $p;
    elseif ($p['category'] === 'coffee') $coffee_opts[] = $p;
    elseif ($p['category'] === 'non-coffee') $non_coffee_opts[] = $p;
}
?>
"""

if not content.startswith("<?php"):
    content = php_init + content

# 2. Inject Alert Banner above the form card
form_card_str = '<!-- ORDER FORM CARD -->'
alert_banner = """<?php if ($store_status === 'closed'): ?>
     <!-- Toko Tutup Alert Banner -->
     <div class="alert alert-danger d-flex align-items-center gap-3 mb-4 fade-in-up" role="alert" style="border-radius: 12px; border: 1px solid #f5c2c7; background-color: #f8d7da; color: #842029; padding: 1rem 1.5rem;">
      <i class="bi bi-shop" style="font-size: 1.8rem; color: #842029;"></i>
      <div>
       <h5 class="alert-heading fw-bold mb-1" style="font-size: 1rem; margin: 0;">Maaf, Toko Kami Sedang Tutup</h5>
       <p class="mb-0" style="font-size: 0.82rem; margin: 0; opacity: 0.9;">Saat ini kami tidak menerima pesanan baru sementara waktu. Silakan hubungi kami untuk informasi lebih lanjut.</p>
      </div>
     </div>
     <?php endif; ?>
     
     <!-- ORDER FORM CARD -->"""

content = content.replace(form_card_str, alert_banner)

# 3. Replace static dropdown
dropdown_regex = r'<select class="form-select" id="produk" required>.*?</select>'
dynamic_dropdown = """<select class="form-select" id="produk" required <?= $store_status === 'closed' ? 'disabled' : '' ?>>
         <option value="">— Pilih produk —</option>
         <?php if (count($bakery_opts) > 0): ?>
          <optgroup label=" Bakery">
           <?php foreach ($bakery_opts as $p): 
              $val = $p['name'] . ' (Rp ' . number_format($p['price'], 0, ',', '.') . ')';
              $lbl = $p['name'] . ' – Rp ' . number_format($p['price'], 0, ',', '.');
           ?>
            <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lbl) ?></option>
           <?php endforeach; ?>
          </optgroup>
         <?php endif; ?>
         <?php if (count($coffee_opts) > 0): ?>
          <optgroup label=" Coffee">
           <?php foreach ($coffee_opts as $p): 
              $val = $p['name'] . ' (Rp ' . number_format($p['price'], 0, ',', '.') . ')';
              $lbl = $p['name'] . ' – Rp ' . number_format($p['price'], 0, ',', '.');
           ?>
            <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lbl) ?></option>
           <?php endforeach; ?>
          </optgroup>
         <?php endif; ?>
         <?php if (count($non_coffee_opts) > 0): ?>
          <optgroup label=" Non-Coffee">
           <?php foreach ($non_coffee_opts as $p): 
              $val = $p['name'] . ' (Rp ' . number_format($p['price'], 0, ',', '.') . ')';
              $lbl = $p['name'] . ' – Rp ' . number_format($p['price'], 0, ',', '.');
           ?>
            <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($lbl) ?></option>
           <?php endforeach; ?>
          </optgroup>
         <?php endif; ?>
        </select>"""

content = re.sub(dropdown_regex, dynamic_dropdown, content, flags=re.DOTALL)

# 4. Disable submit buttons if closed
button_group_regex = r'<!-- Submit -->.*?</div>'
disabled_buttons = """<!-- Submit -->
        <div class="col-12 d-flex gap-3 flex-wrap">
         <button type="submit" class="btn-primary-brown flex-grow-1" style="border:none;" <?= $store_status === 'closed' ? 'disabled' : '' ?>>
          <i class="bi bi-cart-plus me-2"></i>Tambah Pesanan
         </button>
         <button type="reset" class="btn-outline-brown" style="min-width:120px;" onclick="document.getElementById('orderForm').classList.remove('was-validated')" <?= $store_status === 'closed' ? 'disabled' : '' ?>>
          <i class="bi bi-x-circle me-1"></i>Reset
         </button>
        </div>"""

content = re.sub(button_group_regex, disabled_buttons, content, flags=re.DOTALL)

# 5. Disable name, email, jumlah, and catatan fields if closed
name_field_regex = r'<input type="text" class="form-control" id="nama"\s*placeholder="Contoh: Sari Dewi"\s*minlength="2" required />'
disabled_name_field = '<input type="text" class="form-control" id="nama" placeholder="Contoh: Sari Dewi" minlength="2" required <?= $store_status === \'closed\' ? \'disabled\' : \'\' ?> />'
content = content.replace(name_field_regex, disabled_name_field)

email_field_regex = r'<input type="email" class="form-control" id="email"\s*placeholder="emailkamu@gmail.com" required />'
disabled_email_field = '<input type="email" class="form-control" id="email" placeholder="emailkamu@gmail.com" required <?= $store_status === \'closed\' ? \'disabled\' : \'\' ?> />'
content = content.replace(email_field_regex, disabled_email_field)

jumlah_field_regex = r'<input type="number" class="form-control" id="jumlah"\s*placeholder="Contoh: 2"\s*min="1" max="100" required />'
disabled_jumlah_field = '<input type="number" class="form-control" id="jumlah" placeholder="Contoh: 2" min="1" max="100" required <?= $store_status === \'closed\' ? \'disabled\' : \'\' ?> />'
content = content.replace(jumlah_field_regex, disabled_jumlah_field)

catatan_field_regex = r'<textarea class="form-control" id="catatan" rows="3"\s*placeholder="Contoh: tanpa gula, ekstra keju, diantar ke meja 5..."\s*style="resize:none;"></textarea>'
disabled_catatan_field = '<textarea class="form-control" id="catatan" rows="3" placeholder="Contoh: tanpa gula, ekstra keju, diantar ke meja 5..." style="resize:none;" <?= $store_status === \'closed\' ? \'disabled\' : \'\' ?>></textarea>'
content = content.replace(catatan_field_regex, disabled_catatan_field)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Successfully injected dynamic fields and closure rules into form.php!")
