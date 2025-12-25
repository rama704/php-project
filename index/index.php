<?php
session_start(); // مهم جداً لتتبع المستخدم
require_once '../includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

// جلب أعلى 4 منتجات عليها خصم
$query = "SELECT id, category_id, name, description, price, discount_price, image 
          FROM products 
          WHERE discount_price IS NOT NULL 
          ORDER BY created_at DESC 
          LIMIT 4";

$result = $conn->query($query);
$discountProducts = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $discountProducts[] = $row;
    }
}
// جلب المنتجات للسلايدز الرئيسي
$queryslides="SELECT title ,subtitle,badge,image,link FROM slides order by id ASC";
$resultslides=$conn->query($queryslides);
$slidesdata=[];
if($resultslides->num_rows>0){
    while($row=$resultslides->fetch_assoc()){
        $slidesdata[]=$row;
    }
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Techify - Electronics Store</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar">
    <div class="container">
        <div class="nav-wrapper">

            <span class="logo-text">Techify</span>

            <ul class="nav-menu" id="navMenu">
                <li><a href="#home" class="active">HOME</a></li>
                <li><a href="#shop">SHOP</a></li>
                <li><a href="#hotdiscounts">HOT DISCOUNTS</a></li>
                <li><a href="#features">FEATURES</a></li>
                <li><a href="#contact">CONTACT US</a></li>
            </ul>
         
            <div class="nav-icons">
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- المستخدم مسجل -->
                    <a href="../profile/profile1.php">Profile</a>
                    <a href="../login.php" class="icon-btn"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <!-- المستخدم مش مسجل -->
                    <a href="../login.php">Login</a>
                    <a href="../register.php" >register</a>
                <?php endif; ?>
                                <button class="icon-btn search-btn"><i class="fas fa-search"></i></button>

                <button class="icon-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </button>
            </div>

        </div>
    </div>
</nav>

<!-- ================= HERO / HOME ================= -->
<section class="hero-slider" id="home">
    <?php foreach($slidesdata as $index => $slide): ?>
        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <div class="container">
                <div class="slide-content">
                    <div class="slide-text">
                        <span class="badge"><?php echo htmlspecialchars($slide['badge']); ?></span>
                        <h1><?php echo htmlspecialchars($slide['title']); ?></h1>
                        <h2><?php echo htmlspecialchars($slide['subtitle']); ?></h2>
                        <a href="<?php echo $slide['link']; ?>" class="btn btn-primary">Shop Now</a>
                    </div>
                    <div class="slide-image">
                        <div class="speaker-main">
                            <img src="images/<?php echo $slide['image']; ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <button class="slider-arrow left" onclick="changeSlide(-1)">❮</button>
    <button class="slider-arrow right" onclick="changeSlide(1)">❯</button>

    <div class="slider-dots">
        <?php foreach($slidesdata as $index => $slide): ?>
            <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></span>
        <?php endforeach; ?>
    </div>
</section>



<!-- ================= DISCOUNTS / SHOP ================= -->
<section class="discounts" id="hotdiscounts">
    <div class="container">
        <div class="section-header">
            <h2>Hot Discounts</h2>
        </div>

        <div class="products-grid">
            <?php if(!empty($discountProducts)): ?>
                <?php foreach($discountProducts as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php 
                                $discountPercent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100); 
                            ?>
                            <span class="product-badge">-<?php echo $discountPercent; ?>%</span>
                            <div class="product-img">
                                <?php if(!empty($product['image'])): ?>
                                    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php else: ?>
                                    📦
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-price">
                                <span class="current-price">£<?php echo $product['discount_price']; ?></span>
                                <span class="old-price">£<?php echo $product['price']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No discounted products available.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ================= FEATURES ================= -->
<section class="features" id="features">
    <div class="container">
        <div class="features-grid">

            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-truck"></i></div>
                <div class="feature-text">
                    <h3>FREE SHIPPING</h3>
                    <p>Fast worldwide delivery</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <div class="feature-text">
                    <h3>24/7 SUPPORT</h3>
                    <p>We are always here to help</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="feature-text">
                    <h3>MONEY BACK</h3>
                    <p>30 days guarantee</p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ================= FOOTER / CONTACT ================= -->
<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-grid">

            <div class="footer-col">
                <h3>Techify</h3>
                <p>
                    Your trusted electronics store for laptops,
                    headphones and the latest technology.
                </p>
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
                    <li><a href="#home">Home</a></li>
                    <li><a href="#shop">Shop</a></li>
                                        <li><a href="#hotdiscounts">Hot Discount</a></li>

                    <li><a href="#features">Features</a></li>
                    <li><a href="#contact">Contact</a></li>

                </ul>
            </div>

        </div>

       
    </div>
</footer>


<script src="script.js"></script>
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slider .slide');
const dots = document.querySelectorAll('.slider-dots .dot');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle('active', i === index);
    });
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
    currentSlide = index;
}

function changeSlide(direction) {
    let nextSlide = currentSlide + direction;
    if (nextSlide >= slides.length) nextSlide = 0;
    if (nextSlide < 0) nextSlide = slides.length - 1;
    showSlide(nextSlide);
}

function goToSlide(index) {
    showSlide(index);
}

// تغيير السلايد تلقائياً كل 5 ثواني
setInterval(() => {
    changeSlide(1);
}, 5000);
</script>

</body>
</html>
