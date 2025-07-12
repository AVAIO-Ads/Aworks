<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Prepare and execute the SQL statement
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashedPassword);
            $stmt->execute();
            header("Location:login.php");
        } catch (mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                echo "This email is already registered.";
            } else {
                echo "Something went wrong: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="assets/css/style1.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div id="success-popup" class="popup">
        <div class="popup-content">
            <p>Registered successfully!</p>
        </div>
    </div>
    <div class="container">
        <h1>Register</h1>
        <form method="POST" onsubmit="showSuccessPopup(event)">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+" placeholder="Your Email">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
<script>
        function showSuccessPopup(event) {
            event.preventDefault();
            document.getElementById('success-popup').style.display = 'flex';
            setTimeout(() => {
                event.target.submit(); // Submit form after showing popup
            }, 2000);
        }
    </script>
</html>

