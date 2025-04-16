<?php
include('../db_config.php'); // Database connection

// Fetch all data
$sql = "SELECT * FROM Provisional_Truck ORDER BY schedule_date DESC";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

$truck_data = [];
while ($row = oci_fetch_assoc($stmt)) {
    $truck_data[] = $row;
}
oci_free_statement($stmt);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provisional Truck Data</title>
    <style>
        /* General Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: 15px;
            text-align: center;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            margin-right: 5px;
        }
        button {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }

        /* Table Styling */
        table {
            width: 100%;
            max-width: 1000px;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            input[type="text"] {
                width: 80%;
            }
            table {
                font-size: 14px;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    <h2>Provisional Truck Data</h2>

    <!-- Search Bar -->
    <div class="search-container">
        <input type="text" id="search" placeholder="Search by Customer Code or Sales Order No..." onkeyup="filterTable()">
    </div>

    <!-- Data Table -->
    <table id="truckTable">
        <thead>
            <tr>
                <th>Customer Code</th>
                <th>Sales Order No</th>
                <th>Schedule Date</th>
                <th>Truck No</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($truck_data as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['CUSTOMER_CODE']); ?></td>
                    <td><?php echo htmlspecialchars($data['SALES_ORDER_NO']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($data['SCHEDULE_DATE'])); ?></td>
                    <td><?php echo htmlspecialchars($data['TRUCK_NO']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- JavaScript for Search Filter -->
    <script>
        function filterTable() {
            let input = document.getElementById("search").value.toUpperCase();
            let table = document.getElementById("truckTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td1 = tr[i].getElementsByTagName("td")[0]; // Customer Code
                let td2 = tr[i].getElementsByTagName("td")[1]; // Sales Order No

                if (td1 && td2) {
                    let txtValue1 = td1.textContent || td1.innerText;
                    let txtValue2 = td2.textContent || td2.innerText;

                    if (txtValue1.toUpperCase().includes(input) || txtValue2.toUpperCase().includes(input)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>
</html>
