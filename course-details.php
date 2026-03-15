<?php
// course-details.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

if (!isset($_GET['id'])) {
    die("Course ID is required.");
}

$course_id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE id = $course_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Course not found.");
}
$course = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $course['name']; ?> - Details</title>
    <link rel="stylesheet" href="css/course-page.css">
</head>
<body>
<header>
    <h1 class="logo">Learn <span class="span-one">Skill</span></h1>
</header>
<section class="course-details">
    <img src="uploaded_img/<?php echo $course['image']; ?>" alt="Course Image">
    <h2><?php echo $course['name']; ?></h2>
    <p><?php echo $course['description']; ?></p>
    <p>Price: <?php echo $course['price'] == 0 ? 'Free' : '৳' . $course['price']; ?></p>
    <?php if ($course['price'] == 0): ?>
        <form action="enroll.php" method="post">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <button type="submit">Enroll Now</button>
        </form>
    <?php else: ?>
        <form action="add_to_cart.php" method="post">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <button type="submit">Add to Cart</button>
        </form>
    <?php endif; ?>
</section>
</body>
</html>

<?php