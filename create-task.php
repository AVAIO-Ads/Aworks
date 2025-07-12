<?php
include(__DIR__ . '/includes/auth.php');
include(__DIR__ . '/includes/db.php');

// Fetch registered users for the dropdown
$userQuery = "SELECT id, name FROM users";  // Changed from username to name
$userResult = $conn->query($userQuery);

// Check if query was successful
if ($userResult === false) {
    die("Error fetching users: " . $conn->error);
}

$users = [];
while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $assigned_to = $_POST['assigned_to'];
    $created_by = $_SESSION['user_id'];
    $status = 'pending';

    $sql = "INSERT INTO tasks (title, description, status, assigned_to, created_by) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $title, $description, $status, $assigned_to, $created_by);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="POST">
            <h2>Create Task</h2>
            <label for="assigned_to">Assign To:</label>
            <select name="assigned_to" required>
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo htmlspecialchars($user['id']); ?>">
                        <?php echo htmlspecialchars($user['name']); ?>  <!-- Changed from username to name -->
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="title" placeholder="Task Title" required>
            <textarea name="description" placeholder="Task Description" required></textarea>
            <input type="date" name="date">
            <button type="submit">Add Task</button>
        </form>
    </div>
</body>
</html>
