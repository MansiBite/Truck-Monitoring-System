<?php
include('../db_config.php');
include('../navbar.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in.'); window.location.href = '../login.php';</script>";
    exit;
}

// Check if the unique ID is present
if (!isset($_GET['id'])) {
    die("Error: Record ID is missing.");
}

$id = $_GET['id']; // Get the unique ID from URL

// Fetch the record
$query = "SELECT * FROM coal_availability WHERE ID = :id";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":id", $id);
oci_execute($stid);

$row = oci_fetch_assoc($stid);
if (!$row) {
    die("Error: Record not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $area_code = $_POST['area_code'];
    $area_name = $_POST['area_name'];
    $mine_name = $_POST['mine_name'];
    $mine_code = $_POST['mine_code']; // New Field
    $scheme = $_POST['scheme'];
    $grade = $_POST['grade'];
    $size = $_POST['a_size'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $quantity = $_POST['quantity'];

    $updateQuery = "UPDATE coal_availability SET 
        AREA_CODE = :areaCode, 
        AREA_NAME = :areaName, 
        MINE_NAME = :mineName, 
        MINE_CODE = :mineCode, 
        SCHEME = :scheme, 
        GRADE = :grade, 
        A_SIZE = :aSize, 
        START_DATE = TO_DATE(:startDate, 'YYYY-MM-DD'), 
        END_DATE = TO_DATE(:endDate, 'YYYY-MM-DD'), 
        QUANTITY = :quantity 
        WHERE ID = :recordId";

    $updateStmt = oci_parse($conn, $updateQuery);

    oci_bind_by_name($updateStmt, ":areaCode", $area_code);
    oci_bind_by_name($updateStmt, ":areaName", $area_name);
    oci_bind_by_name($updateStmt, ":mineName", $mine_name);
    oci_bind_by_name($updateStmt, ":mineCode", $mine_code);
    oci_bind_by_name($updateStmt, ":scheme", $scheme);
    oci_bind_by_name($updateStmt, ":grade", $grade);
    oci_bind_by_name($updateStmt, ":aSize", $size);
    oci_bind_by_name($updateStmt, ":startDate", $start_date);
    oci_bind_by_name($updateStmt, ":endDate", $end_date);
    oci_bind_by_name($updateStmt, ":quantity", $quantity);
    oci_bind_by_name($updateStmt, ":recordId", $id);

    if (oci_execute($updateStmt)) {
        echo "<script>alert('Record updated successfully!'); window.location.href = '../employee.php';</script>";
    } else {
        $e = oci_error($updateStmt);
        echo "Error: " . htmlentities($e['message']);
    }
}

// Close connection
oci_free_statement($stid);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coal Availability</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            align-items: center;
            justify-content: center;
            background-color: #f4f4f4;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            max-height: 90vh;
            margin: 50px auto;
            /* Ensures the form stays within the screen */
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            width: 100%;
            transition: 0.3s;
        }

        input:focus {
            border-color: #4facfe;
            outline: none;
            box-shadow: 0 0 8px rgba(79, 172, 254, 0.5);
        }

        button {
            margin-top: 10px;
            background: #0072ff;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background: #0055cc;
            box-shadow: 0px 4px 10px rgba(0, 85, 204, 0.3);
        }

        /* Make form scrollable on small screens */
        @media screen and (max-width: 700px) {
            .container {
                max-height: 80vh;
                overflow-y: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Coal Availability Record</h2>
        <form method="post">
            <label>Area Code:</label>
            <input type="text" name="area_code" value="<?php echo $row['AREA_CODE']; ?>" required>

            <label>Area Name:</label>
            <input type="text" name="area_name" value="<?php echo $row['AREA_NAME']; ?>" required>

            <label>Mine Name:</label>
            <input type="text" name="mine_name" value="<?php echo $row['MINE_NAME']; ?>" required>

            <label>Mine Code:</label>
            <input type="text" name="mine_code" value="<?php echo $row['MINE_CODE']; ?>" required>

            <label>Scheme:</label>
            <input type="text" name="scheme" value="<?php echo $row['SCHEME']; ?>" required>

            <label>Grade:</label>
            <input type="text" name="grade" value="<?php echo $row['GRADE']; ?>" required>

            <label>Size:</label>
            <input type="text" name="a_size" value="<?php echo $row['A_SIZE']; ?>" required>

            <label>Start Date:</label>
            <input type="date" name="start_date" value="<?php echo date('Y-m-d', strtotime($row['START_DATE'])); ?>"
                required>

            <label>End Date:</label>
            <input type="date" name="end_date" value="<?php echo date('Y-m-d', strtotime($row['END_DATE'])); ?>"
                required>

            <label>Quantity:</label>
            <input type="number" name="quantity" value="<?php echo $row['QUANTITY']; ?>" required>

            <button type="submit">Save Changes</button>
            <button type="button" onclick="history.back()">Go Back</button>
        </form>
    </div>
</body>
</html>