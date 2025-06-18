<!-- Sidebar -->
<div class="sidebar">
    <div class="logo d-flex align-items-center justify-content-center">
        <img src="img/shortcut-icon.png?v=<?= time(); ?>" alt="Logo" style="height: 30px; margin-right: 10px;">
        <span class="text-white">Cavs</span>
    </div>

    <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
    <a href="home-info.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'home-info.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door-fill me-2"></i> Home Info
    </a>
    <a href="admin.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : '' ?>">
        <i class="bi bi-people-fill me-2"></i> Admin List
    </a>
    <a href="javascript:void(0);" class="nav-link text-danger" onclick="confirmLogout()">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
</div>