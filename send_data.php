<?php
include('../db_config.php'); // Database connection
require '../vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Ensure user is logged in and get their username
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Error: User not logged in.'); window.location.href='your_original_page.php';</script>";
    exit;
}

$logged_in_username = $_SESSION['username'];

// Get selected date range from POST request
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

if (!$start_date || !$end_date) {
    echo "<script>alert('Error: Start and End dates are required.'); window.location.href='../employee.php';</script>";
    exit;
}

// Fetch sender details from login table
$sql_sender = "SELECT EMAIL, NAME, AREA_NAME FROM login WHERE USERNAME = :username";
$stmt_sender = oci_parse($conn, $sql_sender);
oci_bind_by_name($stmt_sender, ":username", $logged_in_username);
oci_execute($stmt_sender);
$sender_data = oci_fetch_assoc($stmt_sender);

if (!$sender_data || empty($sender_data['EMAIL'])) {
    echo "<script>alert('Error: Sender email not found in the login table.'); window.location.href='../employee.php';</script>";
    exit;
}

$sender_email = $sender_data['EMAIL'];
$sender_name = $sender_data['NAME'];
$area_name = $sender_data['AREA_NAME'];

// Validate sender email
if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Error: Invalid sender email address.'); window.location.href='../employee.php';</script>";
    exit;
}

// Fetch customers who have truck usage within the selected date range
$sql_customers = "SELECT DISTINCT D.CUSTOMER_CODE, D.CUSTOMER_NAME, D.EMAIL 
                  FROM DATA D
                  JOIN Truck_Usage TU ON D.CUSTOMER_CODE = TU.CUSTOMER_CODE
                  WHERE TU.TRUCK_DATE BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') 
                  AND TO_DATE(:end_date, 'YYYY-MM-DD')";

$stmt_customers = oci_parse($conn, $sql_customers);
oci_bind_by_name($stmt_customers, ":start_date", $start_date);
oci_bind_by_name($stmt_customers, ":end_date", $end_date);
oci_execute($stmt_customers);

$customers = [];
while ($row = oci_fetch_assoc($stmt_customers)) {
    if (!filter_var($row['EMAIL'], FILTER_VALIDATE_EMAIL)) {
        continue; // Skip invalid emails
    }
    $customers[$row['CUSTOMER_CODE']]['name'] = $row['CUSTOMER_NAME'];
    $customers[$row['CUSTOMER_CODE']]['emails'][] = $row['EMAIL'];
}

if (empty($customers)) {
    echo "<script>alert('No customers found for the selected date range.'); window.location.href='your_original_page.php';</script>";
    exit;
}

// Fetch relevant L_P table data for the selected customers
$customer_codes = implode(",", array_keys($customers));
$sql_lp = "SELECT * FROM L_P WHERE CUSTOMER_CODE IN ($customer_codes)";
$stmt_lp = oci_parse($conn, $sql_lp);
oci_execute($stmt_lp);
$lp_data = [];
while ($row = oci_fetch_assoc($stmt_lp)) {
    $lp_data[$row['CUSTOMER_CODE']][] = $row;
}

// Fetch relevant Truck Usage data for the selected customers and date range
$sql_truck = "SELECT CUSTOMER_CODE, SALES_ORDER_NO, TRUCK_DATE, TOTAL_TRUCK 
              FROM Truck_Usage 
              WHERE CUSTOMER_CODE IN ($customer_codes)
              AND TRUCK_DATE BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') 
              AND TO_DATE(:end_date, 'YYYY-MM-DD')";

$stmt_truck = oci_parse($conn, $sql_truck);
oci_bind_by_name($stmt_truck, ":start_date", $start_date);
oci_bind_by_name($stmt_truck, ":end_date", $end_date);
oci_execute($stmt_truck);
$truck_data = [];
while ($row = oci_fetch_assoc($stmt_truck)) {
    $truck_data[$row['CUSTOMER_CODE']][] = $row;
}

// Initialize PHPMailer
$mail = new PHPMailer(true);
$emails_sent = 0;

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'thakrepranay03@gmail.com';
    $mail->Password = 'snky mdop bedg zzsv';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom($sender_email, "WCL Sales Manager");

    foreach ($customers as $customer_code => $customer) {
        $email_body = "<p>Dear {$customer['name']},</p>";
        $email_body .= "<p>Your Road Sale Weekly Truck Loading Schedule for the period from <strong>$start_date</strong> to <strong>$end_date</strong>.</p>";

        if (!empty($lp_data[$customer_code])) {
            $email_body .= "<h3>Customer and Sales Order Details:</h3><table border='1' cellspacing='0' cellpadding='5'>";
            $email_body .= "<tr><th>Area Name</th><th>Sector</th><th>Mine Code</th><th>Mine Name</th><th>Grade</th><th>Size</th><th>Scheme</th><th>Customer Code</th><th>Customer Name</th><th>Sales Order No</th><th>Valid From</th><th>Valid To</th><th>SO Qty</th><th>Lifted Qty</th><th>Balance Qty</th></tr>";

            foreach ($lp_data[$customer_code] as $row) {
                $email_body .= "<tr><td>{$row['AREA_NAME']}</td><td>{$row['SECTOR']}</td><td>{$row['MINE_CODE']}</td><td>{$row['MINE_NAME']}</td><td>{$row['GRADE']}</td><td>{$row['SIZE_C']}</td><td>{$row['SCHEME']}</td><td>{$row['CUSTOMER_CODE']}</td><td>{$row['CUSTOMER_NAME']}</td><td>{$row['SALES_ORDER_NO']}</td><td>{$row['VALID_FROM']}</td><td>{$row['VALID_TO']}</td><td>{$row['SO_QTY']}</td><td>{$row['LIFTED_QTY']}</td><td>{$row['BALANCE_QTY']}</td></tr>";
            }
            $email_body .= "</table>";
        }

        if (!empty($truck_data[$customer_code])) {
            $email_body .= "<h3>Truck Allotment against Sales Order:</h3><table border='1' cellspacing='0' cellpadding='5'>";
            $email_body .= "<tr><th>Customer Code</th><th>Sales Order No</th><th>Truck Date</th><th>Total Truck</th></tr>";

            foreach ($truck_data[$customer_code] as $row) {
                $email_body .= "<tr><td>{$row['CUSTOMER_CODE']}</td><td>{$row['SALES_ORDER_NO']}</td><td>{$row['TRUCK_DATE']}</td><td>{$row['TOTAL_TRUCK']}</td></tr>";
            }
            $email_body .= "</table>";
        }

        $email_body .= "<p>Request you to deploy trucks as per the given schedule to lift your coal timely.Request you to deploy trucks as per the given Schedule to lift your coal timely. In case of failure to lift coal as per the schedule, WCL shall not be responsible and allocation of additional truck loading schedule against the lapsed program is at the sole discretion of WCL and WCL’s decision shall be final and binding.</p>";
        $email_body .= "<p>With Regards,<br>Office of the Area Sales Manager<br>WCL, $sender_name - $area_name.</p>";


        foreach ($customer['emails'] as $email) {
            $mail->clearAddresses();
            $mail->addAddress($email, $customer['name']);
            $mail->Subject = "Road Sale Weekly Truck Loading Schedule ($start_date - $end_date)";
            $mail->isHTML(true);
            $mail->Body = $email_body;
            if ($mail->send()) {
                $emails_sent++;
            }
        }
    }

    echo "<script>alert('All emails sent successfully.'); window.location.href='../employee.php';</script>";
} catch (Exception $e) {
    echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "');</script>";
}
?>