<?php
include('../navbar.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Truck Indenting Requirement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .form-container {
            width: 50%;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="submit"],
        button {
            width: calc(100% - 20px);
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"],
        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
            width: auto;
            display: inline-block;
            margin-right: 10px;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #0056b3;
        }

        .date-fields {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .date-fields input[type="text"] {
            width: 40%;
        }

        .date-fields input[type="number"] {
            width: 55%;
        }

        .form-container .dynamic-fields {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .form-container .dynamic-fields h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .total-trucks {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            color: #333;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Truck Indenting Requirement</h1>
        <form method="POST" action="../savephp/save_truck_indent.php">

            <label for="customer_code">Customer Code</label>
            <input type="number" id="customer_code" name="customer_code" placeholder="Enter Customer Code" required>

            <label for="sales_order_no">Sales order No</label>
            <input type="number" id="sales_order_no" name="sales_order_no" placeholder="Enter Sales_order_No" required>

            <label for="date_from">Date From</label>
            <div class="date-fields">
                <input type="date" id="date_from" name="date_from" required onchange="updateDates()">
                <input type="text" id="date_to" name="date_to" placeholder="Valid until" readonly>
            </div>

            <div id="date_fields" class="dynamic-fields">
                <h3>Schedule Details:</h3>
            </div>

            <div id="total_trucks_row" class="total-trucks" style="display: none;">Total Trucks: <span id="total_trucks">0</span></div>

            <div class="button-container">
                <input type="submit" value="Submit">
                <button type="button" onclick="goBack()">Go Back</button>
            </div>
        </form>
    </div>

    <script>
        function updateDates() {
            const dateFromInput = document.getElementById('date_from');
            const dateToInput = document.getElementById('date_to');
            const dateFieldsContainer = document.getElementById('date_fields');
            const totalTrucksRow = document.getElementById('total_trucks_row');

            if (dateFromInput.value) {
                const startDate = new Date(dateFromInput.value);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 7);

                dateToInput.value = formatDate(endDate);
                dateFieldsContainer.innerHTML = '';

                for (let i = 0; i < 7; i++) {
                    const currentDay = new Date(startDate);
                    currentDay.setDate(startDate.getDate() + i);

                    const dateFieldHTML = `
                    <div class="date-fields">
                        <input type="text" id="day_${i}" name="day_${i}" value="${formatDate(currentDay)}" readonly>
                        <input type="number" id="trucks_${i}" name="trucks_${i}" placeholder="No. of trucks" min="0" required oninput="calculateTotalTrucks()">
                    </div>
                `;
                    dateFieldsContainer.insertAdjacentHTML('beforeend', dateFieldHTML);
                }
                totalTrucksRow.style.display = 'none';
            }
        }

        function formatDate(date) {
            return new Intl.DateTimeFormat('en-US', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
        }

        function calculateTotalTrucks() {
            let total = 0;
            let allFilled = true;
            for (let i = 0; i < 7; i++) {
                const truckInput = document.getElementById(`trucks_${i}`);
                if (truckInput) {
                    const value = parseInt(truckInput.value) || 0;
                    total += value;
                    if (!truckInput.value) {
                        allFilled = false;
                    }
                }
            }
            document.getElementById('total_trucks').textContent = total;
            document.getElementById('total_trucks_row').style.display = allFilled ? 'block' : 'none';
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
