<?php
include '../inc/db.php';
include '../inc/functions.php';

if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// --- DELETE HANDLER START ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    try {
        // Delete related accords
        $pdo->prepare("DELETE FROM perfume_accords WHERE perfume_id = ?")->execute([$delete_id]);

        // Delete related reviews (optional)
        $pdo->prepare("DELETE FROM reviews WHERE perfume_id = ?")->execute([$delete_id]);

        // Delete the perfume
        $pdo->prepare("DELETE FROM perfumes WHERE id = ?")->execute([$delete_id]);

        // Redirect to refresh the page and avoid re-execution on refresh
        header('Location: admin_dashboard.php');
        exit;
    } catch (PDOException $e) {
        echo "Error deleting perfume: " . htmlspecialchars($e->getMessage());
    }
}
// --- DELETE HANDLER END ---

// Add Brand Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brand'])) {
    $brand_name = trim($_POST['brand_name']);

    if (!empty($brand_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO brands (name) VALUES (?)");
            $stmt->execute([$brand_name]);
            header('Location: admin_dashboard.php'); // Redirect to avoid form resubmission
            exit;
        } catch (PDOException $e) {
            echo "Error adding brand: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Add Accord Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_accord'])) {
    $accord_name = trim($_POST['accord_name']);

    if (!empty($accord_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO accords (name) VALUES (?)");
            $stmt->execute([$accord_name]);
            header('Location: admin_dashboard.php'); // Redirect to avoid form resubmission
            exit;
        } catch (PDOException $e) {
            echo "Error adding accord: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Add Perfume Handling (same as before)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_perfume'])) {
    $name = $_POST['name'];
    $brand_id = $_POST['brand_id'];
    $image = $_POST['image'];
    $release_year = $_POST['release_year'];
    $description = $_POST['description'];
    $accord_ids = $_POST['accords'];

    // Insert perfume
    $stmt = $pdo->prepare("INSERT INTO perfumes (name, brand_id, image, release_year, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $brand_id, $image, $release_year, $description]);
    $perfume_id = $pdo->lastInsertId();

    // Insert accords with position (1–3)
    $position = 1;
    foreach ($accord_ids as $accord_id) {
        $stmt = $pdo->prepare("INSERT INTO perfume_accords (perfume_id, accord_id, position) VALUES (?, ?, ?)");
        $stmt->execute([$perfume_id, $accord_id, $position]);
        $position++;
        if ($position > 3) break;
    }

    header('Location: admin_dashboard.php');
    exit;
}
?>

<?php include '../inc/header.php'; ?>

<div class="admin-dashboard">
    <h2>Admin Dashboard</h2>

    <h3>Add New Perfume</h3>
    <form method="POST" enctype="multipart/form-data" action="admin_dashboard.php">
        <label>Perfume Name:</label>
        <input type="text" name="name" required>

        <label>Brand:</label>
        <select name="brand_id" required>
            <?php
            $brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
            foreach ($brands as $brand) {
                echo "<option value='{$brand['id']}'>" . htmlspecialchars($brand['name']) . "</option>";
            }
            ?>
        </select>

        <label>Image File Name (stored in /assets/img/):</label>
        <input type="text" name="image" required placeholder="e.g. armani-code.jpg">

        <label>Release Year:</label>
        <input type="number" name="release_year" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Top 3 Accords:</label>
        <select name="accords[]" multiple size="6" required>
            <?php
            $accords = $pdo->query("SELECT * FROM accords ORDER BY name ASC")->fetchAll();
            foreach ($accords as $accord) {
                echo "<option value='{$accord['id']}'>" . htmlspecialchars($accord['name']) . "</option>";
            }
            ?>
        </select>

        <button type="submit" name="add_perfume">Add Perfume</button>
    </form>

    <hr>

    <h3>Add New Brand</h3>
    <form method="POST" action="admin_dashboard.php">
        <label>Brand Name:</label>
        <input type="text" name="brand_name" required>
        <button type="submit" name="add_brand">Add Brand</button>
    </form>

    <h3>Add New Accord</h3>
    <form method="POST" action="admin_dashboard.php">
        <label>Accord Name:</label>
        <input type="text" name="accord_name" required>
        <button type="submit" name="add_accord">Add Accord</button>
    </form>

    <hr>

    <h3>Existing Perfumes</h3>
    <ul>
        <?php
        $perfumes = $pdo->query("
            SELECT p.id, p.name, b.name AS brand
            FROM perfumes p
            JOIN brands b ON p.brand_id = b.id
            ORDER BY p.name ASC
        ");
        foreach ($perfumes as $p) {
            echo "<li>" . htmlspecialchars($p['name']) . " by " . htmlspecialchars($p['brand']) .
                 " - <a href='edit_perfume.php?id={$p['id']}'>Edit</a>" . 
                 " | <a href='admin_dashboard.php?delete={$p['id']}' onclick=\"return confirm('Are you sure you want to delete this perfume?');\" style='color:red;'>Delete</a></li>";
        }
        ?>
    </ul>

    <h3>Existing Brands</h3>
    <ul>
        <?php
        $brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
        foreach ($brands as $brand) {
            echo "<li>" . htmlspecialchars($brand['name']) . "</li>";
        }
        ?>
    </ul>

    <h3>Existing Accords</h3>
    <ul>
        <?php
        $accords = $pdo->query("SELECT * FROM accords ORDER BY name ASC")->fetchAll();
        foreach ($accords as $accord) {
            echo "<li>" . htmlspecialchars($accord['name']) . "</li>";
        }
        ?>
    </ul>
</div>

<?php include '../inc/footer.php'; ?>
