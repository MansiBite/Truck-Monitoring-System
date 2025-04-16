<?php include('../navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklisted Transporters</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color:rgb(246, 239, 239);
       
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        

        /* Form Container */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(7, 14, 210, 0.1);
            width: 90%;
            max-width: 400px;
            margin: auto;
        }

        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
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
        }

        .form-group textarea {
            resize: none;
        }

        .form-group .date-range {
            display: flex;
            justify-content: space-between;
        }

        .form-group .date-range input {
            width: 48%;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button-container .save-btn {
            background-color: #4CAF50;
            color: #fff;
        }

        .button-container .back-btn {
            background-color: #f44336;
            color: #fff;
        }

        /* Mobile Responsiveness */
        @media (max-width: 600px) {
            .form-container {
                width: 95%;
            }

            .form-group .date-range {
                flex-direction: column;
            }

            .form-group .date-range input {
                width: 100%;
                margin-bottom: 10px;
            }

            .button-container {
                flex-direction: column;
            }

            .button-container button {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Blacklisted Transporters</h2>
        <form action="../savephp/save_blacklisted_transporter.php" method="POST">
            <div class="form-group">
                <label for="transporterName">Transporter Name</label>
                <input type="text" id="transporterName" name="transporterName" required>
            </div>
            <div class="form-group">
                <label for="transporterPan">Transporter PAN No.</label>
                <input type="text" id="transporterPan" name="transporterPan" required>
            </div>
            <div class="form-group">
                <label for="blacklistReason">Reason for Blacklisting</label>
                <select id="blacklistReason" name="blacklistReason" required>
                    <option value="">Select Reason</option>
                    <option value="Fraud">Fraud</option>
                    <option value="Non-compliance">Non-compliance</option>
                    <option value="Other">Other</option>
                </select>
                <textarea id="otherReason" name="otherReason" placeholder="Please specify if 'Other'" style="display:none; margin-top: 10px;"></textarea>
            </div>
            <div class="form-group">
                <label>Period of Blacklisting</label>
                <div class="date-range">
                    <input type="date" id="fromDate" name="fromDate" required>
                    <input type="date" id="toDate" name="toDate" required>
                </div>
            </div>
            <div class="button-container">
                <button type="submit" class="save-btn">Save</button>
                <button type="button" class="back-btn" onclick="window.history.back();">Go Back</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("blacklistReason").addEventListener("change", function() {
            var otherReasonField = document.getElementById("otherReason");
            if (this.value === "Other") {
                otherReasonField.style.display = "block";
            } else {
                otherReasonField.style.display = "none";
            }
        });
    </script>
</body>
</html>
