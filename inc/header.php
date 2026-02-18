<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ScentMate</title>
    <link rel="stylesheet" href="/perfume_review_site/assets/css/style.css">
</head>
<body>
<header>
  <div class="header-container">
    <div class="header-top">
      <div class="title-wrapper">
        <h1><a href="/perfume_review_site/pages/index.php">ScentMate</a></h1>
      </div>


      <form method="GET" action="/perfume_review_site/pages/index.php" class="header-search">
        <input type="text" name="search" placeholder="Search perfumes..."
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">🔍</button>
      </form>
    </div>

    <nav class="header-nav">
      <ul class="nav-links">
        <li><a href="/perfume_review_site/pages/top_rated.php">Top Rated</a></li>
        <?php if (isUserLoggedIn()): ?>
          <li><a href="/perfume_review_site/pages/profile.php">My Profile</a></li>
          <li><a href="/perfume_review_site/pages/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="/perfume_review_site/pages/login.php">Login</a></li>
          <li><a href="/perfume_review_site/pages/register.php">Register</a></li>
        <?php endif; ?>

        <?php if (!isAdminLoggedIn()): ?>
          <li><a href="/perfume_review_site/pages/admin_login.php">Admin Login</a></li>
        <?php else: ?>
          <li><a href="/perfume_review_site/pages/admin_dashboard.php">Admin Dashboard</a></li>
          <li><a href="/perfume_review_site/pages/logout.php">Logout (Admin)</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<main>
