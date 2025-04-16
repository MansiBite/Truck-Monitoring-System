<?php
// Assuming you get EMPLOYEE_CATEGORY from a session or database
$category = "WCL" || "CUS" || "TRS"; // Example category; replace this with the actual value from your application

// Determine the heading based on EMPLOYEE_CATEGORY
$dashboard_heading = "";
if ($category === "WCL") {
    $dashboard_heading = "Employee Dashboard";
} elseif ($category === "CUS") {
    $dashboard_heading = "Customer Dashboard";
} elseif ($category === "TRS") {
    $dashboard_heading = "Transporter Dashboard";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with Dynamic Heading</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            color: white;
            padding: 10px 20px;
        }
        .navbar .logo img {
            height: 50px;
            width: 70px;
            margin-left: 60px;
        }
        .navbar .heading {
            font-size: 20px;
            font-weight: bold;
            color: white;
        }
        .navbar .logout-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        .navbar .logout-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <!-- Logo on the left -->
        <div class="logo">
            <a href="index.php">
                <img src="../images/wcl.png" alt="Logo"> <!-- Replace 'wcl.png' with the path to your logo -->
            </a>
        </div>

        <!-- Dynamic Heading in the center -->
        <div class="heading">
            <?php echo $dashboard_heading; ?>
        </div>

        <!-- Logout button on the right -->
        <form action="../logout.php" method="post" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
