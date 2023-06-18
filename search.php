<!DOCTYPE html>
<html>
<head>
    <title>Results</title>
    <link rel="stylesheet" type="text/css" href="static/search.css">
</head>
<body>
       
        <div class="nav2" >
        <h1 >RESULTS</h1>
        <div class="back-button">
        <form action="excel.php" method="GET">
                <button type="submit" class="bbutton">Back</button>
        </form>
        </div>
</div>
    <?php
    // Create a database connection
    $servername = "localhost";
    $username = "root";
    $password = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $dbname = $_POST['database'];
        $columnNames = $_POST['column'];
        $keywords = $_POST['keyword'];
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $table = "table1";

    // Check if the search form was submitted
    $whereClauses = [];
    $bindTypes = "";
    $bindParams = [];

    foreach ($columnNames as $index => $columnName) {
        $whereClauses[] = "`$columnName` = ?";
        $bindTypes .= "s";
        $bindParams[] = &$keywords[$index];
    }

    $whereClause = implode(" AND ", $whereClauses);
    $query = "SELECT * FROM `$table` WHERE $whereClause";
    $stmt = $conn->prepare($query);

    // Bind the parameters dynamically
    array_unshift($bindParams, $bindTypes);
    call_user_func_array(array($stmt, 'bind_param'), $bindParams);

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any matching results were found
    if ($result->num_rows > 0) {
        // Start the table with CSS styling
        echo '<table style="width: 100%; border-collapse: collapse; border: 1px solid black;">';

        // Print table headers with green background and white font color
        echo '<tr>';
        while ($column = $result->fetch_field()) {
            echo '<th style="padding: 10px; border: 1px solid black; background-color: #45a049; color: white;">' . $column->name . '</th>';
        }
        echo '</tr>';

        // Print table rows
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td style="padding: 10px; border: 1px solid black;">' . $value . '</td>';
            }
            echo '</tr>';
        }

        // End the table
        echo '</table>';
    } else {
        echo "No results found.";
    }

    $stmt->close();
    $conn->close();

    ?>

 
</body>
</html>
