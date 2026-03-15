<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = [];
$message_type = 'error';
$course_id = $_GET['id'] ?? 0;

if ($course_id == 0) {
    header('location:admin.php');
    exit();
}

// Fetch existing course data
$select_course = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$course_id'");
$course = mysqli_fetch_assoc($select_course);

if (!$course) {
    header('location:admin.php');
    exit();
}

if (isset($_POST['update_product'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_price = mysqli_real_escape_string($conn, $_POST['p_price']);
    $p_description = mysqli_real_escape_string($conn, $_POST['p_description']);
    $p_start_time = $_POST['p_start_time'];
    $p_end_time = $_POST['p_end_time'];

    if(strtotime($p_start_time) >= strtotime($p_end_time)){
        $message[] = 'Error: The course end time must be after the start time.';
    } else {
        // Check for scheduling conflicts, excluding the current course
        $conflict_query = "SELECT * FROM `products` WHERE ('$p_start_time' < end_time AND '$p_end_time' > start_time) AND id != '$course_id'";
        $conflict_result = mysqli_query($conn, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $message[] = 'Error: Updated schedule conflicts with another course.';
        } else {
            $update_query = "UPDATE `products` SET name='$p_name', price='$p_price', description='$p_description', start_time='$p_start_time', end_time='$p_end_time' WHERE id='$course_id'";
            
            if (mysqli_query($conn, $update_query)) {
                $message[] = 'Course updated successfully!';
                $message_type = 'success';
                // Refresh course data to show updated values in the form
                $select_course = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$course_id'");
                $course = mysqli_fetch_assoc($select_course);
            } else {
                $message[] = 'Could not update the course.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 md:p-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Course</h1>

        <?php
        if (!empty($message)) {
            $bg_color = ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            foreach ($message as $msg) {
                echo '<div class="mb-6 p-4 rounded-lg ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
            }
        }
        ?>

        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Course Name</label>
                    <input type="text" name="p_name" value="<?php echo htmlspecialchars($course['name']); ?>" class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="p_price" min="0" value="<?php echo htmlspecialchars($course['price']); ?>" class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="p_description" class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="datetime-local" name="p_start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($course['start_time'])); ?>" class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="datetime-local" name="p_end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($course['end_time'])); ?>" class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button type="submit" name="update_product" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition">Update Course</button>
                    <a href="admin.php" class="text-gray-600 hover:text-gray-900">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
