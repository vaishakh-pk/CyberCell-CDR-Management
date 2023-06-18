<?php
if (isset($_GET['database'])) {
    $selectedDatabase = $_GET['database'];
    
    // Assuming you have the database connection established
    $host = "localhost";
    $conn = new mysqli($host, "root", "", $selectedDatabase);
    
    $query = "SHOW COLUMNS FROM table1";
    $result = mysqli_query($conn, $query);
    
    $tables = array();
    
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }
    
    echo json_encode($tables);
}
?>
