<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_name']);
$isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
?>

<header>
  <div class="header-container">
    <h1 class="logo">Learn <span class="span-one">Skill</span></h1>

    <nav>
      <ul class="menu">
        <li><a href="index.php">Home</a></li>

        <!-- Courses page: Admin goes to course-list, user goes to course-page -->
        <li>
          <a href="<?php echo $isAdmin ? 'course-list.php' : 'course-page.php'; ?>">Courses</a>
        </li>

        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact Us</a></li>

        <!-- Only regular users see My Courses -->
        <?php if (!$isAdmin && $isLoggedIn): ?>
          <li><a href="my-courses.php">My Courses</a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="menu-next">
      <?php if ($isLoggedIn): ?>
        <span style="margin-right: 15px; font-weight: bold;">👤 <?php echo htmlspecialchars($userName); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
      <?php else: ?>
        <a href="login_form.php" class="login-btn">Login</a>
      <?php endif; ?>
    </div>
  </div>
</header>
