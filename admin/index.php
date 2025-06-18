<?php
include 'shared/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM Admin WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('User does not exist.');</script>";
    } else {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["Password"])) {
            $_SESSION["admin_id"] = $user["Admin_ID"];
            $_SESSION["admin_username"] = $user["Username"];

            echo "<script>
                alert('Login successful!');
                window.location.href = 'dashboard.php'; 
            </script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password.');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moviebase</title>
    <link rel="shortcut icon" href="img/shortcut-icon.png?v=<?php echo time(); ?>" type="image/x-icon">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css?v=<?php echo time(); ?>">
</head>

<body>

    <div class="login-card">
        <h3 class="text-center">Admin Login</h3>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="Enter your username" required>
            </div>


            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Enter password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePasswordBtn">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-login mt-3">Login</button>
        </form>
    </div>

</body>
<?php include 'shared/js.php'; ?>

</html>