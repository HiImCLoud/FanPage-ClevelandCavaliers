<?php
include 'shared/config.php';
require 'shared/auth_check.php';
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
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h1 class="text-start m-0">Admin</h1>

            <a href="register.php" class="btn btn-primary btn-md">
                <i class="bi bi-plus-circle me-1"></i> Register
            </a>

        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard.php" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active text-tomato" aria-current="page">Admin List</li>
            </ol>
        </nav>

        <div class="table-responsive">
            <table id="admin-table" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT Username FROM Admin";
                    $result = mysqli_query($conn, $query);
                    $count = 1;

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $count++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['Username']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2' class='text-center'>No admin data found</td></tr>";
                    }
                    ?>
                </tbody>




            </table>
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