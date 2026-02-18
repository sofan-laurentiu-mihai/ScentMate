<?php
// Check if user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Get average rating for a perfume
function getAverageRating($pdo, $perfume_id) {
    $stmt = $pdo->prepare("
        SELECT AVG((sillage + projection + longevity) / 3) AS avg_rating 
        FROM ratings 
        WHERE perfume_id = ?
    ");
    $stmt->execute([$perfume_id]);
    $result = $stmt->fetch();
    return round($result['avg_rating'], 1);
}

// Get top 3 accords for a perfume
function getPerfumeAccords($pdo, $perfume_id) {
    $stmt = $pdo->prepare("
        SELECT a.name 
        FROM perfume_accords pa
        JOIN accords a ON pa.accord_id = a.id
        WHERE pa.perfume_id = ?
        ORDER BY pa.position ASC
        LIMIT 3
    ");
    $stmt->execute([$perfume_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get perfume name by ID
function getPerfumeName($pdo, $id) {
    $stmt = $pdo->prepare("SELECT name FROM perfumes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
}

function renderStars($rating) {
    $fullStar = '⭐';
    $stars = '';
    for ($i = 0; $i < 5; $i++) {
        $stars .= $i < $rating ? $fullStar : '<span style="opacity: 0.2;">' . $fullStar . '</span>';
    }
    return $stars;
}

?>


