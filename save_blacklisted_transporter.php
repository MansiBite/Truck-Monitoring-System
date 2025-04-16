<?php
// Database connection file
include('../db_config.php'); // Assuming the connection script is saved as db_config.php

// Fetching form data
$transporter_name = $_POST['transporterName'] ?? null;
$transporter_pan = $_POST['transporterPan'] ?? null;
$blacklist_reason = $_POST['blacklistReason'] ?? null;
$other_reason = $_POST['otherReason'] ?? null;
$start_date = $_POST['fromDate'] ?? null;
$end_date = $_POST['toDate'] ?? null;

// If the reason is 'Other', use the value from the textarea
if ($blacklist_reason === 'Other' && !empty($other_reason)) {
    $blacklist_reason = $other_reason;
}

// If any field is missing, show the error
if (!$transporter_name || !$transporter_pan || !$blacklist_reason || !$start_date || !$end_date) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

try {
    // SQL query to insert data into the blacklisted_transporter table
    $sql = "INSERT INTO B_T (transporter_name, transporter_pan, blacklist_reason, other_reason, start_date, end_date)
            VALUES (:transporter_name, :transporter_pan, :blacklist_reason, :other_reason, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'))";

    // Prepare the statement
    $stmt = oci_parse($conn, $sql);

    // Bind parameters
    oci_bind_by_name($stmt, ":transporter_name", $transporter_name);
    oci_bind_by_name($stmt, ":transporter_pan", $transporter_pan);
    oci_bind_by_name($stmt, ":blacklist_reason", $blacklist_reason);
    oci_bind_by_name($stmt, ":other_reason", $other_reason);
    oci_bind_by_name($stmt, ":start_date", $start_date);
    oci_bind_by_name($stmt, ":end_date", $end_date);

    // Execute the query
    if (oci_execute($stmt)) {
        // Display success message and redirect
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
