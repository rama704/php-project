<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - Techify</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #000;
    color: #fff;
    min-height: 100vh;
}
.navbar {
    background: rgba(0, 0, 0, 0.95);
    padding: 20px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(254, 164, 11, 0.2);
    backdrop-filter: blur(10px);
}
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
.nav-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.logo-text {
    font-size: 28px;
    font-weight: 700;
    background: linear-gradient(135deg, #fea40b, #84641c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 2px;
}
.nav-menu {
    display: flex;
    list-style: none;
    gap: 30px;
}
.nav-menu a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    position: relative;
}
.nav-menu a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #fea40b, #84641c);
    transition: width 0.3s ease;
}
.nav-menu a:hover,
.nav-menu a.active {
    color: #fea40b;
}
.nav-menu a:hover::after,
.nav-menu a.active::after {
    width: 100%;
}
.nav-icons {
    display: flex;
    gap: 15px;
}
.icon-btn {
    background: transparent;
    border: 1px solid rgba(254, 164, 11, 0.3);
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}
.icon-btn:hover {
    background: linear-gradient(135deg, #fea40b, #84641c);
    border-color: #fea40b;
    transform: translateY(-2px);
}
.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4444;
    color: #fff;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
}
.profile-section {
    padding: 80px 0;
    min-height: calc(100vh - 200px);
}
.profile-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
.profile-sidebar {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    border-radius: 20px;
    padding: 30px;
    height: fit-content;
    border: 1px solid rgba(254, 164, 11, 0.2);
    box-shadow: 0 10px 40px rgba(254, 164, 11, 0.1);
}
.profile-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #fea40b, #84641c);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    margin: 0 auto 20px;
    box-shadow: 0 8px 25px rgba(254, 164, 11, 0.3);
}
.profile-name {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 5px;
    background: linear-gradient(135deg, #fff, #a0a0a0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.profile-email {
    text-align: center;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.5);
    margin-bottom: 30px;
}
.sidebar-menu {
    list-style: none;
}
.sidebar-menu li {
    margin-bottom: 10px;
}
.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-size: 15px;
}
.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: rgba(254, 164, 11, 0.1);
    color: #fea40b;
    transform: translateX(5px);
}
.sidebar-menu i {
    font-size: 18px;
    width: 20px;
}
.logout-btn {
    width: 100%;
    padding: 15px;
    background: rgba(255, 68, 68, 0.1);
    border: 1px solid rgba(255, 68, 68, 0.3);
    color: #ff4444;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 20px;
    font-size: 15px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.logout-btn:hover {
    background: rgba(255, 68, 68, 0.2);
    border-color: #ff4444;
}
.profile-content {
    display: none;
}
.profile-content.active {
    display: block;
}
.content-header {
    margin-bottom: 30px;
}
.content-header h2 {
    font-size: 32px;
    font-weight: 300;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #fff, #a0a0a0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.content-header p {
    color: rgba(255, 255, 255, 0.5);
    font-size: 14px;
}
.info-card {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    border: 1px solid rgba(254, 164, 11, 0.2);
    box-shadow: 0 10px 40px rgba(254, 164, 11, 0.1);
}
.info-card h3 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #fea40b;
    display: flex;
    align-items: center;
    gap: 10px;
}
.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.info-item:last-child {
    border-bottom: none;
}
.info-label {
    color: rgba(255, 255, 255, 0.5);
    font-size: 14px;
}
.info-value {
    color: #fff;
    font-size: 15px;
    font-weight: 500;
}
.edit-btn {
    padding: 8px 20px;
    background: linear-gradient(135deg, #fea40b, #84641c);
    border: none;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    font-size: 13px;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}
.edit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(254, 164, 11, 0.3);
}
.edit-form {
    display: none;
}
.edit-form.active {
    display: block;
}
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.form-group input {
    width: 100%;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(254, 164, 11, 0.2);
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    transition: all 0.3s ease;
}
.form-group input:focus {
    outline: none;
    border-color: #fea40b;
    background: rgba(254, 164, 11, 0.05);
    box-shadow: 0 0 0 1px #fea40b;
}
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}
.btn-save,
.btn-cancel {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-save {
    background: linear-gradient(135deg, #fea40b, #84641c);
    color: #fff;
}
.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(254, 164, 11, 0.3);
}
.btn-cancel {
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.1);
}
.orders-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.order-card {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    border-radius: 15px;
    padding: 25px;
    border: 1px solid rgba(254, 164, 11, 0.2);
    transition: all 0.3s ease;
}
.order-card:hover {
    border-color: #fea40b;
    box-shadow: 0 10px 40px rgba(254, 164, 11, 0.15);
}
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.order-id {
    font-size: 16px;
    font-weight: 600;
    color: #fea40b;
}
.order-status {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    letter-spacing: 1px;
    font-weight: 500;
}
.status-completed {
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border: 1px solid rgba(76, 175, 80, 0.3);
}
.status-processing {
    background: rgba(254, 164, 11, 0.2);
    color: #fea40b;
    border: 1px solid rgba(254, 164, 11, 0.3);
}
.status-pending {
    background: rgba(255, 193, 7, 0.2);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}
.order-details {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}
.order-detail {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.order-detail-label {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.5);
}
.order-detail-value {
    font-size: 14px;
    font-weight: 500;
    color: #fff;
}
.order-items {
    background: rgba(0, 0, 0, 0.3);
    padding: 15px;
    border-radius: 10px;
}
.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
}
.order-item:not(:last-child) {
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.item-name {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
}
.item-quantity {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.5);
}
.item-price {
    font-size: 15px;
    font-weight: 600;
    color: #fea40b;
}
@media (max-width: 968px) {
    .profile-container { grid-template-columns: 1fr; }
    .nav-menu { display: none; }
    .order-details { grid-template-columns: 1fr; }
}
@media (max-width: 576px) {
    .profile-sidebar { padding: 20px; }
    .info-card { padding: 20px; }
    .order-card { padding: 15px; }
    .content-section { display: none; }
    .content-section.active { display: block; }
}
</style>
</head>
<body>
<nav class="navbar">
    <div class="container">
        <div class="nav-wrapper">
            <span class="logo-text">Techify</span>
            <ul class="nav-menu">
                <li><a href="../index/index.php">HOME</a></li>
                <li><a href="../index.php#shop">SHOP</a></li>
                <li><a href="../index.php#hotdiscounts">HOT DISCOUNTS</a></li>
                <li><a href="../index.php#topproducts">TOP PRODUCTS</a></li>
                <li><a href="../index.php#features">FEATURES</a></li>
                <li><a href="../index.php#contact">CONTACT US</a></li>
            </ul>
            <div class="nav-icons">
                <button class="icon-btn"><i class="fas fa-search"></i></button>
                <button class="icon-btn"><i class="fas fa-user"></i></button>
                <button class="icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </button>
            </div>
        </div>
    </div>
</nav>

<section class="profile-section">
    <div class="profile-container">
        <aside class="profile-sidebar">
            <div class="profile-avatar">👤</div>
            <h3 class="profile-name"><?= htmlspecialchars($user['name']) ?></h3>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
            <ul class="sidebar-menu">
                <li><a href="#" class="menu-link active" data-target="info"><i class="fas fa-user"></i> Personal Information</a></li>
                <li><a href="#" class="menu-link" data-target="orders"><i class="fas fa-box"></i> My Orders</a></li>
                <li><a href="#" class="menu-link" data-target="password"><i class="fas fa-lock"></i> Change Password</a></li>
            </ul>
            <button class="logout-btn" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </aside>

        <main>
            <div class="info-card" id="infoView">
                <h3>Profile Details</h3>
                <div class="info-item">
                    <span class="info-label">Full Name</span>
                    <span class="info-value"><?= htmlspecialchars($user['name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?= htmlspecialchars($user['phone_number']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value"><?= htmlspecialchars($user['date_of_birth']) ?></span>
                </div>
                <div class="info-item">
                    <button class="edit-btn" onclick="toggleEdit()">Edit Profile</button>
                </div>
            </div>

            <div class="info-card edit-form" id="infoEdit">
                <h3>Edit Profile</h3>
                <form action="profile1.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth']) ?>">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Save Changes</button>
                        <button type="button" class="btn-cancel" onclick="toggleEdit()">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="profile-content" id="orders">
                <div class="content-header">
                    <h2>My Orders</h2>
                    <p>View and track your order history</p>
                </div>
                <div class="orders-list">
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <span class="order-id">#<?= htmlspecialchars($order['order_number']) ?></span>
                                    <span class="order-status status-<?= strtolower($order['status']) ?>">
                                        <?= strtoupper($order['status']) ?>
                                    </span>
                                </div>
                                <div class="order-details">
                                    <div class="order-detail">
                                        <span class="order-detail-label">Order Date</span>
                                        <span class="order-detail-value">
                                            <?= date("M d, Y", strtotime($order['created_at'])) ?>
                                        </span>
                                    </div>
                                    <div class="order-detail">
                                        <span class="order-detail-label">Total Amount</span>
                                        <span class="order-detail-value">
                                            £<?= number_format($order['total_amount'], 2) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-items">
                                    <?php
                                    $items_sql = "SELECT * FROM order_items WHERE order_id = ?";
                                    $items_stmt = $conn->prepare($items_sql);
                                    $items_stmt->bind_param("i", $order['id']);
                                    $items_stmt->execute();
                                    $items = $items_stmt->get_result();
                                    while ($item = $items->fetch_assoc()): ?>
                                        <div class="order-item">
                                            <div>
                                                <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                                <div class="item-quantity">Quantity: <?= $item['quantity'] ?></div>
                                            </div>
                                            <div class="item-price">£<?= number_format($item['price'], 2) ?></div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:#aaa;">No orders found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-content" id="password">
                <div class="info-card">
                    <h3>Change Password</h3>
                    <form action="profile1.php" method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-save">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</section>

<script>
document.querySelectorAll('.menu-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.profile-content').forEach(c => c.classList.remove('active'));
        document.getElementById(this.dataset.target).classList.add('active');
    });
});
function toggleEdit() {
    document.getElementById('infoView').classList.toggle('active');
    document.getElementById('infoEdit').classList.toggle('active');
}
function logout() {
    if(confirm('Logout?')) {
        window.location.href = '../login.php';
    }
}
</script>
</body>
</html>