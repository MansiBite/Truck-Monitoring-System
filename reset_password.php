<?php
// Start the session
session_start();

// Include database connection
require_once 'db_config.php'; // Ensure this file contains the correct Oracle connection

// Define variables and initialize with empty values
$username = $new_password = $confirm_password = "";
$error = $success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = trim($_POST['username']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Prepare SQL query to check if the user exists
        $query = "SELECT * FROM login WHERE username = :username";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":username", $username);
        oci_execute($stmt);

        // Check if the user exists
        if ($row = oci_fetch_assoc($stmt)) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE login SET password = :password WHERE username = :username";
            $update_stmt = oci_parse($conn, $update_query);
            oci_bind_by_name($update_stmt, ":password", $hashed_password);
            oci_bind_by_name($update_stmt, ":username", $username);

            if (oci_execute($update_stmt)) {
                $success = "Password reset successful! You can now log in.";
            } else {
                $error = "Error updating password. Please try again.";
            }
            oci_free_statement($update_stmt);
        } else {
            $error = "No user found with the provided username.";
        }
        oci_free_statement($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Link to Font Awesome for the eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            color: #555;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #2c3e50;
            outline: none;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: #34495e;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .error-message {
            color: #e74c3c;
        }

        .success-message {
            color: #2ecc71;
        }

        /* Eye Icon */
        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ccc;
            padding-top: 22px;
        }

        .form-group {
            position: relative;
        }

        /* Adjust icon positioning for both password fields */
        .form-group input[type="password"] {
            padding-right: 14px; /* Give space for the icon */
        }

    </style>
</head>
<body>
    <div class="container">
        <h3>Reset Password</h3>
        <form method="POST" action="reset_password.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter a new password" required>
                <!-- Eye Icon -->
                <i class="fa-sharp fa-solid fa-eye eye-icon" id="togglePassword" onclick="togglePasswordVisibility()"></i>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                <!-- Eye Icon -->
                <i class="fa-sharp fa-solid fa-eye eye-icon" id="toggleConfirmPassword" onclick="toggleConfirmPasswordVisibility()"></i>
            </div>
            <?php if ($error): ?>
                <div class="message error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="message success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <div class="form-group">
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </div>

    <!-- JavaScript to toggle password visibility -->
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("new_password");
            var icon = document.getElementById("togglePassword");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function toggleConfirmPasswordVisibility() {
            var confirmPasswordField = document.getElementById("confirm_password");
            var icon = document.getElementById("toggleConfirmPassword");
            if (confirmPasswordField.type === "password") {
                confirmPasswordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                confirmPasswordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
