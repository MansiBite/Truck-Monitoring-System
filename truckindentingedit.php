<?php
include('../navbar.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Truck Indenting Requirement</title>
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

        .fetch-container {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Edit Truck Indenting Requirement</h1>
        <form method="POST" action="">
            <div class="fetch-container">
                <label for="customer_code">Customer Code</label>
                <input type="number" id="customer_code" name="customer_code" placeholder="Enter Customer Code" required>
                <label for="sales_order_no">Sales Order No</label>
                <input type="number" id="sales_order_no" name="sales_order_no" placeholder="Enter Sales Order No" required>
                <button type="submit" name="fetch_data">Fetch Data</button>
                <button type="button" onclick="window.history.back()">Go Back</button>
            </div>
        </form>

        <?php
        include('../db_config.php');

        // Fix for $conn_str not being defined:
        $conn_str = "localhost/XE";  // Adjust your connection string here
        $conn = oci_connect($username, $password, $conn_str);

        if (!$conn) {
            $e = oci_error();
            die("Database connection failed: " . htmlentities($e['message'], ENT_QUOTES));
        }

        if (isset($_POST['fetch_data'])) {
            $customer_code = $_POST['customer_code'];
            $sales_order_no = $_POST['sales_order_no'];

            // Fetch existing data from the database based on customer code and sales order number
            $sql = "SELECT * FROM TRUCK_INDENTING WHERE CUSTOMER_CODE = :customer_code AND SALES_ORDER_NO = :sales_order_no";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ":customer_code", $customer_code);
            oci_bind_by_name($stmt, ":sales_order_no", $sales_order_no);
            oci_execute($stmt);

            $data = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $data[] = $row;
            }

            oci_free_statement($stmt);
        }

        if (isset($_POST['save_data'])) {
            // Update customer code and sales order no
            $customer_code = $_POST['customer_code'];
            $sales_order_no = $_POST['sales_order_no'];
            $date_from = date('Y-m-d', strtotime($_POST['date_from']));
            $date_to = date('Y-m-d', strtotime($_POST['date_to']));

            // Delete existing data before inserting updated data
            $delete_sql = "DELETE FROM TRUCK_INDENTING WHERE CUSTOMER_CODE = :customer_code AND SALES_ORDER_NO = :sales_order_no";
            $delete_stmt = oci_parse($conn, $delete_sql);
            oci_bind_by_name($delete_stmt, ":customer_code", $customer_code);
            oci_bind_by_name($delete_stmt, ":sales_order_no", $sales_order_no);
            oci_execute($delete_stmt);

            // Insert updated data
            for ($i = 0; $i < 7; $i++) {
                // Get the truck date based on the start date
                $truck_date = date('Y-m-d', strtotime($date_from . " +$i days"));
                $no_of_trucks = $_POST["trucks_$i"];

                $sql = "INSERT INTO TRUCK_INDENTING (CUSTOMER_CODE, SALES_ORDER_NO, DATE_FROM, DATE_TO, TRUCK_DATE, NO_OF_TRUCKS) 
                        VALUES (:customer_code, :sales_order_no, TO_DATE(:date_from, 'YYYY-MM-DD'), TO_DATE(:date_to, 'YYYY-MM-DD'), TO_DATE(:truck_date, 'YYYY-MM-DD'), :no_of_trucks)";

                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ":customer_code", $customer_code);
                oci_bind_by_name($stmt, ":sales_order_no", $sales_order_no);
                oci_bind_by_name($stmt, ":date_from", $date_from);
                oci_bind_by_name($stmt, ":date_to", $date_to);
                oci_bind_by_name($stmt, ":truck_date", $truck_date);
                oci_bind_by_name($stmt, ":no_of_trucks", $no_of_trucks);

                $result = oci_execute($stmt);
                if (!$result) {
                    $e = oci_error($stmt);
                    echo "Error: " . htmlentities($e['message'], ENT_QUOTES);
                    exit();
                }
                oci_free_statement($stmt);
            }

            echo "<script>alert('Data updated successfully!'); window.location.href='../customer.php';</script>";
        }

        oci_close($conn);
        ?>

        <?php if (isset($data)) : ?>
        <form method="POST" action="">
            <label for="customer_code">Customer Code</label>
            <input type="number" id="customer_code" name="customer_code" value="<?= $data[0]['CUSTOMER_CODE'] ?>" required>
            <label for="sales_order_no">Sales Order No</label>
            <input type="number" id="sales_order_no" name="sales_order_no" value="<?= $data[0]['SALES_ORDER_NO'] ?>" required>

            <label for="date_from">Date From</label>
            <input type="date" id="date_from" name="date_from" value="<?= $data[0]['DATE_FROM'] ?>" required>
            <label for="date_to">Date To</label>
            <input type="date" id="date_to" name="date_to" value="<?= $data[0]['DATE_TO'] ?>" required>

            <div id="date_fields" class="dynamic-fields">
                <h3>Schedule Details:</h3>
                <?php
                for ($i = 0; $i < 7; $i++) {
                    $currentDay = $data[$i]['TRUCK_DATE'];
                    $formattedDate = date('Y-m-d', strtotime($currentDay));
                    $no_of_trucks = $data[$i]['NO_OF_TRUCKS'];
                    echo "
                        <div class='date-fields'>
                            <input type='text' id='day_$i' name='day_$i' value='$formattedDate' readonly>
                            <input type='number' id='trucks_$i' name='trucks_$i' value='$no_of_trucks' min='0' required>
                        </div>
                    ";
                }
                ?>
            </div>

            <div class="button-container">
                <input type="submit" name="save_data" value="Save Changes">
                <button type="button" onclick="window.history.back()">Go Back</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
