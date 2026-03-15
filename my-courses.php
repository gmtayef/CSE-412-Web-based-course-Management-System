<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT p.* FROM products p 
        JOIN enrollments e ON p.id = e.course_id 
        WHERE e.user_id = '$user_id'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <link rel="stylesheet" href="css/contact.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <div class="header-container">
        <h1 class="logo">Learn <span class="span-one">Skill</span></h1>
    </div>
</header>

<section class="another">
    <div class="another-one">
        <h2><span class="section-heading">My Enrolled Courses</span></h2>
    </div>
    <div class="another-two">
        <a href="index1.php" class="home-link">Home</a> <h4>/ My Courses</h4>
    </div>
</section>

<section class="contact-us">
    <div class="contact-in">
        <div class="contact-form" style="width: 100%;">
            <h1>Enrolled Courses</h1>
            <?php if (isset($_GET['enrolled'])): ?>
                <p style="color: green; font-weight: bold;">✅ Successfully enrolled!</p>
            <?php endif; ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($result)): ?>
                    <div style="background:#fff; padding: 15px; margin-bottom: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: flex; gap: 20px;">
                        <div style="flex: 0 0 200px;">
                            <img src="uploaded_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image" style="width: 100%; height: auto; border-radius: 8px;">
                        </div>
                        <div style="flex: 1;">
                            <h2 style="color:#333;"><?php echo htmlspecialchars($course['name']); ?></h2>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                           
                            <a href="episodeList.php?id=<?php echo $course['id']; ?>">Go to Course</a>

                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You haven't enrolled in any course yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>


<!-- ✅ Footer -->
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
                        <p>+88017**********<p>
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
            <p>All Right reserved by &copy;Learn Skill 2023</p>
        </div>
    </footer>
</section>

</body>
</html>
