<?php
include 'shared/config.php';
require 'shared/auth_check.php';

$adminsQuery = "SELECT COUNT(*) AS total_admins FROM Admin";
$adminsResult = mysqli_query($conn, $adminsQuery);
$adminsCount = mysqli_fetch_assoc($adminsResult)['total_admins'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cavs | Admin Panel</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?php echo time(); ?>" type="image/x-icon">
    <?php include 'shared/css.php'; ?>
</head>

<body>
    <?php include 'shared/sidebar.php'; ?>
    <div class="preloader"></div>
    <div class="content p-5">
        <h1 class="text-start mb-2">Dashboard</h1>
        <p class="text-start text-tomato">Home</p>
        <hr>
        <div class="row g-4">
            <div class="col-12">
                <a href="admin.php" class="card-link text-decoration-none">
                    <div class="card shadow-sm border-0" style="height: 300px;">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                                <i class="bi bi-person-badge fs-2"></i>
                            </div>
                            <h4 class="mb-2">Registered Admins</h4>
                            <h2 class="fw-bold"><?php echo $adminsCount; ?></h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = 'logout.php';
            }
        }
    </script>
    <?php include 'shared/js.php'; ?>
</body>

</html>