<?php
include('../db_config.php'); // Database connection
include('../navbar.php');
session_start(); // Start session to get logged-in user

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href = '../login.php';</script>";
    exit;
}

$username = $_SESSION['username'];

// Fetch Area Code and Area Name based on logged-in username
$query = "SELECT area_code, area_name FROM login WHERE username = :username";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":username", $username);
oci_execute($stid);
$row = oci_fetch_assoc($stid);

if (!$row) {
    echo "<script>alert('No area details found for this user.'); window.location.href = '../login.php';</script>";
    exit;
}

$areaCode = $row['AREA_CODE'];
$areaName = $row['AREA_NAME'];
oci_free_statement($stid);

// Fetch Mine Names and Codes only for the logged-in user's area
$mineQuery = "SELECT mine_name, mine_code FROM mine_table WHERE area_name = :areaName";
$mineStid = oci_parse($conn, $mineQuery);
oci_bind_by_name($mineStid, ":areaName", $areaName);
oci_execute($mineStid);

$mines = [];
while ($mineRow = oci_fetch_assoc($mineStid)) {
    $mines[] = $mineRow;
}
oci_free_statement($mineStid);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $mineName = $_POST['mineName'];
    $mineCode = $_POST['mineCode'];
    $scheme = $_POST['scheme'];
    $grade = $_POST['grade'];
    $a_size = $_POST['a_size'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $quantity = $_POST['quantity'];

    // Insert data into the database
    $query = "INSERT INTO coal_availability (area_code, area_name, mine_name, mine_code, scheme, grade, a_size, start_date, end_date, quantity) 
              VALUES (:areaCode, :areaName, :mineName, :mineCode, :scheme, :grade, :a_size, TO_DATE(:startDate, 'YYYY-MM-DD'), TO_DATE(:endDate, 'YYYY-MM-DD'), :quantity)";

    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":areaCode", $areaCode);
    oci_bind_by_name($stid, ":areaName", $areaName);
    oci_bind_by_name($stid, ":mineName", $mineName);
    oci_bind_by_name($stid, ":mineCode", $mineCode);
    oci_bind_by_name($stid, ":scheme", $scheme);
    oci_bind_by_name($stid, ":grade", $grade);
    oci_bind_by_name($stid, ":a_size", $a_size);
    oci_bind_by_name($stid, ":startDate", $startDate);
    oci_bind_by_name($stid, ":endDate", $endDate);
    oci_bind_by_name($stid, ":quantity", $quantity);

    $result = oci_execute($stid);

    if (!$result) {
        $e = oci_error($stid);
        echo json_encode(["status" => "error", "message" => $e['message']]);
        oci_free_statement($stid);
        oci_close($conn);
        exit;
    }

    oci_free_statement($stid);
    oci_close($conn);

    echo "<script>alert('Data saved successfully!'); window.location.href = '../employee.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coal Availability</title>
    <script>
        function updateMineCode() {
            var mineName = document.getElementById("mineName");
            var mineCode = document.getElementById("mineCode");
            mineCode.value = mineName.options[mineName.selectedIndex].getAttribute("data-code");
        }

        function setEndDate() {
            var startDate = document.getElementById("start_date").value;
            if (startDate) {
                var start = new Date(startDate);
                var end = new Date(start);
                end.setDate(start.getDate() + 7);

                var endDate = document.getElementById("end_date");
                endDate.value = end.toISOString().split('T')[0]; // Convert to YYYY-MM-DD format
                endDate.setAttribute("min", endDate.value);
                endDate.setAttribute("max", endDate.value);
            }
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
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

        button:hover {
            background-color: #0056b3;
        }

        button[type="button"] {
            background-color: #6c757d;
        }

        button[type="button"]:hover {
            background-color: #5a6268;
        }

        @media (max-width: 768px) {
            .container {
                width: 80%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Coal Availability</h2>
        <form id="coalForm" method="POST">
            <div class="form-group">
                <label>Area Name:</label>
                <input type="text" name="areaName" value="<?= htmlspecialchars($areaName) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Area Code:</label>
                <input type="text" name="areaCode" value="<?= htmlspecialchars($areaCode) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="mineName">Mine Name:</label>
                <select id="mineName" name="mineName" onchange="updateMineCode()" required>
                    <option value="">Select Mine Name</option>
                    <?php foreach ($mines as $mine) { ?>
                        <option value="<?= htmlspecialchars($mine['MINE_NAME']) ?>"
                            data-code="<?= htmlspecialchars($mine['MINE_CODE']) ?>">
                            <?= htmlspecialchars($mine['MINE_NAME']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Mine Code:</label>
                <input type="text" id="mineCode" name="mineCode" readonly>
            </div>
            <div class="form-group">
                <label for="scheme">Scheme:</label>
                <select id="scheme" name="scheme" required>
                    <option value="">Select Scheme</option>
                    <option value="FSA Linkage Auction (NRS)">FSA Linkage Auction (NRS)</option>
                    <option value="FSA Linkage Auction (SHAKTI)">FSA Linkage Auction (SHAKTI)</option>
                    <option value="Bridge Linkage">Bridge Linkage</option>
                    <option value="Fuel Supply Agreement">Fuel Supply Agreement</option>
                    <option value="Spot Auction">Spot Auction</option>
                </select>
            </div>
            <div class="form-group">
                <label for="grade">Grade:</label>
                <select id="grade" name="grade" required>
                    <option value="">Select Grade</option>
                    <option value="G6">G6</option>
                    <option value="G7">G7</option>
                    <option value="G8">G8</option>
                    <option value="G9">G9</option>
                    <option value="G10">G10</option>
                    <option value="G11">G11</option>
                    <option value="G12">G12</option>
                    <option value="G13">G13</option>
                    <option value="G14">G14</option>
                    <option value="G6G7">G6G7</option>
                    <option value="G8G9">G8G9</option>
                    <option value="G8G8">G8G8</option>
                    <option value="G7G10">G7G10</option>
                    <option value="G7G8">G7G8</option>
                    <option value="G6G6">G6G6</option>
                    <option value="G7G9">G7G9</option>
                    <option value="G6G9">G6G9</option>
                    <option value="G8G10">G8G10</option>
                    <option value="G8G11">G8G11</option>
                    <option value="G9G10">G9G10</option>
                    <option value="G9G11">G9G11</option>
                    <option value="G10G10">G10G10</option>
                    <option value="G10G11">G10G11</option>
                    <option value="G10G12">G10G12</option>
                    <option value="G11G12">G11G12</option>
                    <option value="G11G11">G11G11</option>
                    <option value="G12G12">G12G12</option>
                </select>
            </div>
            <div class="form-group">
                <label>Size:</label>
                <select id="a_size" name="a_size" required>
                    <option value="">Select Size</option>
                    <option value="-100 MM">-100 MM</option>
                    <option value="-250 MM">-250 MM</option>
                    <option value="MIX">MIX</option>
                    <option value="STEAM">STEAM</option>
                    <option value="SLACK">SLACK</option>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required onchange="setEndDate()">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required readonly>
            </div>
            <div class="form-group">
                <label>Quantity (tonnes):</label>
                <input type="number" name="quantity" required>
            </div>
            <div class="buttons">
                <button type="submit">Save</button>
                <button type="button" onclick="history.back()">Go Back</button>
            </div>
        </form>
    </div>
</body>
</html>