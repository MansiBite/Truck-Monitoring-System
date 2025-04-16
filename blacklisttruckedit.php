<?php
// Database connection file
include('../db_config.php');// Assuming this file contains the $conn connection
include('../navbar.php');
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchDetails'])) {
    // Retrieve truck number from the form
    $truckNo = $_POST['truckNo'];

    // SQL query to fetch details from the B_TRUCKS table
    $query = "SELECT * FROM B_TRUCKS WHERE truck_no = :truckNo";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":truckNo", $truckNo);
    oci_execute($stid);

    $truckDetails = oci_fetch_assoc($stid);
    oci_free_statement($stid);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveDetails'])) {
    // Retrieve form data
    $truckNo = $_POST['truckNo'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $make = $_POST['make'];
    $chasisNo = $_POST['chasisNo'];
    $transporterName = $_POST['transporterName'];
    $reason = $_POST['reason'];
    $otherReason = isset($_POST['otherReason']) ? $_POST['otherReason'] : null;
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // If the reason is 'Other', use the value from the textarea
    if ($reason === 'Other' && !empty($otherReason)) {
        $reason = $otherReason;
    }

    // SQL query to update data in the B_TRUCKS table
    $query = "UPDATE B_TRUCKS SET 
              brand = :brand, model = :model, make = :make, chasis_no = :chasisNo,
              transporter_name = :transporterName, reason = :reason, other_reason = :otherReason,
              start_date = TO_DATE(:startDate, 'YYYY-MM-DD'), end_date = TO_DATE(:endDate, 'YYYY-MM-DD')
              WHERE truck_no = :truckNo";

    $stid = oci_parse($conn, $query);

    // Bind the parameters to the SQL query
    oci_bind_by_name($stid, ":truckNo", $truckNo);
    oci_bind_by_name($stid, ":brand", $brand);
    oci_bind_by_name($stid, ":model", $model);
    oci_bind_by_name($stid, ":make", $make);
    oci_bind_by_name($stid, ":chasisNo", $chasisNo);
    oci_bind_by_name($stid, ":transporterName", $transporterName);
    oci_bind_by_name($stid, ":reason", $reason);
    oci_bind_by_name($stid, ":otherReason", $otherReason);
    oci_bind_by_name($stid, ":startDate", $startDate);
    oci_bind_by_name($stid, ":endDate", $endDate);

    // Execute the query
    $result = oci_execute($stid);

    if ($result) {
        echo "<script>alert('Data has been updated successfully!'); window.location.href = '../employee.php'; </script>";
    } else {
        $e = oci_error($stid);
        echo "<script>alert('Error: " . $e['message'] . "');</script>";
    }

    oci_free_statement($stid);
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blacklisted Truck</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: none;
        }

        .form-group .flex {
            display: flex;
            gap: 10px;
        }

        .form-group .flex input {
            flex: 1;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

        .buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Blacklisted Truck</h2>

        <!-- Fetch details form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="truckNo">Truck No.:</label>
                <input type="text" id="truckNo" name="truckNo" placeholder="Enter Truck No." required>
            </div>
            <button type="submit" name="fetchDetails">Fetch Details</button>
            <button type="button" onclick="goBack()">Go Back</button>
        </form>

        <?php if (isset($truckDetails)) { ?>
            <!-- Edit form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="truckNo">Truck No.:</label>
                    <input type="text" id="truckNo" name="truckNo" value="<?php echo $truckDetails['TRUCK_NO']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="brand">Brand:</label>
                    <input type="text" id="brand" name="brand" value="<?php echo $truckDetails['BRAND']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="model">Model:</label>
                    <input type="text" id="model" name="model" value="<?php echo $truckDetails['MODEL']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="make">Make:</label>
                    <input type="text" id="make" name="make" value="<?php echo $truckDetails['MAKE']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="chasisNo">Chasis No.:</label>
                    <input type="text" id="chasisNo" name="chasisNo" value="<?php echo $truckDetails['CHASIS_NO']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="transporterName">Transporter Name:</label>
                    <input type="text" id="transporterName" name="transporterName" value="<?php echo $truckDetails['TRANSPORTER_NAME']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Blacklisting:</label>
                    <select id="reason" name="reason" required>
                        <option value="violation" <?php echo ($truckDetails['REASON'] == 'violation') ? 'selected' : ''; ?>>Violation</option>
                        <option value="fraud" <?php echo ($truckDetails['REASON'] == 'fraud') ? 'selected' : ''; ?>>Fraudulent Activities</option>
                        <option value="other" <?php echo ($truckDetails['REASON'] == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <textarea id="otherReason" name="otherReason" style="display: <?php echo ($truckDetails['REASON'] == 'other') ? 'block' : 'none'; ?>; margin-top:10px;"><?php echo $truckDetails['OTHER_REASON']; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Period of Blacklisting:</label>
                    <div class="flex">
                        <input type="date" id="startDate" name="startDate" value="<?php echo date('Y-m-d', strtotime($truckDetails['START_DATE'])); ?>" required>
                        <input type="date" id="endDate" name="endDate" value="<?php echo date('Y-m-d', strtotime($truckDetails['END_DATE'])); ?>" required>
                    </div>
                </div>
                <div class="buttons">
                    <button type="submit" name="saveDetails">Save</button>
                    <button type="button" onclick="goBack()">Go Back</button>
                </div>
            </form>
        <?php } ?>
    </div>

    <script>
        const reasonSelect = document.getElementById('reason');
        const otherReasonField = document.getElementById('otherReason');

        reasonSelect.addEventListener('change', () => {
            if (reasonSelect.value === 'other') {
                otherReasonField.style.display = 'block';
                otherReasonField.required = true;
            } else {
                otherReasonField.style.display = 'none';
                otherReasonField.required = false;
            }
        });

        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>
