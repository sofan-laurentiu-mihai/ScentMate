<?php
include '../inc/db.php';
include '../inc/functions.php';
include '../inc/header.php';
?>

<div class="top-rated-page">
    <h2>Top Rated Perfumes</h2>
    <div class="perfume-grid">
        <?php
        $stmt = $pdo->query("
            SELECT 
                p.*, 
                b.name AS brand,
                AVG((r.sillage + r.projection + r.longevity) / 3) AS avg_rating
            FROM perfumes p
            JOIN brands b ON p.brand_id = b.id
            JOIN ratings r ON p.id = r.perfume_id
            GROUP BY p.id
            ORDER BY avg_rating DESC
            LIMIT 10
        ");

        while ($row = $stmt->fetch()):
            $avg = round($row['avg_rating'], 1);
        ?>
            <div class="perfume-card">
                <div class="perfume-img" style="background-image: url('../assets/img/<?= htmlspecialchars($row['image']) ?>');"></div>
                <div class="perfume-info">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="brand"><?= htmlspecialchars($row['brand']) ?></p>
                    <p class="rating">⭐ <?= $avg ?> / 5</p>
                    <a class="view-btn" href="perfume.php?id=<?= $row['id'] ?>">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
