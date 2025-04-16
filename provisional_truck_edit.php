<?php
include('../db_config.php'); // Database connection

// Initialize variables
$customer_code = $sales_order_no = "";
$truck_data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["fetch_data"])) {
    // Fetch data based on customer_code and sales_order_no
    $customer_code = $_POST['customer_code'];
    $sales_order_no = $_POST['sales_order_no'];

    $sql = "SELECT * FROM Provisional_Truck WHERE customer_code = :customer_code AND sales_order_no = :sales_order_no";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':customer_code', $customer_code);
    oci_bind_by_name($stmt, ':sales_order_no', $sales_order_no);

    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $truck_data[] = $row;
    }

    oci_free_statement($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_data"])) {
    // Update data in the database
    try {
        $customer_code = $_POST['customer_code'];
        $sales_order_no = $_POST['sales_order_no'];

        for ($i = 0; $i < count($_POST["schedule_date"]); $i++) {
            $schedule_date = $_POST["schedule_date"][$i];
            $truck_no = $_POST["truck_no"][$i];

            // Convert date format for Oracle
            $schedule_date = date('d-M-Y', strtotime($schedule_date));

            $sql = "UPDATE Provisional_Truck 
                    SET truck_no = :truck_no
                    WHERE customer_code = :customer_code AND sales_order_no = :sales_order_no AND TO_CHAR(schedule_date, 'DD-MON-YYYY') = :schedule_date";

            $stmt = oci_parse($conn, $sql);

            oci_bind_by_name($stmt, ':customer_code', $customer_code);
            oci_bind_by_name($stmt, ':sales_order_no', $sales_order_no);
            oci_bind_by_name($stmt, ':schedule_date', $schedule_date);
            oci_bind_by_name($stmt, ':truck_no', $truck_no);

            $result = oci_execute($stmt);

            if (!$result) {
                $error = oci_error($stmt);
                throw new Exception("Database Error: " . $error['message']);
            }
        }

        oci_free_statement($stmt);
        oci_close($conn);

        echo "<script>alert('Data updated successfully!'); window.location.href='edit_provisional_truck.php';</script>";

    } catch (Exception $e) {
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Provisional Truck</title>
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
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
}

/* Page Title */
h2 {
    color: #333;
    margin-bottom: 20px;
}

/* Form Container */
form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin-bottom: 20px;
}

/* Labels */
label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
    color: #444;
}

/* Input Fields */
input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

/* Buttons */
button {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    font-size: 16px;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background: #0056b3;
}

/* Table Styling */
table {
    width: 100%;
    max-width: 800px;
    border-collapse: collapse;
    background: white;
    margin-top: 10px;
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
    font-size: 16px;
}

/* Readonly Input */
td input[readonly] {
    background: #f9f9f9;
    border: none;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    form {
        width: 90%;
    }

    table {
        font-size: 14px;
        overflow-x: auto;
    }

    th, td {
        padding: 8px;
    }
}


    </style>


</head>

<body>
    <h2>Edit Provisional Truck Details</h2>

    <form method="POST">
        <label for="customer_code">Customer Code:</label>
        <input type="text" name="customer_code" value="<?php echo $customer_code; ?>" required>

        <label for="sales_order_no">Sales Order No:</label>
        <input type="text" name="sales_order_no" value="<?php echo $sales_order_no; ?>" required>

        <button type="submit" name="fetch_data">Fetch Data</button>
    </form>

    <?php if (!empty($truck_data)): ?>
        <form method="POST">
            <input type="hidden" name="customer_code" value="<?php echo $customer_code; ?>">
            <input type="hidden" name="sales_order_no" value="<?php echo $sales_order_no; ?>">

            <table border="1">
                <tr>
                    <th>Schedule Date</th>
                    <th>Truck No</th>
                </tr>

                <?php foreach ($truck_data as $index => $data): ?>
                    <tr>
                        <td>
                            <input type="text" name="schedule_date[]"
                                value="<?php echo date('Y-m-d', strtotime($data['SCHEDULE_DATE'])); ?>" readonly>
                        </td>
                        <td>
                            <input type="text" name="truck_no[]" value="<?php echo htmlspecialchars($data['TRUCK_NO']); ?>"
                                required>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <button type="submit" name="update_data">Update</button>
        </form>
    <?php endif; ?>

</body>

</html>