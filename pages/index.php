<?php 
include '../inc/db.php'; 
include '../inc/functions.php'; 
include '../inc/header.php'; 
?>

<div class="homepage">

    <!-- Sort Dropdown -->
    <form method="GET" class="sort-form" style="margin-bottom: 1rem;">
    <label for="sort">Sort by name:</label>
    <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'asc') ? 'selected' : '' ?>>A → Z</option>
        <option value="desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'desc') ? 'selected' : '' ?>>Z → A</option>
    </select>
</form>

    <div class="title-wrapper">
  <h2 class="section-title">Featured Perfumes</h2>
</div>

    <div class="perfume-grid">
        <?php
       $sort = $_GET['sort'] ?? 'asc';  // default to ascending

$order = ($sort === 'desc') ? 'DESC' : 'ASC';

$stmt = $pdo->query("
    SELECT p.*, b.name AS brand
    FROM perfumes p
    JOIN brands b ON p.brand_id = b.id
    ORDER BY p.name $order
    LIMIT 6
");



        while ($row = $stmt->fetch()):
    $avgRating = getAverageRating($pdo, $row['id']);
?>
    <div class="perfume-card">
        <div class="perfume-img" style="background-image: url('../assets/img/<?= htmlspecialchars($row['image']) ?>');"></div>
        <div class="perfume-info">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p class="brand"><?= htmlspecialchars($row['brand']) ?></p>
            <p class="rating">
                <?= $avgRating ? str_repeat("⭐", round($avgRating)) . " ($avgRating)" : "Not rated yet" ?>
            </p>
            <a class="view-btn" href="perfume.php?id=<?= $row['id'] ?>">View Details</a>
        </div>
    </div>
<?php endwhile; ?>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
