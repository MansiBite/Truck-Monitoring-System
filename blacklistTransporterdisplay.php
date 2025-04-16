<?php
// Database connection file
include('../db_config.php'); // Assuming the connection script is saved as db_connect.php
include('../navbar.php');
// Fetch all records from the blacklisted_transporters table
try {
    $sql = "SELECT id, transporter_name, transporter_pan, blacklist_reason, other_reason, TO_CHAR(start_date, 'YYYY-MM-DD') AS start_date, TO_CHAR(end_date, 'YYYY-MM-DD') AS end_date FROM B_T ORDER BY id";
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
    <title>Blacklisted Transporters</title>
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
    <h2>Blacklisted Transporters</h2>

    <?php if (!empty($records)): ?>
        <table>
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Transporter Name</th>
                    <th>Transporter PAN No.</th>
                    <th>Blacklist Reason</th>
                    <th>Other Reason</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <!-- <td><?php echo htmlspecialchars($record['ID']); ?></td> -->
                        <td><?php echo htmlspecialchars($record['TRANSPORTER_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($record['TRANSPORTER_PAN']); ?></td>
                        <td><?php echo htmlspecialchars($record['BLACKLIST_REASON']); ?></td>
                        <td><?php echo htmlspecialchars($record['OTHER_REASON']); ?></td>
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
