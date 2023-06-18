<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the database name from the form submission
    $databaseName = $_POST["databaseName"];

    // Perform the deletion operation using appropriate database management functions
    // For example, using MySQLi:
    $servername = "localhost";
    $username = "root";
    $password = "";

    $conn = new mysqli($servername, $username, $password);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to drop the database
    $sql = "DROP DATABASE IF EXISTS $databaseName";

    if ($conn->query($sql) === TRUE) {
        echo '<script type ="text/JavaScript">';
        echo 'alert("Database deleted Successfully")';
        echo '</script>';
        $redirectUrl = "modify.php"; // URL of the page to be redirect to

        header("Refresh: 0.5; url=" . $redirectUrl);
        exit;
    } else {
        echo "Error deleting database: " . $conn->error;
    }

    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the new name and current name of the database from the form
    $newName = $_POST["newName"];
    $currentName = $_POST["currentName"];

    // Establish a connection to the database server
    $host = "localhost";
    $username = "root";
    $password = "";
    $conn = new mysqli($host, $username, $password);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the new name is different from the current name
    if ($newName !== $currentName) {
        // Check if the database with the new name already exists
        $checkQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$newName'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            echo "Error: A database with the name '$newName' already exists.";
        } else {
            // Export the database tables and data
            $exportCommand = "mysqldump -u root --password= " . $currentName . " > " . $currentName . "_export.sql";
            exec($exportCommand);

            // Create a new database with the desired name
            $createDatabaseQuery = "CREATE DATABASE `$newName`";
            if ($conn->query($createDatabaseQuery) === TRUE) {
                // Import the exported data into the new database
                $importCommand = "mysql -u root --password= " . $newName . " < " . $currentName . "_export.sql";
                exec($importCommand);

                // Drop the old database
                $dropDatabaseQuery = "DROP DATABASE `$currentName`";
                if ($conn->query($dropDatabaseQuery) === TRUE) {
                    echo '<script type ="text/JavaScript">';
                    echo 'alert("Database renamed Successfully")';
                    echo '</script>';
                    $redirectUrl = "modify.php"; // URL of the page you want to redirect to

                    header("Refresh: 0.5; url=" . $redirectUrl);
                    exit;
                } else {
                    echo "Error dropping old database: " . $conn->error;
                }
            } else {
                echo "Error creating new database: " . $conn->error;
            }
        }
    } else {
        echo "Error: The new name must be different from the current name.";
    }

    // Close the database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Modify Database</title>
  <link rel="stylesheet" type="text/css" href="static/modify.css">
</head>
<body>
<div class="nav">
<h1>MODIFY DATABASE</h1>
<div class="back-button">
<form action="excel.php">
<button class="bbutton" onclick="excel.php">back</button>
</form>
</div>
 
    
  </div>
  <div class="main">
  <ul id="databaseList">
    <?php

$query = "SHOW DATABASES";
$host = "localhost";
$conn = new mysqli($host, "root", "");
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    // Loop through the databases
    foreach ($row as $database) {
        echo '<li>
              <div class="database-info">
                <span class="database-name">' . $database . '</span>
                <div class="button-container">
                <button class="rename-button" onclick="showRenameTextbox(this)">Rename</button>
                  <form method="POST" action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;">
                    <input type="hidden" name="currentName" value="' . $database . '">
                    <input type="text" name="newName" placeholder="New Name" required style="display: none;">
                    <button type="submit" class="rename-submit-button" onclick="return confirm(\'Due to security reasons, renaming this database creates a new database and imports data from this database to the new database. Are you sure you want to rename this database?\')" style="display: none;">Submit</button>
                  </form>
                  <form method="post" action="' . $_SERVER['PHP_SELF'] . '" style="display: inline;">
                    <input type="hidden" name="databaseName" value="' . $database . '">
                    <button type="submit" class="delete-button" onclick="return confirm(\'Are you sure you want to delete this database?\')">Delete</button>
                  </form>
                </div>
              </div>
            </li>';
    }
}

?>
  </ul>
</div>
<script>
function showRenameTextbox(button) {
  const databaseName = button.parentNode.previousElementSibling.innerText;
  const textbox = button.parentNode.querySelector('input[name="newName"]');
  const submitButton = button.parentNode.querySelector('.rename-submit-button');
  
  textbox.style.display = 'inline-block';
  submitButton.style.display = 'inline-block';
  button.style.display = 'none';
  
  textbox.value = databaseName;
  textbox.focus();
}
</script>
</body>
</html>
