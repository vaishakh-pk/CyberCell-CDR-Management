<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Increase PHP execution time and memory limit
set_time_limit(0);
ini_set('memory_limit', '-1');

// Connect to MySQL server
$host = "localhost";
$username = "root";
$password = "";
$database = ""; // Enter your database name here

$conn = new mysqli($host, $username, $password, $database);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Increase MySQL server timeout
$conn->query('SET SESSION wait_timeout = 28800'); // 8 hours

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file is uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Retrieve the selected database
        $selectedDatabase = $_POST['mydb'] ?? '';

        // Retrieve the custom database name
        $customDatabaseName = $_POST['createdb'] ?? '';

        // If a custom database name is provided, create a new database
        if (!empty($customDatabaseName)) {
            // Sanitize the custom database name by removing special characters and spaces
            $sanitizedCustomDatabaseName = preg_replace('/[^A-Za-z0-9_]/', '', $customDatabaseName);

            // Create the new database
            $createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS `$sanitizedCustomDatabaseName`";
            if ($conn->query($createDatabaseQuery) === TRUE) {
                $_SESSION['success_message'] = "Database created successfully!";
                $selectedDatabase = $sanitizedCustomDatabaseName;
            } else {
                $_SESSION['error_message'] = "Error creating database: " . $conn->error;
            }
        }

        // Select the database
        $conn->select_db($selectedDatabase);

        // Retrieve the uploaded file details
        $file = $_FILES['file']['tmp_name'];

        try {
            // Load the spreadsheet file
            $spreadsheet = IOFactory::load($file);

            // Get the first worksheet
            $worksheet = $spreadsheet->getActiveSheet();

            // Get the column names from the first row
            $columnNames = [];
            $row = $worksheet->getRowIterator()->current();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $columnName = $cell->getValue();
                // Sanitize the column name by removing special characters and replacing spaces with underscores
                $sanitizedColumnName = preg_replace('/[^A-Za-z0-9_]/', '', $columnName);
                if (!empty($sanitizedColumnName)) {
                    $columnNames[] = $sanitizedColumnName;
                }
            }

            // Create the table if it doesn't exist
            $tableName = 'table1';

            // Check if the table already exists
            $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
            $result = $conn->query($checkTableQuery);

            if ($result !== FALSE && $result->num_rows === 0) {
                // Table doesn't exist, create a new one
                $createTableQuery = "CREATE TABLE `$tableName` (";
                foreach ($columnNames as $columnName) {
                    if (stripos($columnName, 'date') !== false) {
                        $createTableQuery .= "`$columnName` DATETIME, ";
                    } else {
                        $createTableQuery .= "`$columnName` VARCHAR(255), ";
                    }
                }
                $createTableQuery = rtrim($createTableQuery, ', ');
                $createTableQuery .= ")";

                if ($conn->query($createTableQuery) === TRUE) {
                    $_SESSION['success_message'] = "Table created successfully!";
                } else {
                    $_SESSION['error_message'] = "Error creating table: " . $conn->error;
                }
            }

            // Insert the data into the table
            $highestRow = $worksheet->getHighestRow();

            // Prepare the insert query
            $insertQuery = "INSERT INTO `$tableName` (`" . implode('`, `', $columnNames) . "`) VALUES ";

            // Iterate over the rows and generate the insert query values
            for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
                $rowData = [];
                $row = $worksheet->getRowIterator($rowIndex)->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();
                    if ($cell->getDataType() == \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC && Date::isDateTime($cell)) {
                        $value = Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
                    }
                    $rowData[] = $conn->real_escape_string($value);
                }

                $insertQuery .= "('" . implode("', '", $rowData) . "'), ";

                // Execute the insert query every 1000 rows to prevent timeout
                if ($rowIndex % 1000 === 0) {
                    // Remove the trailing comma and space from the insert query
                    $insertQuery = rtrim($insertQuery, ", ");

                    // Execute the insert query
                    if ($conn->query($insertQuery) === TRUE) {
                        $insertQuery = "INSERT INTO `$tableName` (`" . implode('`, `', $columnNames) . "`) VALUES ";
                    } else {
                        $_SESSION['error_message'] = "Error inserting data: " . $conn->error;
                        // Redirect back to the upload page
                        header("Location: excel.php");
                        exit();
                    }
                }
            }

            // Remove the trailing comma and space from the insert query
            $insertQuery = rtrim($insertQuery, ", ");

            // Execute the final insert query
            if ($conn->query($insertQuery) === TRUE) {
                $_SESSION['success_message'] = "Data inserted successfully!";
            } else {
                $_SESSION['error_message'] = "Error inserting data: " . $conn->error;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error reading the spreadsheet: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Error uploading file: " . $_FILES['file']['error'];
    }

    // Redirect back to the upload page
    header("Location: excel.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request method";
    // Redirect back to the upload page
    header("Location: excel.php");
    exit();
}
