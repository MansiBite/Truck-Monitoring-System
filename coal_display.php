<?php
// Include database configuration
include('../db_config.php');
include('../navbar.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in.'); window.location.href = '../login.php';</script>";
    exit;
}

// Query to fetch data from coal_availability table
$query = "SELECT * FROM coal_availability";
$stid = oci_parse($conn, $query);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Query execution failed: " . htmlentities($e['message']));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coal Availability</title>
    <link rel="stylesheet" href="../style.css"> <!-- Include external CSS if needed -->
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

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
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
    <h2>Coal Availability</h2>
    <table>
        <thead>
            <tr>
                <th>Area Code</th>
                <th>Area Name</th>
                <th>Mine Name</th>
                <th>Mine Code</th>
                <th>Scheme</th>
                <th>Grade</th>
                <th>Size</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

            <?php
            // Fetch and display data from the database
            while ($row = oci_fetch_assoc($stid)) {
                echo "<tr>
                <td>{$row['AREA_CODE']}</td>
                <td>{$row['AREA_NAME']}</td>
                <td>{$row['MINE_NAME']}</td>
                <td>{$row['MINE_CODE']}</td>
                <td>{$row['SCHEME']}</td>
                <td>{$row['GRADE']}</td>
                <td>{$row['A_SIZE']}</td>
                <td>{$row['START_DATE']}</td>
                <td>{$row['END_DATE']}</td>
                <td>{$row['QUANTITY']}</td>
                <td><a href='coal_edit.php?id={$row['ID']}'>Edit</a></td>
              </tr>";
            }
            ?>

        </tbody>
    </table>
    <div class="buttons">
        <button type="button" onclick="history.back()">Go Back</button>
    </div>
</body>
</html>

<?php
// Free resources and close connection
oci_free_statement($stid);
oci_close($conn);
?>