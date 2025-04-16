<?php
ob_start();
session_start();

include 'db_config.php'; // Include database configuration

// Function to validate login credentials
function validate_login($username, $password, $captcha_sum) {
    global $conn;

    $username = strtoupper(trim($username));
    $password = trim($password);

    // Captcha validation
    $first = intval($_POST['first']);
    $second = intval($_POST['second']);
    $expected_sum = $first + $second;

    if ($captcha_sum != $expected_sum) {
        return "Captcha incorrect. Please try again.";
    }

    // Prepare and execute SQL query
    $query = "SELECT * FROM LOGIN WHERE USERNAME = :username FETCH FIRST 1 ROWS ONLY";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":username", $username);

    if (!oci_execute($stmt)) {
        $error = oci_error($stmt);
        return "Error executing query: " . $error['message'];
    }

    // Fetch user details
    if ($row = oci_fetch_assoc($stmt)) {
        $id = $row['ID'];
        $role = $row['ROLE'];
        $db_username = $row['USERNAME'];
        $category = $row['CATEGORY'];
        $db_password = $row['PASSWORD'];

        // Verify password
        if ($password === $db_password || password_verify($password, $db_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;
            $_SESSION['category'] = $category;
            $_SESSION['permissions'] = get_permissions($category, $role);

            // Redirect based on category
            switch ($category) {
                case 'WCL':
                    header("Location: employee.php");
                    exit();
                case 'CUS':
                    header("Location: customer.php");
                    exit();
                case 'TRS':
                    header("Location: transporter.php");
                    exit();
                default:
                    return "Invalid category.";
            }
        } else {
            return "Invalid password.";
        }
    } else {
        return "User not found.";
    }
}

// Function to get permissions based on category and role
function get_permissions($category, $role) {
    $permissions = [];

    $wcl_roles = [
        'ASM' => ['loading_program' => ['create', 'edit', 'display'], 'coal_availability' => ['create', 'edit', 'display'], 'truck_requirement_indenting' => ['display'], 'where_is_my_truck' => ['display']],
        'AGM' => ['loading_program' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display']],
        'DIC' => ['loading_program' => ['display'], 'coal_availability' => ['create', 'edit', 'display'], 'truck_requirement_indenting' => ['display'], 'blacklisted_trucks' => ['display'], 'blacklisted_transporter' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display'], 'provisional_truck_no' => ['display'], 'status_wcl_check_post' => ['display'], 'where_is_my_truck' => ['display']],
        'MMC' => ['loading_program' => ['display'], 'coal_availability' => ['create', 'edit', 'display'], 'blacklisted_trucks' => ['display'], 'blacklisted_transporter' => ['display'], 'blacklisted_drivers' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display'], 'where_is_my_truck' => ['display']],
        'MSG' => ['loading_program' => ['display'], 'blacklisted_trucks' => ['display'], 'blacklisted_transporter' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display'], 'provisional_truck_no' => ['display'], 'status_wcl_check_post' => ['display'], 'where_is_my_truck' => ['display']],
        'RSI' => ['loading_program' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display']],
        'SAM' => ['loading_program' => ['display'], 'blacklisted_trucks' => ['display'], 'blacklisted_transporter' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display']],
        'WBC' => ['loading_program' => ['display'], 'blacklisted_trucks' => ['display'], 'blacklisted_transporter' => ['display'], 'blacklisted_driver' => ['display'], 'sales_order' => ['display'], 'lifting_details' => ['display'], 'invoice' => ['display'], 'refund' => ['display'], 'provisional_truck_no' => ['display'], 'status_wcl_check_post' => ['display'], 'where_is_my_truck' => ['display']]
    ];

    if ($category == 'WCL' && isset($wcl_roles[$role])) {
        return $wcl_roles[$role];
    } elseif ($category == 'CUS') {
        return ['loading_program' => ['display'], 'truck_requirement_indenting' => ['create', 'edit', 'display'], 'blacklisted_trucks' => ['display']];
    } elseif ($category == 'TRS') {
        return ['loading_program' => ['display'], 'truck_requirement_indenting' => ['create', 'edit', 'display'], 'blacklisted_trucks' => ['display']];
    }

    return [];
}

// Main execution
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha_sum = $_POST['sum'] ?? '';

    $login_error = validate_login($username, $password, $captcha_sum);

    if ($login_error) {
        echo "<script>alert('$login_error'); window.location.href='login.php';</script>";
        exit();
    }
}
?>
