<?php
// Database connection file
include('../db_config.php'); // Assuming this file contains the $conn connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fetching form data
$truck_no = $_POST['truckNo'] ?? null;
$brand = $_POST['brand'] ?? null;
$model = $_POST['model'] ?? null;
$make = $_POST['make'] ?? null;
$chasis_no = $_POST['chasisNo'] ?? null;
$transporter_name = $_POST['transporterName'] ?? null;
$reason = $_POST['reason'] ?? null;
$other_reason = $_POST['otherReason'] ?? null;
$start_date = $_POST['startDate'] ?? null;
$end_date = $_POST['endDate'] ?? null;

// If the reason is 'Other', use the value from the textarea
if ($reason === 'Other' && !empty($other_reason)) {
    $reason = $other_reason;
}

// Check if all required fields are provided
if (
    !$truck_no || !$brand || !$model || !$make || !$chasis_no ||
    !$transporter_name || !$reason || !$start_date || !$end_date
) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

try {
    // SQL query to insert data into the blacklisted_trucks table
    $sql = "INSERT INTO B_TRUCKS (
                truck_no, brand, model, make, chasis_no, transporter_name, reason, 
                other_reason, start_date, end_date
            ) VALUES (
                :truck_no, :brand, :model, :make, :chasis_no, :transporter_name, 
                :reason, :other_reason, TO_DATE(:start_date, 'YYYY-MM-DD'), 
                TO_DATE(:end_date, 'YYYY-MM-DD')
            )";

    // Prepare the statement
    $stmt = oci_parse($conn, $sql);
    if (!$stmt) {
        $e = oci_error($conn);
        echo "<script>alert('SQL Parsing Error: " . addslashes($e['message']) . "'); window.history.back();</script>";
        exit();
    }

    // Bind parameters
    oci_bind_by_name($stmt, ":truck_no", $truck_no);
    oci_bind_by_name($stmt, ":brand", $brand);
    oci_bind_by_name($stmt, ":model", $model);
    oci_bind_by_name($stmt, ":make", $make);
    oci_bind_by_name($stmt, ":chasis_no", $chasis_no);
    oci_bind_by_name($stmt, ":transporter_name", $transporter_name);
    oci_bind_by_name($stmt, ":reason", $reason);
    oci_bind_by_name($stmt, ":other_reason", $other_reason);
    oci_bind_by_name($stmt, ":start_date", $start_date);
    oci_bind_by_name($stmt, ":end_date", $end_date);

    // Execute the statement
    if (oci_execute($stmt)) {
        echo "<script>alert('Record successfully added!'); window.location.href='../employee.php';</script>";
    } else {
        $e = oci_error($stmt);
        echo "<script>alert('Execution Error: " . addslashes($e['message']) . "'); window.history.back();</script>";
    }

    // Free the statement resource
    oci_free_statement($stmt);
} catch (Exception $e) {
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
}

// Close the connection
oci_close($conn);
?>
