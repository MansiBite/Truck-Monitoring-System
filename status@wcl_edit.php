<?php
include('../db_config.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fetch_data'])) {
    $truck_no = $_POST['truck_no'];
    
    // Fetch truck data
    $sql = "SELECT * FROM WCL_Check_Post WHERE truck_no = :truck_no";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':truck_no', $truck_no);
    oci_execute($stmt);
    $data = oci_fetch_assoc($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_data'])) {
    $truck_no = $_POST['truck_no'];
    $sales_order_no = $_POST['sales_order_no'];
    $date_in = date("d-M-Y", strtotime($_POST['date_in']));
    $time_in = date("H:i:s", strtotime($_POST['time_in'] . " " . $_POST['am_pm_in']));
    $date_out = !empty($_POST['date_out']) ? date("d-M-Y", strtotime($_POST['date_out'])) : null;
    $time_out = !empty($_POST['time_out']) ? date("H:i:s", strtotime($_POST['time_out'] . " " . $_POST['am_pm_out'])) : null;
    
    // Update Query
    $sql = "UPDATE WCL_Check_Post SET sales_order_no = :sales_order_no, date_in = TO_DATE(:date_in, 'DD-MON-YYYY'), time_in = :time_in,
            date_out = TO_DATE(:date_out, 'DD-MON-YYYY'), time_out = :time_out WHERE truck_no = :truck_no";
    $stmt = oci_parse($conn, $sql);
    
    oci_bind_by_name($stmt, ':truck_no', $truck_no);
    oci_bind_by_name($stmt, ':sales_order_no', $sales_order_no);
    oci_bind_by_name($stmt, ':date_in', $date_in);
    oci_bind_by_name($stmt, ':time_in', $time_in);
    oci_bind_by_name($stmt, ':date_out', $date_out);
    oci_bind_by_name($stmt, ':time_out', $time_out);
    
    if (oci_execute($stmt)) {
        echo "<script>alert('Data updated successfully!'); window.location.href='../employee.php';</script>";
    } else {
        echo "<script>alert('Error updating data!');</script>";
    }
}
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Truck Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            margin-top: 10px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #218838;
        }
        .back-button {
            background-color: #dc3545;
        }
        .back-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h2>Edit Truck Details</h2>
    <form method="POST">
        <label for="truck_no">Enter Truck No:</label>
        <input type="text" name="truck_no" required>
        <button type="submit" name="fetch_data">Fetch Data</button>
    </form>
    
    <?php if (!empty($data)) { ?>
    <form method="POST">
        <input type="hidden" name="truck_no" value="<?php echo $data['TRUCK_NO']; ?>">
        <label>Sales Order No:</label>
        <input type="text" name="sales_order_no" value="<?php echo $data['SALES_ORDER_NO']; ?>" required><br>
        <label>Date In:</label>
        <input type="date" name="date_in" value="<?php echo date('Y-m-d', strtotime($data['DATE_IN'])); ?>" required><br>
        <label>Time In:</label>
        <input type="text" name="time_in" value="<?php echo date('h:i', strtotime($data['TIME_IN'])); ?>" required>
        <select name="am_pm_in">
            <option value="AM" <?php echo (date('A', strtotime($data['TIME_IN'])) == 'AM') ? 'selected' : ''; ?>>AM</option>
            <option value="PM" <?php echo (date('A', strtotime($data['TIME_IN'])) == 'PM') ? 'selected' : ''; ?>>PM</option>
        </select><br>
        <label>Date Out:</label>
        <input type="date" name="date_out" value="<?php echo !empty($data['DATE_OUT']) ? date('Y-m-d', strtotime($data['DATE_OUT'])) : ''; ?>"><br>
        <label>Time Out:</label>
        <input type="text" name="time_out" value="<?php echo !empty($data['TIME_OUT']) ? date('h:i', strtotime($data['TIME_OUT'])) : ''; ?>">
        <select name="am_pm_out">
            <option value="AM" <?php echo (!empty($data['TIME_OUT']) && date('A', strtotime($data['TIME_OUT'])) == 'AM') ? 'selected' : ''; ?>>AM</option>
            <option value="PM" <?php echo (!empty($data['TIME_OUT']) && date('A', strtotime($data['TIME_OUT'])) == 'PM') ? 'selected' : ''; ?>>PM</option>
        </select><br>
        <button type="submit" name="update_data">Save Changes</button>
        <button type="button" class="back-button" onclick="window.location.href='../employee.php';">Back</button>
    </form>
    <?php } ?>
</body>
</html>