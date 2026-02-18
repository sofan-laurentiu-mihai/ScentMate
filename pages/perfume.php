<?php
include '../inc/db.php';
include '../inc/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$perfume_id = $_GET['id'];

// Fetch perfume info
$stmt = $pdo->prepare("
    SELECT p.*, b.name AS brand 
    FROM perfumes p 
    JOIN brands b ON p.brand_id = b.id 
    WHERE p.id = ?
");
$stmt->execute([$perfume_id]);
$perfume = $stmt->fetch();

if (!$perfume) {
    echo "<p>Perfume not found.</p>";
    exit;
}

// Get top 3 accords
$accords = getPerfumeAccords($pdo, $perfume_id);

// Average ratings
$stmt = $pdo->prepare("
    SELECT 
        AVG(sillage) AS avg_sillage,
        AVG(projection) AS avg_projection,
        AVG(longevity) AS avg_longevity
    FROM ratings WHERE perfume_id = ?
");
$stmt->execute([$perfume_id]);
$ratings = $stmt->fetch();
$sillage = round($ratings['avg_sillage'] ?? 0);
$projection = round($ratings['avg_projection'] ?? 0);
$longevity = round($ratings['avg_longevity'] ?? 0);


// Fetch user reviews
$reviews = $pdo->prepare("
    SELECT u.username, r.pros, r.cons, r.created_at
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.perfume_id = ?
    ORDER BY r.created_at DESC
");
$reviews->execute([$perfume_id]);
?>

<?php include '../inc/header.php'; ?>

<div class="perfume-detail">
    <h2><?= htmlspecialchars($perfume['name']) ?> <span class="brand">by <?= htmlspecialchars($perfume['brand']) ?></span></h2>
    <img src="../assets/img/<?= htmlspecialchars($perfume['image']) ?>" class="perfume-image" alt="Perfume Image">

    <div class="accords">
        <h4>Top Accords:</h4>
        <ul>
            <?php foreach ($accords as $accord): ?>
                <li><?= htmlspecialchars($accord) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="ratings-summary">
        <h4>Average Ratings</h4>
        <p><strong>Sillage:</strong> <?= renderStars($sillage) ?></p>
        <p><strong>Projection:</strong> <?= renderStars($projection) ?></p>
        <p><strong>Longevity:</strong> <?= renderStars($longevity) ?></p>

    </div>

    <?php if (isUserLoggedIn()): ?>
        <div class="submit-review">
            <h3>Submit Your Review</h3>
            <form method="POST" action="submit_review.php">
                <input type="hidden" name="perfume_id" value="<?= $perfume_id ?>">
                <label>Sillage (1–5):</label>
                <input type="number" name="sillage" min="1" max="5" required>

                <label>Projection (1–5):</label>
                <input type="number" name="projection" min="1" max="5" required>

                <label>Longevity (1–5):</label>
                <input type="number" name="longevity" min="1" max="5" required>

                <label>Pros:</label>
                <textarea name="pros" required></textarea>

                <label>Cons:</label>
                <textarea name="cons" required></textarea>

                <button type="submit">Submit Review</button>
            </form>
        </div>
    <?php else: ?>
        <p><em><a href="login.php">Log in</a> to leave a review.</em></p>
    <?php endif; ?>

    <div class="user-reviews">
        <h3>User Reviews</h3>
        <?php foreach ($reviews as $review): ?>
            <div class="review-box">
                <p><strong><?= htmlspecialchars($review['username']) ?></strong> (<?= $review['created_at'] ?>)</p>
                <p><strong>Pros:</strong> <?= htmlspecialchars($review['pros']) ?></p>
                <p><strong>Cons:</strong> <?= htmlspecialchars($review['cons']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
