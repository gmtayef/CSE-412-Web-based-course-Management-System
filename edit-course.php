<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location: login_form.php');
    exit();
}

$id = $_GET['id'];
$success = '';
$error = '';

$result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
$course = mysqli_fetch_assoc($result);

if (isset($_POST['update_course'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];

    $sql = "UPDATE products SET name='$name', description='$description', price='$price' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        $success = "Course updated successfully!";
        $course['name'] = $name;
        $course['description'] = $description;
        $course['price'] = $price;
    } else {
        $error = "Failed to update course!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Course</h2>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form action="" method="POST">
            <label>Course Name:</label>
            <input type="text" name="name" value="<?php echo $course['name']; ?>" required>

            <label>Description:</label>
            <textarea name="description" rows="4" required><?php echo $course['description']; ?></textarea>

            <label>Price:</label>
            <input type="number" name="price" value="<?php echo $course['price']; ?>" required>

            <input type="submit" name="update_course" value="Update Course">
        </form>

        <a href="course-list.php" class="btn">&larr; Back to Course List</a>
    </div>
</body>
</html>