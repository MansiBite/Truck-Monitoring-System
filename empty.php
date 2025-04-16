<?php
// Database connection file
include('../db_config.php');// Assuming this file contains the $conn connection
include('../navbar.php');
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchDetails'])) {
    // Retrieve truck number from the form
    $truck_no = $_POST['truck_no'];

    // SQL query to fetch details from the B_TRUCKS table
    $query = "SELECT * FROM WCL_Check_Post WHERE truck_no = :truck_no";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":truck_no", $truck_no);
    oci_execute($stid);

    $truckDetails = oci_fetch_assoc($stid);
    oci_free_statement($stid);
}

// **Update Data in Database**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $truck_no = $_POST['truck_no'];
    $sales_order_no = $_POST['sales_order_no'];
    $date_in = $_POST['date_in'];
    $time_in = $_POST['time_in'] . " " . $_POST['am_pm_in'];
    $date_out = $_POST['date_out'] ?? null;
    $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] . " " . $_POST['am_pm_out'] : null;

    // Convert date format
    $date_in = date("d-M-Y", strtotime($date_in));
    $date_out = !empty($date_out) ? date("d-M-Y", strtotime($date_out)) : null;

    // Convert time to 24-hour format
    $time_in = date("H:i:s", strtotime($time_in));
    $time_out = !empty($time_out) ? date("H:i:s", strtotime($time_out)) : null;

    // **Update Query**
    $sql_update = "UPDATE WCL_Check_Post 
                   SET sales_order_no = :sales_order_no, 
                       date_in = TO_DATE(:date_in, 'DD-MON-YYYY'), 
                       time_in = :time_in, 
                       date_out = TO_DATE(:date_out, 'DD-MON-YYYY'), 
                       time_out = :time_out 
                   WHERE truck_no = :truck_no";

    $stmt_update = oci_parse($conn, $sql_update);

    // Bind parameters
    oci_bind_by_name($stmt_update, ':sales_order_no', $sales_order_no);
    oci_bind_by_name($stmt_update, ':date_in', $date_in);
    oci_bind_by_name($stmt_update, ':time_in', $time_in);
    oci_bind_by_name($stmt_update, ':date_out', $date_out);
    oci_bind_by_name($stmt_update, ':time_out', $time_out);
    oci_bind_by_name($stmt_update, ':truck_no', $truck_no);

    // Execute query
    if (oci_execute($stmt_update)) {
        echo "<script>alert('Data updated successfully!'); window.location.href='status@wcl_edit.php';</script>";
    } else {
        $e = oci_error($stmt_update);
        die("Error updating data: " . $e['message']);
    }
}

oci_close($conn);
?> 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit WCL Check Post Data</title>
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
        <h2>Edit Status @WCL Check Post</h2>

        <!-- Fetch details form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="truck_no">Truck No.:</label>
                <input type="text" id="truck_no" name="truck_no" placeholder="Enter Truck No." required>
            </div>
            <button type="submit" name="fetchDetails">Fetch Details</button>
            <button type="button" onclick="goBack()">Go Back</button>
        </form>

        <?php if (isset($truckDetails)) { ?>
            <!-- Edit form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="truck_no">Truck No.:</label>
                    <input type="text" id="truck_no" name="truck_no" value="<?php echo $truckDetails['TRUCK_NO']; ?>" readonly>
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
