<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="static/loginpg.css">
</head>
<body class="body-login">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve username and password from the login form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Database connection credentials
    $host = "localhost"; // Update with the correct port number
    $dbUsername = $username;
    $dbPassword = $password;
    $dbPassword = "";

    // Attempt to establish a connection with the provided username and password
    $conn = new mysqli($host, $dbUsername, $dbPassword);

    // Check if the connection was successful
    if ($conn->connect_error) {
        // Connection failed, display error message
        echo "Connection failed: " . $conn->connect_error;
    } else {
        // Connection successful, redirect to excel.html or any other desired page
        header("Location: excel.php");
        exit(); // Ensure that the script stops execution after redirecting
    }

    $conn->close();
}
?>
  <div class="center">
    <div class="logo">
      <img src="static/logo_kerala.png" alt="Logo">
    </div>
    <div class="login-container">
      <h2>Login</h2>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
          <input type="submit" value="Login">
        </div>
      </form>
    </div>
  </div>
</body>
</html>
