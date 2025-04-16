<?php
// Include database connection
include('../db_config.php');
include('../navbar.php');

$transporter_pan = $_POST['transporter_pan'] ?? '';

// Fetch details when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetchDetails'])) {
    // SQL query to fetch details from the B_T table
    $query = "SELECT * FROM B_T WHERE transporter_pan = :transporter_pan";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":transporter_pan", $transporter_pan);
    oci_execute($stid);
    $transporterDetails = oci_fetch_assoc($stid);
    oci_free_statement($stid);
}

// Save edited details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveDetails'])) {
    // Retrieve form data
    $transporter_name = $_POST['transporter_name'] ?? '';
    $transporter_pan = $_POST['transporter_pan'] ?? '';
    $blacklist_reason = $_POST['blacklist_reason'] ?? '';
    $other_reason = $_POST['other_reason'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';

    // If the reason is 'Other', use the other_reason value
    if ($blacklist_reason === 'other' && !empty($other_reason)) {
        $blacklist_reason = $other_reason;
    }

    // SQL query to update data in the B_T table
    $query = "UPDATE B_T SET 
                transporter_name = :transporter_name, 
                blacklist_reason = :blacklist_reason, 
                start_date = TO_DATE(:start_date, 'YYYY-MM-DD'), 
                end_date = TO_DATE(:end_date, 'YYYY-MM-DD') 
              WHERE transporter_pan = :transporter_pan";

    $stid = oci_parse($conn, $query);

    // Bind parameters
    oci_bind_by_name($stid, ":transporter_name", $transporter_name);
    oci_bind_by_name($stid, ":blacklist_reason", $blacklist_reason);
    oci_bind_by_name($stid, ":start_date", $start_date);
    oci_bind_by_name($stid, ":end_date", $end_date);
    oci_bind_by_name($stid, ":transporter_pan", $transporter_pan);

    // Execute query
    $result = oci_execute($stid);

    if ($result) {
        echo "<script>alert('Data has been updated successfully!'); window.location.href = '../employee.php';</script>";
    } else {
        $e = oci_error($stid);
        echo "<script>alert('Error: " . addslashes($e['message']) . "');</script>";
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
    <title>Edit Blacklisted Transporter</title>
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
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        <h2>Edit Blacklisted Transporter</h2>

        <!-- Fetch details form -->
        <form method="POST">
            <div class="form-group">
                <label for="transporterPan">Transporter PAN:</label>
                <input type="text" id="transporterPan" name="transporter_pan" placeholder="Enter Transporter PAN" required>
            </div>
            <button type="submit" name="fetchDetails">Fetch Details</button>
            <button type="button" onclick="history.back()">Go Back</button>
        </form>

        <?php if (!empty($transporterDetails)) { ?>
            <!-- Edit form -->
            <form method="POST">
                <div class="form-group">
                    <label for="transporterName">Transporter Name:</label>
                    <input type="text" id="transporterName" name="transporter_name" value="<?php echo htmlspecialchars($transporterDetails['TRANSPORTER_NAME'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="transporterPan">Transporter PAN No.</label>
                    <input type="text" id="transporterPan" name="transporter_pan" value="<?php echo htmlspecialchars($transporterDetails['TRANSPORTER_PAN'] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="blacklistReason">Reason for Blacklisting:</label>
                    <select id="blacklistReason" name="blacklist_reason" required>
                        <option value="Fraud" <?php echo ($transporterDetails['BLACKLIST_REASON'] == 'Fraud') ? 'selected' : ''; ?>>Fraud</option>
                        <option value="Non-compliance" <?php echo ($transporterDetails['BLACKLIST_REASON'] == 'Non-compliance') ? 'selected' : ''; ?>>Non-compliance</option>
                        <option value="other" <?php echo ($transporterDetails['BLACKLIST_REASON'] == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <textarea id="other_reason" name="other_reason" style="display: <?php echo ($transporterDetails['BLACKLIST_REASON'] == 'other') ? 'block' : 'none'; ?>;"><?php echo htmlspecialchars($transporterDetails['OTHER_REASON'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Period of Blacklisting:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $transporterDetails['START_DATE'] ? date('Y-m-d', strtotime($transporterDetails['START_DATE'])) : ''; ?>" required>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $transporterDetails['END_DATE'] ? date('Y-m-d', strtotime($transporterDetails['END_DATE'])) : ''; ?>" required>
                </div>
                <div class="buttons">
                    <button type="submit" name="saveDetails">Save</button>
                    <button type="button" onclick="history.back()">Go Back</button>
                </div>
            </form>
        <?php } ?>
    </div>

    <script>
        document.getElementById('blacklistReason').addEventListener('change', function () {
            document.getElementById('other_reason').style.display = this.value === 'other' ? 'block' : 'none';
        });
    </script>
</body>
</html>
