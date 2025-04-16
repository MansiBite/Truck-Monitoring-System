<?php
include('../navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklisted Drivers</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Include external CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .button-container .save-btn {
            background-color: #28a745;
            color: white;
        }

        .button-container .back-btn {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Blacklisted Drivers</h2>
        <form method="POST" action="../savephp/save_blacklisted_driver.php">
            <div class="form-group">
                <label for="name">Name as per AADHAR</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="aadhar-no">AADHAR No.</label>
                <input type="text" id="aadhar-no" name="aadhar_no" pattern="\d{12}" maxlength="12" required>
            </div>

            <div class="form-group">
                <label for="license-no">Driving License No.</label>
                <input type="text" id="license-no" name="license_no" required>
            </div>

            <div class="form-group">
                <label for="reason">Reason for Blacklisting</label>
                <select id="reason" name="reason" required>
                    <option value="">Select Reason</option>
                    <option value="Traffic Violations">Traffic Violations</option>
                    <option value="Fraudulent Documents">Fraudulent Documents</option>
                    <option value="Other">Other</option>
                </select>
                <textarea id="other-reason" name="other_reason" placeholder="Please specify if 'Other'" style="display:none; margin-top:10px;"></textarea>
            </div>

            <div class="form-group">
                <label for="blacklist-period">Period of Blacklisting</label>
                <div style="display: flex; gap: 10px;">
                    <input type="date" id="start-date" name="start_date" required>
                    <span>to</span>
                    <input type="date" id="end-date" name="end_date" required>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="back-btn" onclick="window.history.back();">Go Back</button>
            </div>
        </form>
    </div>

    <script>
        // Show/Hide 'Other' text area based on selected reason
        document.getElementById('reason').addEventListener('change', function () {
            const otherReasonField = document.getElementById('other-reason');
            if (this.value === 'Other') {
                otherReasonField.style.display = 'block';
            } else {
                otherReasonField.style.display = 'none';
            }
        });
    </script>
</body>
</html>

