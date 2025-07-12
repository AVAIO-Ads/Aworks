<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Manager</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <h1>Welcome to Task Manager</h1>
    <p>A simple and dynamic system for organizing your work.</p>
    <div class="button-group">
      <a href="register.php" class="btn">Register</a>
      <a href="login.php" class="btn">Login</a>
    </div>
  </div>
</body>
</html>