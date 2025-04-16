<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status @ WCL Check Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 20px;
        }
        h2 {
            background-color: #0073e6;
            color: white;
            padding: 15px;
            border-radius: 5px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #0073e6;
            color: white;
        }
        input, select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 90%;
        }
        .add-button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #0073e6;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
    <script>
        function addRow() {
            const table = document.getElementById("inputTable");
            const row = table.insertRow();
            row.innerHTML = `
                <td><input type="text" name="truck_no[]" required></td>
                <td><input type="text" name="sales_order_no[]" required></td>
                <td><input type="date" name="date_in[]" required></td>
                <td>
                    <input type="time" name="time_in[]" style="width: 60%;" required>
                    <select name="am_pm_in[]">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </td>
                <td><input type="date" name="date_out[]"></td>
                <td>
                    <input type="time" name="time_out[]" style="width: 60%;">
                    <select name="am_pm_out[]">
                        <option value="AM">AM</option>
                        <option value="PM">PM</option>
                    </select>
                </td>`;
        }

        function validateForm() {
            const inputs = document.querySelectorAll("input[required]");
            for (let input of inputs) {
                if (!input.value.trim()) {
                    alert("Please fill all required fields.");
                    return false;
                }
            }
            return true;
        }
    </script>
</head>
<body>
    <h2>Status @ WCL Check Post</h2>
    
    <form action="../savephp/save_status@wcl.php" method="POST" onsubmit="return validateForm()">
        <table id="inputTable">
            <tr>
                <th>Truck No.</th>
                <th>Sales Order No.</th>
                <th>Date In</th>
                <th>In Time</th>
                <th>Date Out</th>
                <th>Out Time</th>
            </tr>
            <tr>
                <td><input type="text" name="truck_no[]" required></td>
                <td><input type="text" name="sales_order_no[]" required></td>
                <td><input type="date" name="date_in[]" required></td>
                <td>
                    <input type="time" name="time_in[]" style="width: 60%;" required>
                    
                </td>
                <td><input type="date" name="date_out[]"></td>
                <td>
                    <input type="time" name="time_out[]" style="width: 60%;">
                   
                </td>
            </tr>
        </table>

        <button type="button" class="add-button" onclick="addRow()">Add More</button>
        <button type="submit" class="add-button">Submit</button>
        <button type="button" class="add-button" onclick="window.history.back();">Go Back</button>
    </form>
</body>
</html>
