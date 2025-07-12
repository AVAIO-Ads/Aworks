<?php
session_start();
include(__DIR__ . '/includes/auth.php');
include(__DIR__ . '/includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Prepare SQL query based on user role
$sql = $role === 'admin'
    ? "SELECT tasks.*, users.name AS assignee FROM tasks LEFT JOIN users ON tasks.assigned_to = users.id"
    : "SELECT tasks.*, users.name AS assignee FROM tasks LEFT JOIN users ON tasks.assigned_to = users.id WHERE assigned_to = ?";

$stmt = $conn->prepare($sql);
if ($role !== 'admin') {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <h1><i class="fas fa-tasks"></i> Task Manager</h1>
        <nav>
            <a href="create-task.php" class="nav-btn">Create Task</a>
            <a href="logout.php" class="nav-btn">Logout</a>
        </nav>
    </header>

    <section class="task-list">
        <h2>Your Tasks</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Task Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars($task['description']) ?></td>
                            <td><span class="status <?= htmlspecialchars($task['status']) ?>"><?= htmlspecialchars(ucfirst($task['status'])) ?></span></td>
                            <td><?= htmlspecialchars($task['assignee']) ?></td>
                            <td>
                                <a href="edit-task.php?id=<?= $task['id'] ?>" class="edit-btn">Edit</a>
                                <a href="delete-task.php?id=<?= $task['id'] ?>" class="delete-btn" onclick="return confirm('Delete this task?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tasks found.</p>
        <?php endif; ?>
    </section>
</body>
</html>
