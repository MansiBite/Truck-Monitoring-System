<?php
include('db_config.php');
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start session for managing displayed tables
session_start();
if (!isset($_SESSION['tables'])) {
    $_SESSION['tables'] = [];
}

// Get user-selected date range
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

// Get today's date for validation
$today_date = date('Y-m-d');

// Fetch TRUCK_DATE values within the selected range
$date_columns = [];
$sql_dates = "SELECT DISTINCT TRUCK_DATE FROM Truck_Usage";
if ($start_date && $end_date) {
    $sql_dates .= " WHERE TRUCK_DATE BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') AND TO_DATE(:end_date, 'YYYY-MM-DD')";
}
$sql_dates .= " ORDER BY TRUCK_DATE";

$stmt_dates = oci_parse($conn, $sql_dates);
if ($start_date && $end_date) {
    oci_bind_by_name($stmt_dates, ':start_date', $start_date);
    oci_bind_by_name($stmt_dates, ':end_date', $end_date);
}
oci_execute($stmt_dates);

while ($row = oci_fetch_assoc($stmt_dates)) {
    $date_columns[] = $row['TRUCK_DATE'];
}

// Fetch L_P table data
$sql = "SELECT * FROM L_P";

// Fetch Truck Usage Data based on date range
$sql_truck = "SELECT CUSTOMER_CODE, SALES_ORDER_NO, TRUCK_DATE, TOTAL_TRUCK FROM Truck_Usage";
if ($start_date && $end_date) {
    $sql_truck .= " WHERE TRUCK_DATE BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') AND TO_DATE(:end_date, 'YYYY-MM-DD')";
}

$stmt_truck = oci_parse($conn, $sql_truck);
if ($start_date && $end_date) {
    oci_bind_by_name($stmt_truck, ':start_date', $start_date);
    oci_bind_by_name($stmt_truck, ':end_date', $end_date);
}
oci_execute($stmt_truck);

// Store truck data
$truck_data = [];
while ($row = oci_fetch_assoc($stmt_truck)) {
    $truck_data[$row['CUSTOMER_CODE']][$row['SALES_ORDER_NO']][$row['TRUCK_DATE']] = $row['TOTAL_TRUCK'];
}

// Generate unique table ID
$table_id = "table_" . time();
array_push($_SESSION['tables'], $table_id);

// Keep last 3 table views
if (count($_SESSION['tables']) > 3) {
    array_shift($_SESSION['tables']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Program Display</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid black;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            margin: 10px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-container {
            margin: 20px;
        }
    </style>
</head>

<body>

    <h2>Select Date Range to Filter and Send Email</h2>
    <form method="post">
        <label>Start Date:</label>
        <input type="date" name="start_date" required>
        <label>End Date:</label>
        <input type="date" name="end_date" required>
        <button type="submit">Filter Data</button>
    </form>

    <h2><?php echo $start_date && $end_date ? "Filtered Data from $start_date to $end_date" : "All Data"; ?></h2>

    <div id="<?= $table_id ?>" class="data-table">
        <table>
            <tr>
                <th>Area Name</th>
                <th>Sector</th>
                <th>Mine Code</th>
                <th>Mine Name</th>
                <th>Grade</th>
                <th>Size</th>
                <th>Scheme</th>
                <th>Customer Code</th>
                <th>Customer Name</th>
                <th>Sales Order No</th>
                <th>Valid From</th>
                <th>Valid To</th>
                <th>SO Qty</th>
                <th>Lifted Qty</th>
                <th>Balance Qty</th>

                <!-- Display Only Selected Date Range -->
                <?php foreach ($date_columns as $date) { ?>
                    <th><?= $date ?></th>
                <?php } ?>

                <th>Total Trucks</th>
            </tr>

            <?php
            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt);
            while ($row = oci_fetch_assoc($stmt)) {
                $customer_code = $row['CUSTOMER_CODE'];
                $sales_order_no = $row['SALES_ORDER_NO'];

                // Skip rows if date range is selected but no truck data exists for it
                if ($start_date && $end_date) {
                    $has_data = false;
                    foreach ($date_columns as $date) {
                        if (isset($truck_data[$customer_code][$sales_order_no][$date])) {
                            $has_data = true;
                            break;
                        }
                    }
                    if (!$has_data)
                        continue;
                }
                ?>
                <tr>
                    <td><?= $row['AREA_NAME'] ?></td>
                    <td><?= $row['SECTOR'] ?></td>
                    <td><?= $row['MINE_CODE'] ?></td>
                    <td><?= $row['MINE_NAME'] ?></td>
                    <td><?= $row['GRADE'] ?></td>
                    <td><?= $row['SIZE_C'] ?></td>
                    <td><?= $row['SCHEME'] ?></td>
                    <td><?= $row['CUSTOMER_CODE'] ?></td>
                    <td><?= $row['CUSTOMER_NAME'] ?></td>
                    <td><?= $row['SALES_ORDER_NO'] ?></td>
                    <td><?= $row['VALID_FROM'] ?></td>
                    <td><?= $row['VALID_TO'] ?></td>
                    <td><?= $row['SO_QTY'] ?></td>
                    <td><?= $row['LIFTED_QTY'] ?></td>
                    <td><?= $row['BALANCE_QTY'] ?></td>

                    <!-- Display Only Selected Date Range -->
                    <?php foreach ($date_columns as $date) { ?>
                        <td>
                            <?= isset($truck_data[$customer_code][$sales_order_no][$date])
                                ? $truck_data[$customer_code][$sales_order_no][$date]
                                : '-' ?>
                        </td>
                    <?php } ?>

                    <!-- Total Trucks -->
                    <td>
                        <?php
                        $total_trucks = 0;
                        if (isset($truck_data[$customer_code][$sales_order_no])) {
                            foreach ($truck_data[$customer_code][$sales_order_no] as $truck_count) {
                                $total_trucks += $truck_count;
                            }
                        }
                        echo $total_trucks;
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <!-- Send Email Button (Only Show if Start Date is Today or in the Future) -->
        <?php if ($start_date && $start_date >= $today_date) { ?>
            <div class="btn-container">
                <form action="email/send_data.php" method="post">
                    <input type="hidden" name="start_date" value="<?= $start_date ?>">
                    <input type="hidden" name="end_date" value="<?= $end_date ?>">
                    <button type="submit">Send Email</button>
                </form>
            </div>
        <?php } ?>

        <!-- Go Back Button -->
        <button onclick="history.back()">Go Back</button>

    </div>

</body>

</html>