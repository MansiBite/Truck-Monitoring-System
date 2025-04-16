<?php
include('../db_config.php'); // Database connection
include('../navbar.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Fetch the area name based on logged-in username
$sql_area = "SELECT AREA_NAME FROM login WHERE USERNAME = :username";
$stid_area = oci_parse($conn, $sql_area);
oci_bind_by_name($stid_area, ":username", $username);
oci_execute($stid_area);
$row_area = oci_fetch_assoc($stid_area);
$area_name = $row_area['AREA_NAME'] ?? '';

// Fetch the mine names based on the area
$sql_mines = "SELECT MINE_NAME, MINE_CODE FROM MINE_TABLE WHERE AREA_NAME = :area_name";
$stid_mines = oci_parse($conn, $sql_mines);
oci_bind_by_name($stid_mines, ":area_name", $area_name);
oci_execute($stid_mines);

$mines = [];
while ($row_mine = oci_fetch_assoc($stid_mines)) {
    $mines[] = $row_mine;
}

// Auto-fill mine code based on mine name selection
$mine_code = '';
if (isset($_POST['MINE_NAME']) && !empty($_POST['MINE_NAME'])) {
    $mine_name = $_POST['MINE_NAME'];
    $sql_mine_code = "SELECT MINE_CODE FROM MINE_TABLE WHERE MINE_NAME = :mine_name AND AREA_NAME = :area_name";
    $stid_mine_code = oci_parse($conn, $sql_mine_code);
    oci_bind_by_name($stid_mine_code, ":mine_name", $mine_name);
    oci_bind_by_name($stid_mine_code, ":area_name", $area_name);
    oci_execute($stid_mine_code);
    $row_mine_code = oci_fetch_assoc($stid_mine_code);
    $mine_code = $row_mine_code['MINE_CODE'] ?? '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Program</title>
    <style>
       /* General Styles */
       body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        h2 {
            text-align: center;
            color: #343a40;
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
            margin-right: 17px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background-color: #f1f3f5;
            color: #495057;
            margin-bottom: 10px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #007BFF;
            background-color: #ffffff;
        }

        /* Flexbox Layout for Grouped Labels */
        .label-group {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .label-group label {
            flex: 1 1 45%;
            min-width: 200px;
        }

        .full-width {
            width: 100%;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }

        td input {
            width: 80%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background-color: #f1f3f5;
        }

        td input:focus {
            border-color: #007BFF;
        }

        /* Buttons Styling */
        .buttons {
            display: flex;
            justify-content: space-evenly;

        }

        button {
            padding: 14px 25px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        button.go-back {
            background-color: #dc3545;
            color: white;

        }

        button.go-back:hover {
            background-color: #c82333;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .label-group {
                flex-direction: column;
            }

            .label-group label {
                flex: 1 1 100%;
            }
        }
    </style>
    <script>
      function validateDates() {
            let today = new Date().toISOString().split('T')[0];
            let validFrom = document.getElementById("valid_from").value;
            let validTo = document.getElementById("valid_to");
            let dateRangeStart = document.getElementById("date_range_start");
            let dateRangeEnd = document.getElementById("date_range_end");

            if (validFrom < today) {
                alert("Please select a valid date from today onwards!");
                document.getElementById("valid_from").value = "";
                return;
            }

            let toDate = new Date(validFrom);
            toDate.setDate(toDate.getDate() + 45);
            validTo.value = toDate.toISOString().split('T')[0];

            dateRangeStart.setAttribute("min", validFrom);
            dateRangeStart.setAttribute("max", validTo.value);
            dateRangeEnd.setAttribute("min", validFrom);
            dateRangeEnd.setAttribute("max", validTo.value);
        }

        function validateDateRange() {
            let validFrom = document.getElementById("valid_from").value;
            let validTo = document.getElementById("valid_to").value;
            let dateRangeStart = document.getElementById("date_range_start").value;
            let dateRangeEnd = document.getElementById("date_range_end");

            if (dateRangeStart < validFrom || new Date(dateRangeStart) > new Date(validTo)) {
                alert("Start date should be within the 45-day period!");
                document.getElementById("date_range_start").value = "";
                return;
            }

            let endDate = new Date(dateRangeStart);
            endDate.setDate(endDate.getDate() + 6);
            dateRangeEnd.value = endDate.toISOString().split('T')[0];

            if (dateRangeEnd.value > validTo) {
                alert("7-day period should be within the 45-day period!");
                document.getElementById("date_range_start").value = "";
                document.getElementById("date_range_end").value = "";
                return;
            }

            generateTruckInputs(dateRangeStart);
        }

        function generateTruckInputs(startDate) {
            let container = document.getElementById("truck_input_container");
            container.innerHTML = "";

            let table = document.createElement("table");
            table.border = "1";
            table.style.width = "100%";
            table.style.marginTop = "10px";
            let thead = table.createTHead();
            let tbody = table.createTBody();
            let row = thead.insertRow();
            row.innerHTML = "<th>Date</th><th>No. of Trucks</th>";

            for (let i = 0; i < 7; i++) {
                let date = new Date(startDate);
                date.setDate(date.getDate() + i);
                let formattedDate = date.toISOString().split('T')[0];

                let tr = tbody.insertRow();
                let td1 = tr.insertCell(0);
                let td2 = tr.insertCell(1);

                td1.innerHTML = `<input type='text' name='truck_dates[]' value='${formattedDate}' readonly>`;

                let inputField = document.createElement("input");
                inputField.type = "number";
                inputField.name = "trucks_per_day[]";
                inputField.min = "0";
                inputField.value = "0";
                inputField.setAttribute("oninput", "calculateTotalTrucks()");

                td2.appendChild(inputField);
            }

            let totalRow = tbody.insertRow();
            totalRow.innerHTML = `<td><b>Total Trucks:</b></td><td><input type="text" id="total_trucks" name="total_trucks" value="0" readonly></td>`;

            container.appendChild(table);
        }
        

        function calculateTotalTrucks() {
            let total = 0;
            document.querySelectorAll("input[name='trucks_per_day[]']").forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById("total_trucks").value = total;
        }

        function calculateBalanceCoalQty() {
            let totalQty = document.getElementsByName("TOTAL_QTY")[0].value;
            let liftedQty = document.getElementsByName("LIFTED_QTY")[0].value;

            let balanceCoalQty = totalQty - liftedQty;
            document.getElementsByName("BALANCE_QTY")[0].value = balanceCoalQty;
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Loading Program</h2>
        <form method="POST">
            <label>AREA NAME: 
                <input type="text" name="AREA_NAME" value="<?php echo htmlspecialchars($area_name); ?>" required readonly>
            </label>
            
            <label>MINE NAME: 
    <select name="MINE_NAME" required onchange="this.form.submit()">
        <option value="">Select Mine</option>
        <?php 
        // Check if the 'MINE_NAME' is set in POST to avoid warning
        foreach ($mines as $mine) : 
        ?>
            <option value="<?php echo $mine['MINE_NAME']; ?>" 
                    <?php echo (isset($_POST['MINE_NAME']) && $_POST['MINE_NAME'] == $mine['MINE_NAME']) ? 'selected' : ''; ?>>
                <?php echo $mine['MINE_NAME']; ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>


            <label>MINE CODE: 
                <input type="text" name="MINE_CODE" value="<?php echo htmlspecialchars($mine_code); ?>" required readonly>
            </label>

            <label>SECTOR:
                <select name="SECTOR" required>
                    <option value="SECTOR">Select Sector</option>
                    <option value="Power">Power</option>
                    <option value="Non-Power">Non-Power</option>
                </select>
            </label>

            <label>GRADE:
                <select name="GRADE" required>
                    <option value="GRADE">Grade</option>
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
            </label>
            <label>SIZE:
                <select name="SIZE" required>
                    <option value="SIZE">Size</option>
                    <option value="-100 MM">-100 MM</option>
                    <option value="-250 MM">-250 MM</option>
                    <option value="MIX">MIX</option>
                    <option value="STEAM">STEAM</option>
                    <option value="SLACK">SLACK</option>
                </select>
            </label>

            <label>SCHEME:
                <select name="SCHEME" required>
                    <option value="SCHEME">Scheme</option>
                    <option value="FSA Linkage Auction (NRS)">FSA Linkage Auction (NRS)</option>
                    <option value="FSA Linkage Auction (SHAKTI)">FSA Linkage Auction (SHAKTI)</option>
                    <option value="Bridge Linkage">Bridge Linkage</option>
                    <option value="Fuel Supply Agreement">Fuel Supply Agreement</option>
                    <option value="Spot Auction">Spot Auction</option>
                </select>
            </label>
            <label>CUSTOMER CODE: <input type="text" name="CUSTOMER_CODE" required></label>
            <label>CUSTOMER NAME: <input type="text" name="CUSTOMER_NAME" required></label>
            <label>SALES ORDER NO: <input type="text" name="SALES_ORDER_NO" required></label>

            <label>VALID_FROM: <input type="date" id="valid_from" name="VALID_FROM" required
                    onchange="validateDates()"></label>
            <label>VALID_TO: <input type="date" id="valid_to" name="VALID_TO" readonly></label>

            <label for="TOTAL_QTY">TOTAL COAL QTY:</label>
            <input type="number" name="TOTAL_QTY" oninput="calculateBalanceCoalQty()" required>

            <label for="LIFTED_QTY">LIFTED COAL QTY:</label>
            <input type="number" name="LIFTED_QTY" oninput="calculateBalanceCoalQty()" required>

            <label for="BALANCE_QTY">BALANCE COAL QTY:</label>
            <input type="number" name="BALANCE_QTY" readonly>
            <!-- <label>TOTAL_QTY: <input type="number" name="TOTAL_QTY" required></label>
            <label>LIFTED_QTY: <input type="number" name="LIFTED_QTY" required></label>
            <label>BALANCE_QTY: <input type="number" name="BALANCE_QTY" required></label> -->

            <div class="label-group">
                <label>7-Day Date Range (Start): <input type="date" id="date_range_start" name="DATE_RANGE_START"
                        required onchange="validateDateRange()"></label>
                <label>7-Day Date Range (End): <input type="date" id="date_range_end" name="DATE_RANGE_END"
                        readonly></label>
            </div>

            <div id="truck_input_container" class="full-width"></div>

            <div class="buttons">
                <button type="submit">Submit</button>
                <button type="button" class="go-back" onclick="history.back()">Go Back</button>
            </div>
        </form>
    </div>
</body>

</html>
