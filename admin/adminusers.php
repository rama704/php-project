<?php
session_start();
require_once '../includes/db.connection.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$query = "SELECT id, name, email, role, created_at FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$total_users = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adminproducts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="adminusers.css">
       
</head>
<body>

<!-- ================= SIDEBAR ================= -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="logo-text">Dashboard</span>
            <span class="admin-badge">Admin</span>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admindashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="adminproducts.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            <a href="admincategories.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
            </a>
            <a href="adminorders.php" class="nav-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Orders</span>
            </a>
            <a href="adminusers.php" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Users</span>
                <span class="badge"><?= $total_users ?></span>
            </a>
         <a href="#" onclick="confirmLogout(event)" class="nav-item logout">
    <i class="fas fa-sign-out-alt"></i>
    <span>Logout</span>
</a>
        </nav>
    </aside>

<!-- ================= MAIN CONTENT ================= -->
<main class="main-content" id="mainContent">

    <!-- ================= TOP BAR ================= -->
    <div class="top-bar">
        <button class="toggle-sidebar" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="top-bar-right">
            <div class="admin-profile">
                <span>Welcome, Admin</span>
                <div class="admin-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= CONTAINER ================= -->
    <div class="dashboard-container">

        <!-- ================= PAGE HEADER ================= -->
        <div class="page-header">
            <div>
                <h1>Users Management</h1>
                <p>View and manage all registered users</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <!-- ================= USERS TABLE ================= -->
        <section class="content-section">

            <div class="section-header">
                <h2>Users List</h2>
                <div class="header-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search user...">
                    </div>
                </div>
            </div>

            <?php if ($total_users > 0): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong>#<?= $row['id'] ?></strong></td>

                            <td>
                                <div class="user-name">
                                    <?= htmlspecialchars($row['name']) ?>
                                </div>
                            </td>

                            <td><?= htmlspecialchars($row['email']) ?></td>

                            <td>
                                <span class="category-badge">
                                    <i class="fas fa-user-shield"></i>
                                    <?= ucfirst($row['role']) ?>
                                </span>
                            </td>

                            <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>

                            <td>
                                <div class="action-buttons">
                                    <button onclick="deleteUser(<?= $row['id'] ?>)" class="action-btn delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-content">
                    <i class="fas fa-users-slash"></i>
                    <h3>No Users Found</h3>
                    <p>There are currently no registered users.</p>
                </div>
            </div>
            <?php endif; ?>

        </section>

    </div>
</main>

<!-- ================= JS ================= -->
<script>
/* Sidebar toggle */
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const toggleBtn = document.getElementById('toggleSidebar');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
});

/* Search */
const searchInput = document.getElementById('searchInput');
const tableBody = document.getElementById('usersTableBody');

searchInput.addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();
    const rows = tableBody.getElementsByTagName('tr');

    Array.from(rows).forEach(row => {
        const name = row.children[1].textContent.toLowerCase();
        const email = row.children[2].textContent.toLowerCase();

        row.style.display = (name.includes(filter) || email.includes(filter)) ? '' : 'none';
    });
});

/* Delete */
function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This user will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `adminusersdelete.php?id=${id}`;
        }
    });
}
/* Logout Confirmation */
function confirmLogout(event) {
    event.preventDefault();
    
    Swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../login2.php';
        }
    });
}
</script>

</body>
</html>