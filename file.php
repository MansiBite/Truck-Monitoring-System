<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        form {
            background: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        button {
            padding: 10px 15px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2>Upload Excel File</h2>
    <form action="sync_excel_to_oracle.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" accept=".xlsx, .xls" required>
        <br>
        <button type="submit" name="submit">Upload</button>
        <button type="button" onclick="goBack()">Go Back</button>
    </form>
    <script>
         function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>




