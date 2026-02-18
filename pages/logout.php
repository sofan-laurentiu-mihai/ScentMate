<?php
session_start(); // Start the session if not already started

// Unset all login session variables
unset($_SESSION['user_id']);
unset($_SESSION['admin_id']);

// Optionally destroy session completely
session_destroy();

// Redirect to homepage or login page
header('Location: index.php');
exit;
