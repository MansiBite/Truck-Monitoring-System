<?php
// Include database connection
include('../db_config.php');
include('../navbar.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchDetails'])) {
    // Retrieve the Aadhar number from the form
    $aadhar_no = $_POST['aadhar_no'];

    // SQL query to fetch details from the blacklisted_driver table
    $query = "SELECT * FROM B_D WHERE aadhar_no = :aadhar_no";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":aadhar_no", $aadhar_no);
    oci_execute($stid);

    $driverDetails = oci_fetch_assoc($stid);
    oci_free_statement($stid);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveDetails'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $aadhar_no = $_POST['aadhar_no'];
    $license_no = $_POST['license_no'];
    $reason = $_POST['reason'];
    $other_reason = isset($_POST['other_reason']) ? $_POST['other_reason'] : null;
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // If the reason is 'Other', use the value from the textarea
    if ($reason === 'Other' && !empty($other_reason)) {
        $reason = $other_reason;
    }

    // SQL query to update data in the blacklisted_driver table
    $query = "UPDATE B_D SET name = :name, license_no = :license_no, reason = :reason, start_date = TO_DATE(:start_date, 'YYYY-MM-DD'), end_date = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE aadhar_no = :aadhar_no";

    $stid = oci_parse($conn, $query);

    // Bind the parameters to the SQL query
    oci_bind_by_name($stid, ":aadhar_no", $aadhar_no);
    oci_bind_by_name($stid, ":name", $name);
    oci_bind_by_name($stid, ":license_no", $license_no);
    oci_bind_by_name($stid, ":reason", $reason);
    oci_bind_by_name($stid, ":start_date", $start_date);
    oci_bind_by_name($stid, ":end_date", $end_date);

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
    <title>Edit Blacklisted Driver</title>
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
        <h2>Edit Blacklisted Driver</h2>

        <!-- Fetch details form -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="aadhar_no">Aadhar No.:</label>
                <input type="text" id="aadhar_no" name="aadhar_no" placeholder="Enter Aadhar No." pattern="\d{12}" maxlength="12" required>
            </div>
            <button type="submit" name="fetchDetails">Fetch Details</button>
            <button type="button" onclick="goBack()">Go Back</button>
        </form>

        <?php if (isset($driverDetails)) { ?>
            <!-- Edit form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $driverDetails['NAME']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="aadhar_no">Aadhar No.:</label>
                    <input type="text" id="aadhar_no" name="aadhar_no" pattern="\d{12}" maxlength="12" value="<?php echo $driverDetails['AADHAR_NO']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="license_no">License No.:</label>
                    <input type="text" id="license_no" name="license_no" value="<?php echo $driverDetails['LICENSE_NO']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Blacklisting:</label>
                    <select id="reason" name="reason" required>
                        <option value="violation" <?php echo ($driverDetails['REASON'] == 'violation') ? 'selected' : ''; ?>>Violation</option>
                        <option value="fraud" <?php echo ($driverDetails['REASON'] == 'fraud') ? 'selected' : ''; ?>>Fraudulent Activities</option>
                        <option value="other" <?php echo ($driverDetails['REASON'] == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <textarea id="other_reason" name="other_reason" style="display: <?php echo ($driverDetails['REASON'] == 'other') ? 'block' : 'none'; ?>; margin-top:10px;"><?php echo $driverDetails['OTHER_REASON']; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Period of Blacklisting:</label>
                    <div class="flex">
                        <input type="date" id="start_date" name="start_date" value="<?php echo date('Y-m-d', strtotime($driverDetails['START_DATE'])); ?>" required>
                        <input type="date" id="end_date" name="end_date" value="<?php echo date('Y-m-d', strtotime($driverDetails['END_DATE'])); ?>" required>
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
        const otherReasonField = document.getElementById('other_reason');

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