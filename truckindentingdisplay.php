<?php
include('../navbar.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Truck Indenting Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }
    </style>
</head>

<body>
    <h2>Truck Indenting Records</h2>
    <table>
        <tr>
            <!-- <th>ID</th> -->
            <th>Customer Code</th>
            <th>Sales Order No</th>
            <th>Date From</th>
            <th>Date To</th>
            <th>Truck Date</th>
            <th>No. of Trucks</th>
        </tr>
        <?php
        // Database connection file
        include('../db_config.php');

        try {
            $sql = "SELECT INDENT_ID, CUSTOMER_CODE, SALES_ORDER_NO, TO_CHAR(DATE_FROM, 'YYYY-MM-DD') AS DATE_FROM, 
                           TO_CHAR(DATE_TO, 'YYYY-MM-DD') AS DATE_TO, TO_CHAR(TRUCK_DATE, 'YYYY-MM-DD') AS TRUCK_DATE, NO_OF_TRUCKS 
                    FROM TRUCK_INDENTING ORDER BY INDENT_ID DESC";

            $stmt = oci_parse($conn, $sql);
            oci_execute($stmt);

            while ($row = oci_fetch_assoc($stmt)) {
                echo "<tr>";
                // echo "<td>" . htmlspecialchars($row['INDENT_ID']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CUSTOMER_CODE']) . "</td>";
                echo "<td>" . htmlspecialchars($row['SALES_ORDER_NO']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DATE_FROM']) . "</td>";
                echo "<td>" . htmlspecialchars($row['DATE_TO']) . "</td>";
                echo "<td>" . htmlspecialchars($row['TRUCK_DATE']) . "</td>";
                echo "<td>" . htmlspecialchars($row['NO_OF_TRUCKS']) . "</td>";
                echo "</tr>";
            }
        } catch (Exception $e) {
            echo "<tr><td colspan='7'>Error: " . $e->getMessage() . "</td></tr>";
        }

        oci_close($conn);
        ?>
    </table>

    <div class="buttons">

        <button type="button" onclick="history.back()">Go Back</button>
    </div>
</body>

</html>