<?php
require __DIR__ . '/../vendor/autoload.php'; // Load PhpSpreadsheet
include('../db_config.php'); // Ensure this correctly sets $conn

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['submit'])) {
    $file = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $destination = "uploads/" . $fileName;

    // Move uploaded file
    if (!move_uploaded_file($file, $destination)) {
        die("<script>alert('Failed to upload file.'); window.location.href='your_page.php';</script>");
    }

    // Load Excel File
    $spreadsheet = IOFactory::load($destination);
    $worksheet = $spreadsheet->getActiveSheet();
    $data = $worksheet->toArray();

    // Extract header row (first row)
    $headers = $data[0]; 
    unset($data[0]); // Remove header row from processing

    foreach ($data as $row) {
        $area_name = $row[0] ?? null;
        $sector = $row[1] ?? null;
        $mine_code = $row[2] ?? null;
        $mine_name = $row[3] ?? null;
        $grade = $row[4] ?? null;
        $size_c = $row[5] ?? null; 
        $scheme = $row[6] ?? null;
        $customer_code = !empty($row[7]) ? trim($row[7]) : null;
        $customer_name = $row[8] ?? null;
        $sales_order_no = !empty($row[9]) ? trim($row[9]) : null;
        $valid_from = !empty($row[10]) ? date('d-M-Y', strtotime($row[10])) : null;
        $valid_to = !empty($row[11]) ? date('d-M-Y', strtotime($row[11])) : null;
        $so_qty = $row[12] ?? 0;
        $lifted_qty = $row[13] ?? 0;
        $balance_qty = $row[14] ?? 0;

        // Skip rows where CUSTOMER_CODE or SALES_ORDER_NO is missing
        if (empty($customer_code) || empty($sales_order_no)) {
            continue;
        }

        // Insert data into L_P table
        $sql = "MERGE INTO L_P USING dual ON (CUSTOMER_CODE = :customer_code AND SALES_ORDER_NO = :sales_order_no)
                WHEN NOT MATCHED THEN INSERT 
                (AREA_NAME, SECTOR, MINE_CODE, MINE_NAME, GRADE, SIZE_C, SCHEME, CUSTOMER_CODE, CUSTOMER_NAME, 
                SALES_ORDER_NO, VALID_FROM, VALID_TO, SO_QTY, LIFTED_QTY, BALANCE_QTY)
                VALUES (:area_name, :sector, :mine_code, :mine_name, :grade, :size_c, :scheme, :customer_code, 
                :customer_name, :sales_order_no, TO_DATE(:valid_from, 'DD-MON-YYYY'), TO_DATE(:valid_to, 'DD-MON-YYYY'), 
                :so_qty, :lifted_qty, :balance_qty)";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":customer_code", $customer_code);
        oci_bind_by_name($stmt, ":sales_order_no", $sales_order_no);
        oci_bind_by_name($stmt, ":area_name", $area_name);
        oci_bind_by_name($stmt, ":sector", $sector);
        oci_bind_by_name($stmt, ":mine_code", $mine_code);
        oci_bind_by_name($stmt, ":mine_name", $mine_name);
        oci_bind_by_name($stmt, ":grade", $grade);
        oci_bind_by_name($stmt, ":size_c", $size_c);
        oci_bind_by_name($stmt, ":scheme", $scheme);
        oci_bind_by_name($stmt, ":customer_name", $customer_name);
        oci_bind_by_name($stmt, ":valid_from", $valid_from);
        oci_bind_by_name($stmt, ":valid_to", $valid_to);
        oci_bind_by_name($stmt, ":so_qty", $so_qty);
        oci_bind_by_name($stmt, ":lifted_qty", $lifted_qty);
        oci_bind_by_name($stmt, ":balance_qty", $balance_qty);
        oci_execute($stmt);

        // Process Truck Usage Table for multiple dates
        for ($i = 15; $i < count($row); $i++) {
            $truck_date_raw = $headers[$i] ?? null;
            $truck_count = $row[$i] ?? 0;

            if (!empty($truck_date_raw) && !empty($truck_count)) {
                $truck_date = date('d-M-Y', strtotime($truck_date_raw));

                $sql = "MERGE INTO Truck_Usage USING dual 
                ON (CUSTOMER_CODE = :customer_code AND SALES_ORDER_NO = :sales_order_no AND TRUCK_DATE = TO_DATE(:truck_date, 'DD-MON-YYYY'))
                WHEN NOT MATCHED THEN INSERT (CUSTOMER_CODE, SALES_ORDER_NO, TRUCK_DATE, TOTAL_TRUCK)
                VALUES (:customer_code, :sales_order_no, TO_DATE(:truck_date, 'DD-MON-YYYY'), :truck_count)";

                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ":customer_code", $customer_code);
                oci_bind_by_name($stmt, ":sales_order_no", $sales_order_no);
                oci_bind_by_name($stmt, ":truck_date", $truck_date);
                oci_bind_by_name($stmt, ":truck_count", $truck_count);
                oci_execute($stmt);
            }
        }
    }

    oci_close($conn);

    // Success message and redirect
    echo "<script>alert('Data uploaded and processed successfully!'); window.location.href='../employee.php';</script>";
    exit();
}
?>
