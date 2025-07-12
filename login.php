<?php
session_start();
include(__DIR__ . '/includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Check for admin credentials
    if ($email === 'admin@gmail.com' && $password === '123456789') {
        // Store admin information in session
        $_SESSION['user_id'] = 1; // Assuming admin ID is 1
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] = 'Admin'; // Store admin username
        header("Location: dashboard.php");
        exit(); // Ensure no further code is executed after the redirect
    }

    // Prepare SQL statement to prevent SQL injection for regular users
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify user credentials
    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['name']; // Store the user's name
        header("Location: user_home.php");
        exit(); // Ensure no further code is executed after the redirect
    } else {
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style1.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
