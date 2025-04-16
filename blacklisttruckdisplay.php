<?php
// Database connection file
include('../db_config.php'); // Assuming the connection script is saved as db_connection.php
include('../navbar.php');
// Fetch all records from the blacklisted_trucks table
try {
    $sql = "SELECT id, truck_no, brand, model, make, chasis_no, transporter_name, reason, TO_CHAR(start_date, 'YYYY-MM-DD') AS start_date, TO_CHAR(end_date, 'YYYY-MM-DD') AS end_date FROM B_TRUCKS ORDER BY id";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $records = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $records[] = $row;
    }

    oci_free_statement($stmt);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Close the connection
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklisted Trucks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-btn:hover {
            background-color: #5a6268;
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
    <h2>Blacklisted Trucks</h2>

    <?php if (!empty($records)): ?>
        <table>
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Truck No.</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Make</th>
                    <th>Chasis No.</th>
                    <th>Transporter Name</th>
                    <th>Reason</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <!-- <td><?php echo htmlspecialchars($record['ID']); ?></td> -->
                        <td><?php echo htmlspecialchars($record['TRUCK_NO']); ?></td>
                        <td><?php echo htmlspecialchars($record['BRAND']); ?></td>
                        <td><?php echo htmlspecialchars($record['MODEL']); ?></td>
                        <td><?php echo htmlspecialchars($record['MAKE']); ?></td>
                        <td><?php echo htmlspecialchars($record['CHASIS_NO']); ?></td>
                        <td><?php echo htmlspecialchars($record['TRANSPORTER_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($record['REASON']); ?></td>
                        <td><?php echo htmlspecialchars($record['START_DATE']); ?></td>
                        <td><?php echo htmlspecialchars($record['END_DATE']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records found in the database.</p>
    <?php endif; ?>

    <div class="buttons">
       
       <button type="button" onclick="history.back()">Go Back</button>
   </div>
</body>
</html>
