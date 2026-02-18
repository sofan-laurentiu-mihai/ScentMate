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

// 🔄 Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $brand_id = $_POST['brand_id'];
    $image = $_POST['image'];
    $release_year = $_POST['release_year'];
    $description = $_POST['description'];
    $accord_ids = $_POST['accords'];

    // Update perfume
    $stmt = $pdo->prepare("UPDATE perfumes SET name = ?, brand_id = ?, image = ?, release_year = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $brand_id, $image, $release_year, $description, $perfume_id]);

    // Delete existing accords
    $pdo->prepare("DELETE FROM perfume_accords WHERE perfume_id = ?")->execute([$perfume_id]);

    // Insert new top 3 accords
    $position = 1;
    foreach ($accord_ids as $accord_id) {
        $stmt = $pdo->prepare("INSERT INTO perfume_accords (perfume_id, accord_id, position) VALUES (?, ?, ?)");
        $stmt->execute([$perfume_id, $accord_id, $position]);
        $position++;
        if ($position > 3) break;
    }

    header("Location: admin_dashboard.php");
    exit;
}

// Load perfume
$stmt = $pdo->prepare("SELECT * FROM perfumes WHERE id = ?");
$stmt->execute([$perfume_id]);
$perfume = $stmt->fetch();

// Load existing accords
$stmt = $pdo->prepare("SELECT accord_id FROM perfume_accords WHERE perfume_id = ?");
$stmt->execute([$perfume_id]);
$current_accords = array_column($stmt->fetchAll(), 'accord_id');
?>

<?php include '../inc/header.php'; ?>

<div class="admin-dashboard">
    <h2>Edit Perfume: <?= htmlspecialchars($perfume['name']) ?></h2>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($perfume['name']) ?>" required>

        <label>Brand:</label>
        <select name="brand_id" required>
            <?php
            $brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
            foreach ($brands as $brand) {
                $selected = ($brand['id'] == $perfume['brand_id']) ? 'selected' : '';
                echo "<option value='{$brand['id']}' $selected>" . htmlspecialchars($brand['name']) . "</option>";
            }
            ?>
        </select>

        <label>Image File Name:</label>
        <input type="text" name="image" value="<?= htmlspecialchars($perfume['image']) ?>" required>

        <label>Release Year:</label>
        <input type="number" name="release_year" value="<?= htmlspecialchars($perfume['release_year']) ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($perfume['description']) ?></textarea>

        <label>Top 3 Accords:</label>
        <select name="accords[]" multiple size="6" required>
            <?php
            $accords = $pdo->query("SELECT * FROM accords ORDER BY name ASC")->fetchAll();
            foreach ($accords as $accord) {
                $selected = in_array($accord['id'], $current_accords) ? 'selected' : '';
                echo "<option value='{$accord['id']}' $selected>" . htmlspecialchars($accord['name']) . "</option>";
            }
            ?>
        </select>
        <small style="color:#ccc;">Hold Ctrl (Windows) or Cmd (Mac) to select up to 3 accords.</small>

        <br><br>
        <button type="submit">Save Changes</button>
    </form>
</div>

<?php include '../inc/footer.php'; ?>
