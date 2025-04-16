<!DOCTYPE html>
<html>
<head>
    <title>Where is My Truck</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Where is My Truck</h1>
    <table>
        <thead>
            <tr>
                <th>Sales Order No.</th>
                <th>Truck No.</th>
                <th>In Date and Time @ Security Gate</th>
                <th>In Date and Time @ Weighbridge</th>
                <th>Out Date and Time from Weighbridge</th>
                <th>Out Date and Time from Security Gate</th>
                <th>Status()</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be inserted here dynamically -->
        </tbody>
    </table>
</body>
</html>
