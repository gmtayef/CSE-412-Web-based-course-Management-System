<?php
include 'config.php';
session_start();

if (!isset($_GET['id'])) {
    die("Course ID not provided.");
}

$course_id = intval($_GET['id']);
$course_query = "SELECT * FROM products WHERE id = $course_id";
$course_result = mysqli_query($conn, $course_query);
if (!$course_result || mysqli_num_rows($course_result) == 0) {
    die("Invalid course ID.");
}
$course = mysqli_fetch_assoc($course_result);

$episode_query = "SELECT * FROM episode WHERE course_id = $course_id ORDER BY e_id ASC";
$result = mysqli_query($conn, $episode_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Episodes for <?php echo htmlspecialchars($course['name']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;600&family=Poppins:wght@300;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/episodeList.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-container">
            <h1 class="logo">Learn <span class="span-one">Skill</span></h1>
            <div class="menu-next"></div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <section class="another">
        <div class="another-one">
            <h2><span class="section-heading">Episodes For: <?php echo htmlspecialchars($course['name']); ?></span></h2>
        </div>
        <div class="another-two">
            <a href="index1.php" class="home-link"><i class="fa fa-home"></i> Home</a> <h4>/ Episodes</h4>
        </div>
    </section>

    <!-- Episode List -->
    <div class="episode-container">
        <a href="index1.php" class="back-btn">&larr; Back to Home</a>
        <h2 class="episode-heading">Episodes for: <?php echo htmlspecialchars($course['name']); ?></h2>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="episode-box">
                    <h3 class="episode-title">
                        <?php echo htmlspecialchars($row['e_name']); ?>
                        <button class="like-btn" title="Like this episode"><i class="fa fa-heart"></i></button>
                    </h3>
                    <p class="episode-desc"><?php echo htmlspecialchars($row['e_description']); ?></p>
                    <a class="watch-btn" href="<?php echo htmlspecialchars($row['e_path']); ?>" target="_blank">
                        <i class="fa fa-play"></i> Watch Episode
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="new">No episodes found for this course.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <section class="footer-menu">
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col" id="company">
                        <h1 class="logo-new">. Learn <span class="span-one">Skill</span></h1>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos magni expedita laboriosam deleniti eaque corrupti hic itaque illum ab architecto?</p>
                        <div class="social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                            <a href="#"><i class="fa fa-youtube"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-github"></i></a>
                        </div>
                    </div>
                    <div class="col" id="Explore">
                        <h3>Explore</h3>
                        <div class="links">
                            <a href="about.php">About us</a>
                            <a href="contact.php">Contact us</a>
                            <a href="FAQ.php">FAQ</a>
                            <a href="privacy-policy.php">Privacy policy</a>
                        </div>
                    </div>
                    <div class="col" id="Courses">
                        <h3>Courses</h3>
                        <div class="links">
                            <a href="#">Python</a>
                            <a href="#">Java</a>
                            <a href="#">C++</a>
                            <a href="#">C</a>
                        </div>
                    </div>
                    <div class="col" id="address">
                        <h3>Address</h3>
                        <div class="address-details">
                            <i class="fa fa-location"></i>
                            <p>Aftabnagor, <br> Dhaka, <br> Bangladesh</p>
                        </div>
                        <div class="address-details">
                            <i class="fa fa-phone"></i>
                            <p>+88017**********</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form">
                        <form action="">
                            <input type="text" placeholder="Email here...">
                            <button><i class="fa fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bottom-details">
                <p>All Rights reserved by &copy;Learn Skill 2023</p>
            </div>
        </footer>
    </section>

</body>
</html>
