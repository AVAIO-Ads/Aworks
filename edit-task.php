<?php
include(__DIR__ . '/includes/auth.php');
include(__DIR__ . '/includes/db.php');

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    $sql = "UPDATE tasks SET title=?, description=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $status, $id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
} else {
    $sql = "SELECT * FROM tasks WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add the CSS styles here */
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #7f7fd5, #86a8e7, #91eae4);
            margin: 0;
            padding: 20px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensures padding is included in width */
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: #2ebfcc; /* Change border color on focus */
            outline: none; /* Remove default outline */
        }

        button {
            background-color: #2ebfcc;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #1a9fbc; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Edit Task</h2>
        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
        <textarea name="description" required><?= htmlspecialchars($task['description']) ?></textarea>
        <select name="status" required>
            <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="in-progress" <?= $task['status'] == 'in-progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <button type="submit">Update Task</button>
    </form>
</body>
</html>
