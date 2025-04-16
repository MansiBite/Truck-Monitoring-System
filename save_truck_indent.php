<?php
include('../db_config.php');

$conn = oci_connect($username, $password, $conn_str);
if (!$conn) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_code = $_POST['customer_code'];
    $sales_order_no = $_POST['sales_order_no'];
    $date_from = date('Y-m-d', strtotime($_POST['date_from']));
    $date_to = date('Y-m-d', strtotime($_POST['date_to']));

    // Loop through each truck date entry
    for ($i = 0; $i < 7; $i++) {
        $truck_date = date('Y-m-d', strtotime($_POST["day_$i"]));
        $no_of_trucks = $_POST["trucks_$i"];

        // Insert data into Oracle table
        $sql = "INSERT INTO TRUCK_INDENTING (CUSTOMER_CODE, SALES_ORDER_NO, DATE_FROM, DATE_TO, TRUCK_DATE, NO_OF_TRUCKS) 
                VALUES (:customer_code, :sales_order_no, TO_DATE(:date_from, 'YYYY-MM-DD'), TO_DATE(:date_to, 'YYYY-MM-DD'), TO_DATE(:truck_date, 'YYYY-MM-DD'), :no_of_trucks)";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":customer_code", $customer_code);
        oci_bind_by_name($stmt, ":sales_order_no", $sales_order_no);
        oci_bind_by_name($stmt, ":date_from", $date_from);
        oci_bind_by_name($stmt, ":date_to", $date_to);
        oci_bind_by_name($stmt, ":truck_date", $truck_date);
        oci_bind_by_name($stmt, ":no_of_trucks", $no_of_trucks);

        $result = oci_execute($stmt);
        if (!$result) {
            $e = oci_error($stmt);
            echo "Error: " . htmlentities($e['message'], ENT_QUOTES);
            exit();
        }
        oci_free_statement($stmt);
    }

    echo "<script>alert('Data saved successfully!'); window.location.href='../customer.php';</script>";
}

// Close connection
oci_close($conn);
?>
