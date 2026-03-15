<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = [];
$message_type = 'error';


if (isset($_POST['add_product'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $p_price = mysqli_real_escape_string($conn, $_POST['p_price']);
    $p_description = mysqli_real_escape_string($conn, $_POST['p_description']);
    $p_start_time = $_POST['p_start_time'];
    $p_end_time = $_POST['p_end_time'];
    $instructor_id = mysqli_real_escape_string($conn, $_POST['instructor_id']); 
    
    $p_image = $_FILES['p_image']['name'];
    $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
    $p_image_folder = 'uploaded_img/' . $p_image;

    if(!empty($p_start_time) && !empty($p_end_time) && strtotime($p_start_time) >= strtotime($p_end_time)){
        $message[] = 'Error: The course end time must be after the start time.';
    } else {
        $conflict_query = "SELECT * FROM `products` WHERE ('$p_start_time' < end_time AND '$p_end_time' > start_time)";
        $conflict_result = mysqli_query($conn, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $message[] = 'Error: Course schedule conflicts with an existing course.';
        } else {
         
            $insert_query = mysqli_query($conn, "INSERT INTO `products`(instructor_id, name, price, image, description, start_time, end_time) VALUES('$instructor_id', '$p_name', '$p_price', '$p_image', '$p_description', '$p_start_time', '$p_end_time')");

            if ($insert_query) {
                $course_id = mysqli_insert_id($conn); 
                
                
                if(!empty($instructor_id)){
                    $assign_query = "INSERT INTO `course_activity` (instructor_id, course_id, assigned_date) VALUES ('$instructor_id', '$course_id', NOW())";
                    mysqli_query($conn, $assign_query);
                }

                move_uploaded_file($p_image_tmp_name, $p_image_folder);
                $message[] = 'Course added successfully and instructor assigned.';
                $message_type = 'success';
            } else {
                $message[] = 'Could not add the course. (Check if you ran the ALTER TABLE products ADD instructor_id query!)';
            }
        }
    }
}


if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_p_name = mysqli_real_escape_string($conn, $_POST['update_p_name']);
    $update_p_price = mysqli_real_escape_string($conn, $_POST['update_p_price']);
    $update_p_description = mysqli_real_escape_string($conn, $_POST['update_p_description']);
    $update_p_start_time = $_POST['update_p_start_time'];
    $update_p_end_time = $_POST['update_p_end_time'];
    $update_instructor_id = mysqli_real_escape_string($conn, $_POST['update_instructor_id']);

    if(!empty($update_p_start_time) && !empty($update_p_end_time) && strtotime($update_p_start_time) >= strtotime($update_p_end_time)){
        $message[] = 'Error: The course end time must be after the start time.';
        $message_type = 'error';
    } else {
     
        $update_query = mysqli_query($conn, "UPDATE `products` SET instructor_id = '$update_instructor_id', name = '$update_p_name', price = '$update_p_price', description = '$update_p_description', start_time = '$update_p_start_time', end_time = '$update_p_end_time' WHERE id = '$update_p_id'");

        if($update_query){
            
            $check_assignment = mysqli_query($conn, "SELECT * FROM `course_activity` WHERE course_id = '$update_p_id'");
            if(mysqli_num_rows($check_assignment) > 0){
                mysqli_query($conn, "UPDATE `course_activity` SET instructor_id = '$update_instructor_id' WHERE course_id = '$update_p_id'");
            } else {
                mysqli_query($conn, "INSERT INTO `course_activity` (instructor_id, course_id, assigned_date) VALUES ('$update_instructor_id', '$update_p_id', NOW())");
            }

     
            $update_p_image = $_FILES['update_p_image']['name'];
            $update_p_image_tmp_name = $_FILES['update_p_image']['tmp_name'];
            $update_p_image_folder = 'uploaded_img/'.$update_p_image;

            if(!empty($update_p_image)){
                mysqli_query($conn, "UPDATE `products` SET image = '$update_p_image' WHERE id = '$update_p_id'");
                move_uploaded_file($update_p_image_tmp_name, $update_p_image_folder);
            }

            $message[] = 'Course updated successfully!';
            $message_type = 'success';
        } else {
            $message[] = 'Course could not be updated!';
            $message_type = 'error';
        }
    }
}


if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_query = mysqli_query($conn, "DELETE FROM `products` WHERE id = $delete_id ");
   mysqli_query($conn, "DELETE FROM `course_activity` WHERE course_id = $delete_id ");

   if($delete_query){
      $message[] = 'Course has been deleted successfully.';
      $message_type = 'success';
   }else{
      $message[] = 'Course could not be deleted.';
   }
}


$total_courses = mysqli_query($conn, "SELECT COUNT(*) as count FROM `products`");
$total_courses_count = mysqli_fetch_assoc($total_courses)['count'] ?? 0;
$total_instructors = mysqli_query($conn, "SELECT COUNT(*) as count FROM `instructors`");
$total_instructors_count = mysqli_fetch_assoc($total_instructors)['count'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                BOT <span class="text-indigo-400">Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin_panel.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="admin.php" class="flex items-center px-4 py-2 rounded-lg bg-gray-700 text-white"><i class="fas fa-book mr-3"></i>Manage Courses</a>
                <a   <a href="add_instructor.php" class="flex items-center px-4 py-2 rounded-lg bg-gray-700"><i class="fas fa-user-plus mr-3"></i>Manage Instructors</a>
                <a href="index.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-home mr-3"></i>View Site</a>
                <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Courses</h1>

            <?php
            if (!empty($message)) {
                $bg_color = ($message_type === 'success') ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400';
                foreach ($message as $msg) {
                    echo '<div class="mb-6 p-4 rounded-lg ' . $bg_color . '">' . htmlspecialchars($msg) . '</div>';
                }
            }
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-indigo-500">
                    <h3 class="text-gray-500 text-sm font-medium">Total Courses</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_courses_count; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                    <h3 class="text-gray-500 text-sm font-medium">Total Instructors</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $total_instructors_count; ?></p>
                </div>
            </div>

            <?php 
            if(isset($_GET['edit'])): 
                $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
                $edit_query = mysqli_query($conn, "SELECT p.*, ca.instructor_id as current_instructor FROM `products` p LEFT JOIN `course_activity` ca ON p.id = ca.course_id WHERE p.id = '$edit_id'");
                if(mysqli_num_rows($edit_query) > 0):
                    $fetch_edit = mysqli_fetch_assoc($edit_query);
            ?>
            <div class="bg-white rounded-lg shadow-md p-6 md:p-8 mb-8 border-t-4 border-yellow-400">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Update Course: <?php echo htmlspecialchars($fetch_edit['name']); ?></h2>
                <form action="admin.php" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">
                    
                    <input type="text" name="update_p_name" value="<?php echo htmlspecialchars($fetch_edit['name']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    <input type="number" name="update_p_price" min="0" value="<?php echo htmlspecialchars($fetch_edit['price']); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    <textarea name="update_p_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required><?php echo htmlspecialchars($fetch_edit['description']); ?></textarea>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="datetime-local" name="update_p_start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['start_time'])); ?>" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" name="update_p_end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['end_time'])); ?>" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assign Instructor</label>
                            <select name="update_instructor_id" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                                <option value="" disabled>Select an instructor...</option>
                                <?php
                                $instructor_query = mysqli_query($conn, "SELECT id, name FROM instructors");
                                if($instructor_query && mysqli_num_rows($instructor_query) > 0){
                                    while($inst = mysqli_fetch_assoc($instructor_query)){
                                       
                                        $selected = ($inst['id'] == $fetch_edit['current_instructor']) ? 'selected' : '';
                                        echo '<option value="' . $inst['id'] . '" ' . $selected . '>' . htmlspecialchars($inst['name']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Course Image (Leave blank to keep current image)</label>
                        <div class="flex items-center mt-2 gap-4">
                            <img src="uploaded_img/<?php echo htmlspecialchars($fetch_edit['image']); ?>" class="h-16 w-16 object-cover rounded shadow-sm border">
                            <input type="file" name="update_p_image" accept="image/png, image/jpg, image/jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" name="update_product" class="flex-1 bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition">Save Changes</button>
                        <a href="admin.php" class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-lg hover:bg-gray-500 transition text-center flex items-center justify-center">Cancel Edit</a>
                    </div>
                </form>
            </div>
            <?php 
                endif; 
            else: 
            ?>
            <div class="bg-white rounded-lg shadow-md p-6 md:p-8 mb-8 border-t-4 border-indigo-500">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Add a New Course</h2>
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="text" name="p_name" placeholder="Enter course name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    <input type="number" name="p_price" min="0" placeholder="Enter course price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    <textarea name="p_description" placeholder="Enter course description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required></textarea>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="p_start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="datetime-local" id="p_start_time" name="p_start_time" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="p_end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" id="p_end_time" name="p_end_time" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="instructor_id" class="block text-sm font-medium text-gray-700">Assign Instructor</label>
                            <select id="instructor_id" name="instructor_id" class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                                <option value="" disabled selected>Select an instructor...</option>
                                <?php
                                $instructor_query = mysqli_query($conn, "SELECT id, name FROM instructors");
                                if($instructor_query && mysqli_num_rows($instructor_query) > 0){
                                    while($inst = mysqli_fetch_assoc($instructor_query)){
                                        echo '<option value="' . $inst['id'] . '">' . htmlspecialchars($inst['name']) . '</option>';
                                    }
                                } else {
                                    echo '<option value="" disabled>No instructors available.</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="p_image" class="block text-sm font-medium text-gray-700">Course Image</label>
                        <input type="file" id="p_image" name="p_image" accept="image/png, image/jpg, image/jpeg" class="w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                    </div>

                    <button type="submit" name="add_product" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition">Add Course & Assign</button>
                </form>
            </div>
            <?php endif; ?>
            
            <div class="bg-white rounded-lg shadow-md p-6 md:p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Course Directory</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th scope="col" class="px-6 py-3">Course</th>
                                <th scope="col" class="px-6 py-3">Instructor</th>
                                <th scope="col" class="px-6 py-3">Price</th>
                                <th scope="col" class="px-6 py-3">Schedule</th>
                                <th scope="col" class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $select_products = mysqli_query($conn, "
                                SELECT p.*, i.name as instructor_name 
                                FROM `products` p
                                LEFT JOIN `course_activity` ca ON p.id = ca.course_id
                                LEFT JOIN `instructors` i ON ca.instructor_id = i.id
                            ");

                            if($select_products && mysqli_num_rows($select_products) > 0){
                               while($row = mysqli_fetch_assoc($select_products)){
                            ?>
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 flex items-center">
                                    <img src="uploaded_img/<?php echo htmlspecialchars($row['image'] ?? ''); ?>" class="h-10 w-10 rounded-full mr-4 object-cover border" alt="">
                                    <span><?php echo htmlspecialchars($row['name'] ?? ''); ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        echo !empty($row['instructor_name']) ? htmlspecialchars($row['instructor_name']) : '<span class="text-red-500 text-xs font-bold uppercase tracking-wide bg-red-100 px-2 py-1 rounded">Not Assigned</span>'; 
                                    ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-indigo-600">৳<?php echo htmlspecialchars($row['price'] ?? '0'); ?></td>
                                
                                <td class="px-6 py-4 text-xs text-gray-500 space-y-1">
                                    <div><span class="font-semibold text-gray-700">Start:</span> <?php echo !empty($row['start_time']) ? date('M d, Y - h:i A', strtotime($row['start_time'])) : 'N/A'; ?></div>
                                    <div><span class="font-semibold text-gray-700">End:</span> <?php echo !empty($row['end_time']) ? date('M d, Y - h:i A', strtotime($row['end_time'])) : 'N/A'; ?></div>
                                </td>

                                <td class="px-6 py-4 text-center space-x-2">
                                    <a href="admin.php?edit=<?php echo $row['id']; ?>" class="text-indigo-500 hover:text-indigo-700 p-2" title="Edit Course"><i class="fas fa-edit text-lg"></i></a>
                                    <a href="admin.php?delete=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 p-2" title="Delete Course" onclick="return confirm('Are you sure you want to delete this course?');"><i class="fas fa-trash text-lg"></i></a>
                                </td>
                            </tr>
                            <?php
                               };
                            } else {
                               echo "<tr><td colspan='5' class='text-center py-8 text-gray-500 text-lg'>No courses added yet.</td></tr>";
                            };
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>