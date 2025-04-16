<?php
// Include the database connection file
include('../db_config.php');

// Fetch all transporter records
$sql_select = "SELECT name, aadhar_no, license_no, password FROM transporter";
$stmt_select = oci_parse($conn, $sql_select);
oci_execute($stmt_select);

$transporter_records = [];
while ($row = oci_fetch_assoc($stmt_select)) {
    $transporter_records[] = $row;
}

// Free the statement
oci_free_statement($stmt_select);

// Close the connection
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Created Transporters</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
    </style>
</head>
<body>
    <h2>Created Transporters</h2>

    <?php if (!empty($transporter_records)): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>AADHAR No.</th>
                    <th>License No.</th>
                    <!-- <th>Password</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transporter_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['NAME']); ?></td>
                        <td><?php echo htmlspecialchars($record['AADHAR_NO']); ?></td>
                        <td><?php echo htmlspecialchars($record['LICENSE_NO']); ?></td>
                        <!-- <td><?php echo htmlspecialchars($record['PASSWORD']); ?></td> -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No transporter records found.</p>
    <?php endif; ?>

    <a href="../customer.php" class="back-btn">Back to Menu</a>
</body>
</html>


