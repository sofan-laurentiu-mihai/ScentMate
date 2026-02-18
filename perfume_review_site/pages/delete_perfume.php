<?php
include '../inc/db.php';
include '../inc/functions.php';

if (!isAdminLoggedIn()) {
    header('Location: admin_login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid perfume ID');
}

$perfume_id = (int) $_GET['id'];

// Delete related accords
$pdo->prepare("DELETE FROM perfume_accords WHERE perfume_id = ?")->execute([$perfume_id]);

// Delete ratings & reviews
$pdo->prepare("DELETE FROM ratings WHERE perfume_id = ?")->execute([$perfume_id]);
$pdo->prepare("DELETE FROM reviews WHERE perfume_id = ?")->execute([$perfume_id]);

// Delete perfume
$pdo->prepare("DELETE FROM perfumes WHERE id = ?")->execute([$perfume_id]);

header('Location: admin_dashboard.php');
exit;
