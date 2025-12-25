<?php
session_start();
require_once 'includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// ================== فلترة البحث ==================
$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'price' => $_GET['price'] ?? '',
    'sort' => $_GET['sort'] ?? 'newest'
];

$price = $filters['price']; // متغير للسعر للاستخدام في الـ HTML
$search = $filters['search'];
$category = $filters['category'];
$sort = $filters['sort'];

// بناء استعلام SQL ديناميكي
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

// فلترة البحث والفئة والسعر
if ($filters['search']) { 
    $sql .= " AND name LIKE ?"; 
    $params[] = "%{$filters['search']}%"; 
    $types .= "s"; 
}

if ($filters['category']) { 
    $sql .= " AND category_id = ?"; 
    $params[] = $filters['category']; 
    $types .= "i"; 
}

if ($filters['price'] !== '') {
    $sql .= " AND price <= ?"; 
    $params[] = $filters['price']; 
    $types .= "d"; 
}

// ترتيب النتائج
$order = match($filters['sort']) {
    'price_low' => 'price ASC',
    'price_high' => 'price DESC',
    'name' => 'name ASC',
    default => 'created_at DESC'
};
$sql .= " ORDER BY $order";

// تنفيذ الاستعلام
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// جلب الفئات
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Techify</title>
    <link rel="stylesheet" href="products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar">
    <div class="container">
        <div class="nav-wrapper">
            <span class="logo-text">Techify</span>
            <ul class="nav-menu" id="navMenu">
                <li><a href="../php-project/index/index.php">HOME</a></li>
                <li><a href="../php-project/products.php" class="active">SHOP</a></li>
                <li><a href="../php-project/index/index.php#hotdiscounts">HOT DISCOUNTS</a></li>
                <li><a href="../php-project/index/index.php#features">FEATURES</a></li>
                <li><a href="../php-project/index/index.php#contact">CONTACT US</a></li>
            </ul>
            <div class="nav-icons">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="../PHP-PROJECT/profile/profile1.php">Profile</a>
                    <a href="../login.php" class="icon-btn"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="../login.php">Login</a>
                    <a href="../register.php">Register</a>
                <?php endif; ?>
             
                <button class="icon-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- ================= PAGE HEADER ================= -->
<section class="page-header">
    <div class="container">
        <h1>Our Products</h1>
        <p>Discover the latest electronics and technology</p>
    </div>
</section>

<!-- ================= PRODUCTS SECTION ================= -->
<section class="products-section">
    <div class="container">
        
        <!-- ================= FILTERS ================= -->
        <div class="filters-wrapper">
            <form method="GET" action="products.php" class="filters-form">
                
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="filter-group">
                    <label><i class="fas fa-th-large"></i> Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id']==$category?'selected':'' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="fas fa-dollar-sign"></i> Price</label>
                    <input type="number" name="price" placeholder="0" value="<?= htmlspecialchars($price) ?>">
                </div>

                <div class="filter-group">
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <select name="sort">
                        <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest First</option>
                        <option value="price_low" <?= $sort=='price_low'?'selected':'' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort=='price_high'?'selected':'' ?>>Price: High to Low</option>
                        <option value="name" <?= $sort=='name'?'selected':'' ?>>Name A-Z</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- ================= RESULTS INFO ================= -->
        <div class="results-info">
            <p>Showing <strong><?= count($products) ?></strong> products</p>
        </div>

        <!-- ================= PRODUCTS GRID ================= -->
        <div class="products-grid">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <?php 
                        $finalPrice = isset($product['discount_price']) && $product['discount_price'] 
                            ? $product['discount_price'] 
                            : $product['price'];
                        $hasDiscount = isset($product['discount_price']) && $product['discount_price'] < $product['price'];
                        $discountPercent = $hasDiscount 
                            ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) 
                            : 0;
                    ?>
                    <div class="product-card">
                        <?php if($hasDiscount): ?>
                            <span class="product-badge">-<?= $discountPercent ?>%</span>
                        <?php endif; ?>
                        
                        <div class="product-image">
                            <div class="product-img">
                                <?php if(!empty($product['image'])): ?>
                                    <img src="images/<?= htmlspecialchars($product['image']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-box"></i>
                                <?php endif; ?>
                            </div>
                            <div class="product-overlay">
                                <a href="product_details.php?id=<?= $product['id'] ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <button class="btn-cart" onclick="addToCart(<?= $product['id'] ?>)">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        
                        <div class="product-info">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-price">
                                <span class="current-price">£<?= number_format($finalPrice, 2) ?></span>
                                <?php if($hasDiscount): ?>
                                    <span class="old-price">£<?= number_format($product['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>No Products Found</h3>
                    <p>Try adjusting your filters or search terms</p>
                    <a href="product_details.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Techify</h3>
                <p>Your trusted electronics store for laptops, headphones and the latest technology.</p>
            </div>

            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-list">
                    <li>📍 Amman, Jordan</li>
                    <li>📞 +962 7 0000 0000</li>
                    <li>✉️ support@techify.com</li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-list">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Shop</a></li>
                    <li><a href="index.php#hotdiscounts">Hot Discount</a></li>
                    <li><a href="index.php#features">Features</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<script>
function addToCart(productId) {
    alert('Product added to cart! (Product ID: ' + productId + ')');
    // هنا يمكنك إضافة كود AJAX لإضافة المنتج للسلة
}
</script>

</body>
</html>
