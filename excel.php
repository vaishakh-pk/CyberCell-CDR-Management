<?php
session_start();

if (isset($_SESSION['success_message'])) {
    echo "<script>alert('".$_SESSION['success_message']."');</script>";
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<script>alert('".$_SESSION['error_message']."');</script>";
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doc</title>
    <link rel="stylesheet" type="text/css" href="static/excel.css">
</head>
<body>
    <h1 class="nav">UPLOADING AND SEARCHING</h1>
    <div class="container">
        <div class="left-component">
            <!-- Content for the left component -->
            <h2>UPLOAD FILE</h2>

            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="upload-container">
                    <input type="file" id="file-input" name="file" accept=".xlsx, .xls, .csv" onchange="handleFileChange(event)">
                    <label for="file-input" class="upload-button">Choose Excel File</label>
                    <span id="file-name">No file chosen</span>
                </div>

                <div class="options">
                    <div class="options">
                        <p style="padding-right: 10px;">Choose database</p>
                        <div class="dropdown-input">
                            <select id="input1" name="mydb" onchange="handleInputChange()">
                                <option value="" disabled selected>Select an option</option>
                                <?php
                                // Assuming you have the database connection established

                                // Fetch the list of all databases
                                $query = "SHOW DATABASES";
                                $host = "localhost";
                                $conn = new mysqli($host, "root", "");
                                $result = mysqli_query($conn, $query);

                                // Iterate through the result and create options for each database
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $database = $row['Database'];
                                    echo "<option value='$database'>$database</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="options">
                    <br>
                    <p style="padding-right: 10px;">Create database</p>
                    <input type="text" id="input2" name="createdb" placeholder="Database name" oninput="handleInputChange()">
                </div>

                <div class="options">
                    <br>
                    <button type="submit">Upload</button>
                </div>
            </form>

        </div>
        
        <!----------------------------------------------------------------- Content for the right component ----------------------------------------->
            
        <div class="right-component">
    <h2 style="text-align: center;">SEARCH DATABASES</h2>
    <?php
    if (!isset($_POST['database'])) {
        // First form: Select Database
        ?>
        <div class="options">
            <div class="dropdown" style="text-align: center;">
                <form action="" method="POST">
                    <select id="search-input" name="database" class="dropdown-button">
                        <?php
                        $query = "SHOW DATABASES";
                        $host = "localhost";
                        $conn = new mysqli($host, "root", "");
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()) {
                            $database = $row['Database'];
                            echo "<option value='$database'>$database</option>";
                        }
                        ?>
                    </select>
                    <div style="margin-top: 10px;">
                        <input type="submit" name="submit" value="Submit Database" class="submit-button">
                    </div>
                </form>
            </div>
        </div>
        <?php
    } else {
        // Second form: Select Columns
        $selectedDatabase = $_POST['database'];
        $table = "table1";

        // Check if the table exists in the selected database
        $conn = new mysqli($host, "root", "", $selectedDatabase);
        $query = "SHOW TABLES LIKE '$table'";
        $result = $conn->query($query);
        if ($result->num_rows == 0) {
            echo "<p style='text-align: center;'>No table named '$table' found in the selected database.</p>";
        } else {
            ?>
            <div>
                <form action="search.php" method="POST">
                    <input type="hidden" name="database" value="<?php echo $selectedDatabase; ?>">
                    <div id="columns-container" style="text-align: center;">
                        <div style="margin-bottom: 0px;">
                            <label for="column" style="display: inline-block;">Select column:</label>
                            <select name="column[]" style="display: inline-block; margin-right: 10px;">
                                <?php
                                $query = "SHOW COLUMNS FROM $table";
                                $result = $conn->query($query);

                                while ($row = $result->fetch_row()) {
                                    $column = $row[0];
                                    echo "<option value='$column'>$column</option>";
                                }
                                ?>
                            </select>
                            <input type="text" name="keyword[]" placeholder="Search keyword" style="display: inline-block; margin-right: 10px;">
                        </div>
                    </div>
                    <div class="dropdown" style="text-align: center; margin-top: 5px;">
                        <button type="button" onclick="addColumn()" id="addButton" style="margin-left: 20px;">Add</button>
                    </div>
                    <div class="options" style="text-align: center;">
                        <div style="position: sticky; bottom: 10px;">
                            <input type="submit" value="Search" style="padding: 8px 16px; font-size: 14px; border: none; border-radius: 4px; background-color: #4CAF50; color: white; cursor: pointer;">
                            <button type="button" onclick="goBack()" style="padding: 8px 16px; font-size: 14px; border: none; border-radius: 4px; background-color: #e0e0e0; color: black; cursor: pointer; margin-left: 10px;">Change database</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
    }
    ?>
</div>
</div>
<div class="bottom-div">
    <div class="bottom-component">
        <div class="options">
            <button onclick="modify()">MODIFY DATABASE</button>
        </div>
    </div>
</div>

<script>
    function handleInputChange() {
        var input1 = document.getElementById('input1');
        var input2 = document.getElementById('input2');

        // Disable input2 if input1 has a value, enable otherwise
        if (input1.value.trim() !== '') {
            input2.disabled = true;
        } else {
            input2.disabled = false;
        }

        // Disable input1 if input2 has a value, enable otherwise
        if (input2.value.trim() !== '') {
            input1.disabled = true;
        } else {
            input1.disabled = false;
        }
    }

    function handleFileChange(event) {
        var fileInput = event.target;
        var fileNameElement = document.getElementById('file-name');
        if (fileInput.files.length > 0) {
            fileNameElement.textContent = fileInput.files[0].name;
        } else {
            fileNameElement.textContent = 'No file chosen';
        }
    }

    function modify() {
        window.location.href = 'modify.php';
    }

    </script>
    <script>
    function addColumn() {
        var container = document.getElementById("columns-container");
        var existingColumns = container.getElementsByTagName("div").length;
        if (existingColumns < 4) {
            var newColumn = document.createElement("div");
            newColumn.innerHTML = `
                <label for="column" style="display: inline-block;">Select column:</label>
                <select name="column[]" style="display: inline-block; margin-right: 10px;">
                    <?php
                    $query = "SHOW COLUMNS FROM $table";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_row()) {
                        $column = $row[0];
                        echo "<option value='$column'>$column</option>";
                    }
                    ?>
                </select>
                <input type="text" name="keyword[]" placeholder="Search keyword" style="display: inline-block; margin-right: 10px;margin-top: 10px;">
                <button type="button" onclick="deleteColumn(this)" style="font-size: 12px; padding: 2px 4px; background-color: white; color: red; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='black'" onmouseout="this.style.backgroundColor='white'">❌</button>
            `;
            container.appendChild(newColumn);

            // Disable "Add" button if the maximum number of columns is reached
            if (existingColumns + 1 === 4) {
                document.getElementById("addButton").disabled = true;
            }
        }
    }

    function deleteColumn(button) {
        var columnDiv = button.parentNode;
        columnDiv.parentNode.removeChild(columnDiv);

        // Enable "Add" button after deleting a column
        document.getElementById("addButton").disabled = false;
    }

    function goBack() {
        window.history.back();
        
    }




</script>
</body>
</html>
