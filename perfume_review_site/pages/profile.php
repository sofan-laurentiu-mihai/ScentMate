<?php
include '../inc/db.php';
include '../inc/functions.php';

if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT username, email, favorite_perfume_id FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get favorite perfume info (if any)
$favPerfume = null;
if ($user['favorite_perfume_id']) {
    $stmt = $pdo->prepare("
        SELECT p.*, b.name AS brand 
        FROM perfumes p 
        JOIN brands b ON p.brand_id = b.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$user['favorite_perfume_id']]);
    $favPerfume = $stmt->fetch();
}

// Handle update of favorite perfume
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_perfume'])) {
    $newFavorite = (int) $_POST['favorite_perfume'];
    $stmt = $pdo->prepare("UPDATE users SET favorite_perfume_id = ? WHERE id = ?");
    $stmt->execute([$newFavorite, $userId]);
    header("Location: profile.php");
    exit;
}
?>

<?php include '../inc/header.php'; ?>

<div class="profile-container">
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

    <h3>Your Favorite Perfume</h3>
    <?php if ($favPerfume): ?>
        <div class="perfume-card" style="max-width: 300px;">
            <div class="perfume-img" style="background-image: url('../assets/img/<?= htmlspecialchars($favPerfume['image']) ?>');"></div>
            <div class="perfume-info">
                <h4><?= htmlspecialchars($favPerfume['name']) ?></h4>
                <p class="brand"><?= htmlspecialchars($favPerfume['brand']) ?></p>
                <a href="perfume.php?id=<?= $favPerfume['id'] ?>">View Details</a>
            </div>
        </div>
    <?php else: ?>
        <p>You have not selected a favorite perfume.</p>
    <?php endif; ?>

    <h3>Set or Change Favorite Perfume</h3>
    <form method="POST">
        <select name="favorite_perfume" required>
            <option value="">-- Select Perfume --</option>
            <?php
            $stmt = $pdo->query("SELECT id, name FROM perfumes ORDER BY name ASC");
            while ($perfume = $stmt->fetch()):
                $selected = ($perfume['id'] == $user['favorite_perfume_id']) ? 'selected' : '';
            ?>
                <option value="<?= $perfume['id'] ?>" <?= $selected ?>><?= htmlspecialchars($perfume['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Update Favorite</button>
    </form>
</div>

<?php include '../inc/footer.php'; ?>
