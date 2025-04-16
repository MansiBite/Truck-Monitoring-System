<?php
// Database connection file
include('../db_config.php');

// Initialize variables
$name = $aadhar_no = $license_no = $reason = $start_date = $end_date = "";
$other_reason = "";

// If the form is submitted for updating the driver data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_driver'])) {
    // Fetch form data for update
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
        echo "All fields are required.";
        exit();
    }

    try {
        // SQL query to update the blacklisted_driver table
        $sql = "UPDATE blacklisted_drivers SET name = :name, license_no = :license_no, reason = :reason, 
                start_date = TO_DATE(:start_date, 'YYYY-MM-DD'), end_date = TO_DATE(:end_date, 'YYYY-MM-DD') 
                WHERE aadhar_no = :aadhar_no";

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
            echo "Record successfully updated!";
        } else {
            $e = oci_error($stmt); // Get detailed error
            echo "Error: " . $e['message'];
        }

        // Free the statement
        oci_free_statement($stmt);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    oci_close($conn);
}

// Fetch the driver data from the database based on the Aadhar number
if (isset($_POST['search_aadhar'])) {
    $search_aadhar = $_POST['search_aadhar'];

    // Query to get the data of the driver with the given Aadhar
    $sql = "SELECT name, aadhar_no, license_no, reason, TO_CHAR(start_date, 'YYYY-MM-DD') AS start_date, 
            TO_CHAR(end_date, 'YYYY-MM-DD') AS end_date 
            FROM blacklisted_drivers WHERE aadhar_no = :aadhar_no";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":aadhar_no", $search_aadhar);
    oci_execute($stmt);

    $driver = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if ($driver) {
        // Populate form fields with the fetched data
        $name = $driver['NAME'];
        $aadhar_no = $driver['AADHAR_NO'];
        $license_no = $driver['LICENSE_NO'];
        $reason = $driver['REASON'];
        $start_date = $driver['START_DATE'];
        $end_date = $driver['END_DATE'];
    } else {
        echo "Driver not found!";
        exit;
    }
}
?>