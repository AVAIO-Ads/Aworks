<?php
session_start();
include(__DIR__ . '/includes/db.php');
include(__DIR__ . '/includes/auth.php');

$id = $_GET['id'];

$sql = "DELETE FROM tasks WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  header("Location: dashboard.php");
} else {
  echo "Error: " . $conn->error;
}

$stmt->close();
?>