<?php
// Database connection details
$host = "localhost"; // Replace with your Oracle DB hostname or IP
$port = "1521";      // Default Oracle port
$service_name = "XE"; // Change to your new database service name (data)
$username = "system";
$password = "system";

// Connection string
$dsn = "(DESCRIPTION =
          (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))
          (CONNECT_DATA =
            (SERVER = DEDICATED)
            (SERVICE_NAME = $service_name)
          )
        )";

try {
    $conn = oci_connect($username, $password, $dsn);
    if (!$conn) {
        $e = oci_error();
        throw new Exception($e['message']);
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
