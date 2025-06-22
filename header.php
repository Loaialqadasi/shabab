<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
  <div>Shabab Lost & Found Assistant</div>
  <div class="top-right-buttons">
    <?php if (!isset($_SESSION['username'])): ?>
      <a href="login.php" class="header-link">Login</a>
      <a href="register.php" class="header-link">Register</a>
    <?php else: ?>
      <span class="header-link" style="background-color: #555; cursor: default;">
        Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
      </span>
      <a href="logout.php" class="header-link">Logout</a>
    <?php endif; ?>
  </div>
</header>

<nav>
  <a href="index.php">Home</a>
  <a href="report-lost.php">Report Lost</a>
  <a href="report-found.php">Report Found</a>
  <a href="search.php">Search</a>
</nav>