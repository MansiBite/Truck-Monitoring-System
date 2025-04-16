<?php
include('../db_config.php'); // Database connection

try {
    // Query to fetch all data from WCL_Check_Post table
    $query = "SELECT * FROM WCL_Check_Post ORDER BY id DESC";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WCL Check Post Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 20px;
        }
        h2 {
            background-color: #0073e6;
            color: white;
            padding: 15px;
            border-radius: 5px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #0073e6;
            color: white;
        }
    </style>
</head>
<body>

    <h2>WCL Check Post Records</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Truck No.</th>
            <th>Sales Order No.</th>
            <th>Date In</th>
            <th>In Time</th>
            <th>Date Out</th>
            <th>Out Time</th>
        </tr>

        <?php
        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['ID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TRUCK_NO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['SALES_ORDER_NO']) . "</td>";
            echo "<td>" . htmlspecialchars($row['DATE_IN']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TIME_IN']) . "</td>";
            echo "<td>" . htmlspecialchars($row['DATE_OUT']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TIME_OUT']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
