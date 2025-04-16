<?php
include('../navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklisted Trucks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .btn-group {
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Blacklisted Trucks</h2>
       <form action="../savephp/save_blacklisted_trucks.php" method="POST">
            <div class="form-group">
                <label for="truckNo">Truck No.</label>
                <input type="text" id="truckNo" name="truckNo" required>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" required>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" required>
            </div>
            <div class="form-group">
                <label for="make">Make</label>
                <input type="text" id="make" name="make" required>
            </div>
            <div class="form-group">
                <label for="chasisNo">Chasis No.</label>
                <input type="text" id="chasisNo" name="chasisNo" required>
            </div>
            <div class="form-group">
                <label for="transporterName">Transporter Name</label>
                <input type="text" id="transporterName" name="transporterName" required>
            </div>
            <div class="form-group">
                <label for="reason">Reason for Blacklisting</label>
                <select id="reason" name="reason" required>
                    <option value="">Select Reason</option>
                    <option value="Late Deliveries">Late Deliveries</option>
                    <option value="Damaged Goods">Damaged Goods</option>
                    <option value="Violation of Rules">Violation of Rules</option>
                    <option value="Other">Other</option>
                </select>
                <textarea id="otherReason" name="otherReason" placeholder="Specify if Other" style="display: none;"></textarea>
            </div>
            <div class="form-group">
                <label for="blacklistPeriod">Period of Blacklisting</label>
                <input type="date" id="startDate" name="startDate" required> to 
                <input type="date" id="endDate" name="endDate" required>
            </div>
            <div class="btn-group">
    <button type="submit" class="btn">Save</button>
               <button type="button" class="btn" onclick="history.back()">Go Back</button>
            </div>
        </form>
    </div>

    <script>
      
      // Show/Hide 'Other' text area based on selected reason
document.getElementById('reason').addEventListener('change', function () {
    const otherReasonField = document.getElementById('otherReason'); // Correct ID
    if (this.value === 'Other') {
        otherReasonField.style.display = 'block';
    } else {
        otherReasonField.style.display = 'none';
    }
});
    </script>
</body>
</html>
