<?php
include '../inc/db.php';
include '../inc/functions.php';

if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $perfume_id = (int) $_POST['perfume_id'];
    $sillage = (int) $_POST['sillage'];
    $projection = (int) $_POST['projection'];
    $longevity = (int) $_POST['longevity'];
    $pros = trim($_POST['pros']);
    $cons = trim($_POST['cons']);

    // Save ratings
    $stmt = $pdo->prepare("
        INSERT INTO ratings (user_id, perfume_id, sillage, projection, longevity)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $perfume_id, $sillage, $projection, $longevity]);

    // Save review
    $stmt = $pdo->prepare("
        INSERT INTO reviews (user_id, perfume_id, pros, cons)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $perfume_id, $pros, $cons]);

    // Redirect back to perfume detail page
    header("Location: perfume.php?id=" . $perfume_id);
    exit;
} else {
    header("Location: index.php");
    exit;
}
