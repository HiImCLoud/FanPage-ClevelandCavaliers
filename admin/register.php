<?php
include 'shared/config.php';
require 'shared/auth_check.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["user"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM Admin WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Username already taken.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insert = $conn->prepare("INSERT INTO Admin (Username, Password) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $hashedPassword);

            if ($insert->execute()) {
                echo "<script>
                    alert('Registration successful! Redirecting to login page...');
                    window.location.href = 'index.php';
                </script>";
                exit();
            } else {
                $message = "Something went wrong. Try again.";
            }
        }
    }

    if ($message !== "") {
        echo "<script>alert('" . addslashes($message) . "');</script>";
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
        <h3 class="text-center">Register</h3>
        <form action="" method="POST" onsubmit="return validateForm();">
            <div class="mb-3">
                <label for="user" class="form-label">Username</label>
                <input type="text" name="user" class="form-control" id="user" placeholder="Enter your username" required>
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

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" class="form-control" id="confirmPasswordInput" placeholder="Confirm password" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPasswordBtn">
                        <i class="bi bi-eye-slash" id="toggleConfirmIcon"></i>
                    </button>
                </div>
            </div>
            <p class="text-center">Already have an account? <a href="index.php">Login here</a>.</p>


            <button type="submit" class="btn btn-login mt-3">Register</button>
        </form>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirm = document.getElementById("confirm_password").value;

            if (password.length < 6) {
                alert("Password must be at least 6 characters long.");
                return false;
            }

            if (password !== confirm) {
                alert("Passwords do not match. Please try again.");
                return false;
            }

            return true;
        }
    </script>

    <?php include 'shared/js.php'; ?>

</body>

</html>