<?php
include('../db_config.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get main form data
        $customer_code = $_POST['customer_code'];
        $sales_order_no = $_POST['sales_order_no'];
        $date_from = $_POST['date_from'];
        $date_to = $_POST['date_to'];

        // Convert 'Date From' and 'Date To' to Oracle format
        $date_from = date('d-M-Y', strtotime(str_replace(" - ", "-", $date_from)));
        $date_to = date('d-M-Y', strtotime(str_replace(" - ", "-", $date_to)));

        // Prepare SQL query
        $sql = "INSERT INTO Provisional_Truck (customer_code, sales_order_no, date_from, date_to, schedule_date, truck_no) 
                VALUES (:customer_code, :sales_order_no, TO_DATE(:date_from, 'DD-MON-YYYY'), TO_DATE(:date_to, 'DD-MON-YYYY'), TO_DATE(:schedule_date, 'DD-MON-YYYY'), :truck_no)";

        $stmt = oci_parse($conn, $sql);

        // Loop through truck schedule details
        for ($i = 0; $i < 7; $i++) {
            if (!empty($_POST["day_$i"]) && !empty($_POST["trucks_$i"])) {
                $schedule_date = $_POST["day_$i"];
                $truck_no = $_POST["trucks_$i"];

                // Convert 'March 1, 2025' to '01-MAR-2025'
                $schedule_date = date('d-M-Y', strtotime($schedule_date));

                // Bind values
                oci_bind_by_name($stmt, ':customer_code', $customer_code);
                oci_bind_by_name($stmt, ':sales_order_no', $sales_order_no);
                oci_bind_by_name($stmt, ':date_from', $date_from);
                oci_bind_by_name($stmt, ':date_to', $date_to);
                oci_bind_by_name($stmt, ':schedule_date', $schedule_date);
                oci_bind_by_name($stmt, ':truck_no', $truck_no);

                // Execute query
                $result = oci_execute($stmt);

                if (!$result) {
                    $error = oci_error($stmt);
                    throw new Exception("Database Error: " . $error['message']);
                }
            }
        }

        // Close statement
        oci_free_statement($stmt);
        oci_close($conn);

        // Success alert
        echo "<script>
                alert('Data saved successfully!');
                window.location.href='../your_redirect_page.php';
              </script>";

    } catch (Exception $e) {
        // Error alert
        echo "<script>
                alert('Error: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
} else {
    // If accessed directly
    echo "<script>
            alert('Invalid request!');
            window.history.back();
          </script>";
}
?>
