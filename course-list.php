<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location: login_form.php');
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course List - Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="list-container">
        <h2>Course List</h2>
        <a href="add-course.php" class="btn">+ Add New Course</a>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Course Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><img src="uploaded_img/<?php echo $row['image']; ?>" height="50"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>৳<?php echo $row['price']; ?></td>
                    <td>
                        <a href="edit-course.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                        <a href="delete-course.php?id=<?php echo $row['id']; ?>" class="btn delete" onclick="return confirm('Are you sure to delete?')">Delete</a>
                        <a href="episodeUpload.php?id=<?php echo $row['id']; ?>" class="btn">Upload Episode</a>
                        <a href="episodeList.php?id=<?php echo $row['id']; ?>" class="btn">View Episodes</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
