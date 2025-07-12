<?php
// Start the session securely
session_start();

// Include authentication and database connection files
include(__DIR__ . '/includes/auth.php');
include(__DIR__ . '/includes/db.php');

// Get user data from session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; // Default to 'Guest' if not logged in
$id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Use user_id from session

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['new_status'])) {
    $task_id = $_POST['task_id'];
    $new_status = $_POST['new_status'];

    // Debugging: Check the values
    echo "Updating task ID: $task_id to status: $new_status<br>"; // Uncomment for debugging

    // Update task status
    $updateStmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $updateStmt->bind_param("si", $new_status, $task_id);
    
    if ($updateStmt->execute()) {
        // Redirect to the same page to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Handle error
        echo "Error updating task: " . $updateStmt->error;
    }
}

// Fetch tasks assigned to the user along with the assigned user's name
$tasks = [];
$stmt = $conn->prepare("
    SELECT t.id, t.title, t.description, t.status, t.created_at, u.name AS assigned_name 
    FROM tasks t 
    JOIN users u ON t.assigned_to = u.id 
    WHERE t.assigned_to = ?
");
$stmt->bind_param("i", $id); // Assuming $id is the user ID
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Task Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        select, input[type="submit"] {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background: #ecf0f1;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #bdc3c7;
        }
        .nav-wrapper {
            display: flex;
            justify-content: flex-end;  /* Aligns child to the right */
            padding: 10px;
        }

        .nav-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0078d7;
            color: #fff;
            border-radius: 20px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2> <!-- Display the username -->
    <h3>Your Assigned Tasks</h3>
    <table>
        <tr>
            <th>No.</th>
            <th>Title</th>
            <th>Description</th>
            <th>Date/Time</th>
            <th>Current Status</th>
            <th>Status</th>
        </tr>
        <?php if (!empty($tasks)) : ?>
            <?php foreach ($tasks as $index => $task) : ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                    <td><?php echo htmlspecialchars($task['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($task['status']); ?></td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <select name="new_status">
                                <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in-progress" <?= $task['status'] == 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <input type="submit" value="Update">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6">No tasks assigned yet.</td>
            </tr>
        <?php endif; ?>
    </table>
    <div class="nav-wrapper">
        <a href="logout.php" class="nav-btn">Logout</a>
    </div>
</body>
</html>