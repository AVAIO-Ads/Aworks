<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Optional: Restrict admin-only pages
function isAdmin() {
  return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}