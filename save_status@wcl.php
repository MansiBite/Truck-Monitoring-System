<?php
include('../db_config.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['truck_no'] as $key => $truck_no) {
        $sales_order_no = $_POST['sales_order_no'][$key];
        $date_in = $_POST['date_in'][$key];
        $time_in = $_POST['time_in'][$key] . " " . $_POST['am_pm_in'][$key]; // e.g., "08:30 PM"
        $date_out = $_POST['date_out'][$key] ?? null;
        $time_out = !empty($_POST['time_out'][$key]) ? $_POST['time_out'][$key] . " " . $_POST['am_pm_out'][$key] : null;

        // Convert Date Format for Oracle
        $date_in = !empty($date_in) ? date("d-M-Y", strtotime($date_in)) : null;
        $date_out = !empty($date_out) ? date("d-M-Y", strtotime($date_out)) : null;

        // Convert Time to 24-hour format **(Fixed Storage Issue)**
        $time_in = date("H:i:s", strtotime($time_in));
        $time_out = !empty($time_out) ? date("H:i:s", strtotime($time_out)) : null;

        // **CHECK IF TRUCK NUMBER ALREADY EXISTS**
        $check_sql = "SELECT COUNT(*) AS COUNT FROM WCL_Check_Post WHERE truck_no = :truck_no";
        $check_stmt = oci_parse($conn, $check_sql);
        oci_bind_by_name($check_stmt, ':truck_no', $truck_no);
        oci_execute($check_stmt);
        $row = oci_fetch_assoc($check_stmt);
        
        if ($row['COUNT'] > 0) {
            echo "<script>
                alert('Truck No. $truck_no already exists. Skipping entry.');
            </script>";
            continue; // Skip inserting duplicate entry
        }

        // **Fixed SQL Query for Time Storage**
        $sql = "INSERT INTO WCL_Check_Post (truck_no, sales_order_no, date_in, time_in, date_out, time_out)
                VALUES (:truck_no, :sales_order_no, TO_DATE(:date_in, 'DD-MON-YYYY'), :time_in, 
                        TO_DATE(:date_out, 'DD-MON-YYYY'), :time_out)";
        
        $stmt = oci_parse($conn, $sql);

        // Bind Parameters
        oci_bind_by_name($stmt, ':truck_no', $truck_no);
        oci_bind_by_name($stmt, ':sales_order_no', $sales_order_no);
        oci_bind_by_name($stmt, ':date_in', $date_in);
        oci_bind_by_name($stmt, ':time_in', $time_in); // Now storing as VARCHAR2 (String)
        oci_bind_by_name($stmt, ':date_out', $date_out);
        oci_bind_by_name($stmt, ':time_out', $time_out); // Now storing as VARCHAR2 (String)

        // Execute Query
        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            die("Error inserting data: " . $e['message']);
        }
    }
    
    echo  "<script>
        alert('Data saved successfully!');
        window.location.href='../your_redirect_page.php';
    </script>";
}

oci_close($conn);
?>
