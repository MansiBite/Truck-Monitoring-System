<?php
// Database connection file
include('../db_config.php'); // Assuming the connection script is saved as db_connection.php

// Fetching form data
$name = $_POST['name'] ?? null;
$aadhar_no = $_POST['aadhar_no'] ?? null;
$license_no = $_POST['license_no'] ?? null;
$reason = $_POST['reason'] ?? null;
$other_reason = $_POST['other_reason'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

// If the reason is 'Other', use the value from the textarea
if ($reason === 'Other' && !empty($other_reason)) {
    $reason = $other_reason;
}

// Check if all required fields are provided
if (!$name || !$aadhar_no || !$license_no || !$reason || !$start_date || !$end_date) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

try {
    // SQL query to insert data into the blacklisted_driver table
    $sql = "INSERT INTO B_D (name, aadhar_no, license_no, reason, start_date, end_date)
            VALUES (:name, :aadhar_no, :license_no, :reason, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'))";

    // Prepare the statement
    $stmt = oci_parse($conn, $sql);

    // Bind parameters
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":aadhar_no", $aadhar_no);
    oci_bind_by_name($stmt, ":license_no", $license_no);
    oci_bind_by_name($stmt, ":reason", $reason);
    oci_bind_by_name($stmt, ":start_date", $start_date);
    oci_bind_by_name($stmt, ":end_date", $end_date);

    // Execute the query
    if (oci_execute($stmt)) {
        echo "<script>alert('Record successfully added!'); window.location.href='../employee.php';</script>";
    } else {
        $e = oci_error($stmt); // Get detailed error
        echo "<script>alert('Error: " . addslashes($e['message']) . "'); window.history.back();</script>";
    }

    // Free the statement
    oci_free_statement($stmt);
} catch (Exception $e) {
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
}

// Close the connection
oci_close($conn);
?>
