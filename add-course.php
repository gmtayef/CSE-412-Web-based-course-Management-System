<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location: login_form.php');
    exit();
}

$success = '';
$error = '';

if (isset($_POST['add_course'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    $check = mysqli_query($conn, "SELECT * FROM products WHERE name='$name'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Course name already exists!";
    } else {
        if (move_uploaded_file($image_tmp, $image_folder)) {
            $sql = "INSERT INTO products(name, description, price, image) VALUES('$name', '$description', '$price', '$image')";
            if (mysqli_query($conn, $sql)) {
                $success = "Course added successfully!";
            } else {
                $error = "Failed to add course!";
            }
        } else {
            $error = "Failed to upload image!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Add New Course</h2>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label>Course Name:</label>
            <input type="text" name="name" required>

            <label>Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Price:</label>
            <input type="number" name="price" required>

            <label>Upload Image:</label>
            <input type="file" name="image" accept="image/*" required>

            <input type="submit" name="add_course" value="Add Course">
        </form>

        <a href="course-list.php" class="btn">&larr; Back to Course List</a>
    </div>
</body>
</html>