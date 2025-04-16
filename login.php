<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="header">
        <img src="images/wcl.png" alt="WCL Logo">
        <div class="subheadings">
            <h2>Western Coalfields Limited</h2>
            <h4>A Miniratna Company</h4>
            <h4>A Subsidiary of Coal India Limited</h4>
        </div>
        <img src="images/Coal-India-Logo.webp" alt="Top Right Icon">
    </div>
    <div class="vms">
        Vehicle Management System
    </div>
    <div class="main">
        <h3>Login</h3>
        <form action="validate_login.php" method="POST" onsubmit="return validate(this);">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="form-group captcha">
                <label>Captcha:</label>
                <span id="first-number"></span>
                <span>+</span>
                <span id="second-number"></span>
                <span>=</span>
                <input type="hidden" name="first" id="first">
                <input type="hidden" name="second" id="second">
                <input type="text" name="sum" id="sum" class="form-control" placeholder="Answer" required>
            </div>
            <div class="error-message" id="error"></div>
            <div class="form-group">
                <input type="submit" value="Login">
                <button type="button" onclick="location.href='reset_password.php';">Reset Password</button>
            </div>
        </form>
    </div>

    <script>
        // Function to generate random captcha
        function generateCaptcha() {
            const firstNumber = Math.floor(Math.random() * 50) + 1; // Random number between 1 and 50
            const secondNumber = Math.floor(Math.random() * 50) + 1; // Random number between 1 and 50

            document.getElementById('first-number').textContent = firstNumber;
            document.getElementById('second-number').textContent = secondNumber;
            document.getElementById('first').value = firstNumber;
            document.getElementById('second').value = secondNumber;
        }

        // Function to validate captcha
        function validateCaptcha() {
            const sum = parseInt(document.getElementById('sum').value);
            const first = parseInt(document.getElementById('first').value);
            const second = parseInt(document.getElementById('second').value);

            if (sum !== (first + second)) {
                document.getElementById('error').innerText = "Captcha incorrect. Please try again.";
                return false;
            }
            return true;
        }

        // Validate form
        function validate(form) {
            if (!validateCaptcha()) {
                return false;
            }
            return true;
        }

        // Generate captcha on page load
        window.onload = generateCaptcha;
    </script>
</body>
</html>
