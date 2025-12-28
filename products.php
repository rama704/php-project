<?php
session_start();
require_once 'includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// ================== إعدادات Pagination ==================
$products_per_page = 2; // عدد المنتجات في كل صفحة
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $products_per_page;

// ================== فلترة البحث ==================
$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'price' => $_GET['price'] ?? '',
    'sort' => $_GET['sort'] ?? 'newest'
];

$price = $filters['price'];
$search = $filters['search'];
$category = $filters['category'];
$sort = $filters['sort'];

// بناء استعلام SQL ديناميكي
$sql = "SELECT * FROM products WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
$params = [];
$types = "";

// فلترة البحث والفئة والسعر
if ($filters['search']) {
    $sql .= " AND name LIKE ?";
    $count_sql .= " AND name LIKE ?";
    $params[] = "%{$filters['search']}%";
    $types .= "s";
}

if ($filters['category']) {
    $sql .= " AND category_id = ?";
    $count_sql .= " AND category_id = ?";
    $params[] = $filters['category'];
    $types .= "i";
}

if ($filters['price'] !== '') {
    $sql .= " AND price <= ?";
    $count_sql .= " AND price <= ?";
    $params[] = $filters['price'];
    $types .= "d";
}

// حساب إجمالي عدد المنتجات
$count_stmt = $conn->prepare($count_sql);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_products = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// ترتيب النتائج
$order = match ($filters['sort']) {
    'price_low' => 'price ASC',
    'price_high' => 'price DESC',
    'name' => 'name ASC',
    default => 'created_at DESC'
};
$sql .= " ORDER BY $order";

// إضافة LIMIT و OFFSET
$sql .= " LIMIT ? OFFSET ?";

// تنفيذ الاستعلام
$stmt = $conn->prepare($sql);
if ($params) {
    $types .= "ii";
    $params[] = $products_per_page;
    $params[] = $offset;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $products_per_page, $offset);
}
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
                    <?php if (isset($_SESSION['user_id'])): ?>
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
                        <input type="text" name="search" placeholder="Search products..."
                            value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <div class="filter-group">
                        <label><i class="fas fa-th-large"></i> Category</label>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $category ? 'selected' : '' ?>>
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
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>Name A-Z</option>
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
                <p>Showing <strong><?= min($offset + 1, $total_products) ?> - <?= min($offset + $products_per_page, $total_products) ?></strong> of <strong><?= $total_products ?></strong> products</p>
            </div>

            <!-- ================= PRODUCTS GRID ================= -->
            <div class="products-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
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
                            <?php if ($hasDiscount): ?>
                                <span class="product-badge">-<?= $discountPercent ?>%</span>
                            <?php endif; ?>

                            <div class="product-image">
                                <div class="product-img">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="../index/images/<?= htmlspecialchars($product['image']) ?>"
                                            alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php else: ?>
                                        <i class="fas fa-box"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-overlay">
                                    <a href="product_details.php?id=<?= $product['id'] ?>" class="btn-view">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    <form action="add_to_cart.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn-cart">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="product-price">
                                    <span class="current-price">£<?= number_format($finalPrice, 2) ?></span>
                                    <?php if ($hasDiscount): ?>
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
                        <a href="products.php" class="btn btn-primary">View All Products</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ================= PAGINATION ================= -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php
                    // بناء URL للحفاظ على الفلاتر
                    $query_params = $_GET;
                    unset($query_params['page']);
                    $base_url = 'products.php?' . http_build_query($query_params);
                    $separator = empty($query_params) ? '' : '&';
                    ?>

                    <!-- زر Previous -->
                    <?php if ($current_page > 1): ?>
                        <a href="<?= $base_url . $separator ?>page=<?= $current_page - 1 ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>

                    <!-- أرقام الصفحات -->
                    <div class="pagination-numbers">
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        // إظهار الصفحة الأولى دائماً
                        if ($start_page > 1): ?>
                            <a href="<?= $base_url . $separator ?>page=1" class="pagination-number">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-dots">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- الصفحات المحيطة بالصفحة الحالية -->
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?= $base_url . $separator ?>page=<?= $i ?>" 
                               class="pagination-number <?= $i == $current_page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- إظهار الصفحة الأخيرة دائماً -->
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <span class="pagination-dots">...</span>
                            <?php endif; ?>
                            <a href="<?= $base_url . $separator ?>page=<?= $total_pages ?>" class="pagination-number">
                                <?= $total_pages ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- زر Next -->
                    <?php if ($current_page < $total_pages): ?>
                        <a href="<?= $base_url . $separator ?>page=<?= $current_page + 1 ?>" class="pagination-btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

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
        }
    </script>

</body>

</html>