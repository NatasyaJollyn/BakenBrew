import re

filepath = r"d:\KULIAH\TUGAS\SEMESTER 4\PEMROGRAMAN WEB\EAS PEMWEB\product.php"

with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Inject PHP initialization at the very top
php_init = """<?php
require_once 'config/koneksi.php';

try {
    $stmt = $pdo->query("SELECT * FROM `products` ORDER BY `id` ASC");
    $all_products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal memuat produk: " . $e->getMessage());
}

$bakery_products = [];
$coffee_products = [];
$non_coffee_products = [];

foreach ($all_products as $p) {
    if ($p['category'] === 'bakery') $bakery_products[] = $p;
    elseif ($p['category'] === 'coffee') $coffee_products[] = $p;
    elseif ($p['category'] === 'non-coffee') $non_coffee_products[] = $p;
}

function renderProductCard($p, $delayIndex) {
    $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $p['name'])));
    
    $img_src = htmlspecialchars($p['image']);
    if (!empty($p['image']) && !str_starts_with($p['image'], 'http')) {
        $img_src = 'public/images/products/' . htmlspecialchars($p['image']);
    }
    
    $price_formatted = 'Rp ' . number_format($p['price'], 0, ',', '.');
    
    $badge_html = '';
    if ($p['is_bestseller']) {
        $badge_html .= '<span class="badge-bestseller">Best Seller</span>';
    }
    if ($p['is_new']) {
        $badge_html .= '<span class="badge-new">New</span>';
    }
    
    $badge_container = '';
    if (!empty($badge_html)) {
        $badge_container = '<div style="position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap;">' . $badge_html . '</div>';
    }
    
    $delay_class = 'delay-' . (($delayIndex % 4) + 1);
    $category_attr = ($p['category'] === 'non-coffee') ? 'noncoffee' : htmlspecialchars($p['category']);

    echo '
    <div class="col-sm-6 col-lg-3 fade-in-up ' . $delay_class . ' product-item" data-category="' . $category_attr . '" id="' . $slug . '">
     <div class="product-card card h-100">
      <div style="overflow:hidden;position:relative;">
       <img src="' . $img_src . '" class="card-img-top" alt="' . htmlspecialchars($p['name']) . '" loading="lazy" />
       ' . $badge_container . '
      </div>
      <div class="card-body d-flex flex-column">
       <h5 class="card-title">' . htmlspecialchars($p['name']) . '</h5>
       <p class="card-text flex-grow-1">' . htmlspecialchars($p['description']) . '</p>
       <div class="d-flex justify-content-between align-items-center mt-3">
        <span class="price">' . $price_formatted . '</span>
        <a href="form.php" class="btn-primary-brown" style="padding:.45rem 1rem;font-size:.8rem;">Order</a>
       </div>
      </div>
     </div>
    </div>';
}
?>
"""

if not content.startswith("<?php"):
    content = php_init + content

# 2. Replace Bakery product container contents
# Locate the section for Bakery: id="bakery" -> class="row g-4 mb-5"
# We want to replace the static HTML within the row
bakery_row_match = re.search(r'(<!-- === BAKERY === -->.*?<div class="row g-4 mb-5">)(.*?)(</div>.*?<!-- === COFFEE === -->)', content, re.DOTALL)
if bakery_row_match:
    prefix = bakery_row_match.group(1)
    suffix = bakery_row_match.group(3)
    dynamic_bakery = """
   <?php 
   if (count($bakery_products) > 0) {
       foreach ($bakery_products as $idx => $p) {
           renderProductCard($p, $idx);
       }
   } else {
       echo '<div class="col-12 text-center text-muted py-4">Tidak ada menu bakery tersedia.</div>';
   }
   ?>
  """
    content = content.replace(bakery_row_match.group(0), prefix + dynamic_bakery + suffix)

# 3. Replace Coffee product container contents
coffee_row_match = re.search(r'(<!-- === COFFEE === -->.*?<div class="row g-4 mb-5">)(.*?)(</div>.*?<!-- === NON-COFFEE === -->)', content, re.DOTALL)
if coffee_row_match:
    prefix = coffee_row_match.group(1)
    suffix = coffee_row_match.group(3)
    dynamic_coffee = """
   <?php 
   if (count($coffee_products) > 0) {
       foreach ($coffee_products as $idx => $p) {
           renderProductCard($p, $idx);
       }
   } else {
       echo '<div class="col-12 text-center text-muted py-4">Tidak ada menu kopi tersedia.</div>';
   }
   ?>
  """
    content = content.replace(coffee_row_match.group(0), prefix + dynamic_coffee + suffix)

# 4. Replace Non-Coffee product container contents
noncoffee_row_match = re.search(r'(<!-- === NON-COFFEE === -->.*?<div class="row g-4">)(.*?)(</div>\s*<!-- CTA -->)', content, re.DOTALL)
if noncoffee_row_match:
    prefix = noncoffee_row_match.group(1)
    suffix = noncoffee_row_match.group(3)
    dynamic_noncoffee = """
   <?php 
   if (count($non_coffee_products) > 0) {
       foreach ($non_coffee_products as $idx => $p) {
           renderProductCard($p, $idx);
       }
   } else {
       echo '<div class="col-12 text-center text-muted py-4">Tidak ada menu non-kopi tersedia.</div>';
   }
   ?>
  """
    content = content.replace(noncoffee_row_match.group(0), prefix + dynamic_noncoffee + suffix)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Successfully injected dynamic product loading into product.php!")
