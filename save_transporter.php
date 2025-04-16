<?php
// Include the database connection file
include('../db_config.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $aadhar_no = $_POST['aadhar_no'];
    $license_no = $_POST['license_no'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password

    // Check if AADHAR No. already exists
    $check_aadhar = "SELECT COUNT(*) FROM transporter WHERE aadhar_no = :aadhar_no";
    $stmt_check_aadhar = oci_parse($conn, $check_aadhar);
    oci_bind_by_name($stmt_check_aadhar, ":aadhar_no", $aadhar_no);
    oci_execute($stmt_check_aadhar);

    $aadhar_count = oci_fetch_assoc($stmt_check_aadhar)['COUNT(*)'];
    
    // Check if License No. already exists
    $check_license = "SELECT COUNT(*) FROM transporter WHERE license_no = :license_no";
    $stmt_check_license = oci_parse($conn, $check_license);
    oci_bind_by_name($stmt_check_license, ":license_no", $license_no);
    oci_execute($stmt_check_license);

    $license_count = oci_fetch_assoc($stmt_check_license)['COUNT(*)'];

    // If either aadhar_no or license_no already exists, show an error
    if ($aadhar_count > 0) {
        echo "Error: Aadhar No. already exists!";
    } elseif ($license_count > 0) {
        echo "Error: License No. already exists!";
    } else {
        // Insert data into the transporter table if no duplicates
        $sql_insert = "
        INSERT INTO TRANSPORTER (name, aadhar_no, license_no, password)
        VALUES (:name, :aadhar_no, :license_no, :password)
        ";

        // Prepare the statement
        $stmt = oci_parse($conn, $sql_insert);

        // Bind variables
        oci_bind_by_name($stmt, ":name", $name);
        oci_bind_by_name($stmt, ":aadhar_no", $aadhar_no);
        oci_bind_by_name($stmt, ":license_no", $license_no);
        oci_bind_by_name($stmt, ":password", $password);

        // Execute the statement
        if (oci_execute($stmt)) {
            // Success - show message and redirect
            echo "<script>alert('Transporter registered successfully!'); window.location.href = '../customer.php';</script>";
        } else {
            $e = oci_error($stmt); // Get error information
            echo "Error: " . $e['message'];
        }

        // Free the statement
        oci_free_statement($stmt);
    }

    // Free the statements
    oci_free_statement($stmt_check_aadhar);
    oci_free_statement($stmt_check_license);
}

// Close the connection
oci_close($conn);
?>
