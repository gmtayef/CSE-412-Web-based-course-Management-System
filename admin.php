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
    $instructor_id = mysqli_real_escape_string($conn, $_POST['instructor_id']);    
    $p_image = $_FILES['p_image']['name'];
    $p_image_tmp_name = $_FILES['p_image']['tmp_name'];
    $p_image_folder = 'uploaded_img/' . $p_image;

    $insert_query = mysqli_query($conn, "INSERT INTO `products`(instructor_id, name, price, image, description, start_time) VALUES('$instructor_id', '$p_name', '$p_price', '$p_image', '$p_description', '$p_start_time')");
    
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
        $message[] = 'Could not add the course. Verify database structure.';
    }
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_p_name = mysqli_real_escape_string($conn, $_POST['update_p_name']);
    $update_p_price = mysqli_real_escape_string($conn, $_POST['update_p_price']);
    $update_p_description = mysqli_real_escape_string($conn, $_POST['update_p_description']);
    $update_p_start_time = $_POST['update_p_start_time'];
    $update_instructor_id = mysqli_real_escape_string($conn, $_POST['update_instructor_id']);
    
    $update_query = mysqli_query($conn, "UPDATE `products` SET instructor_id = '$update_instructor_id', name = '$update_p_name', price = '$update_p_price', description = '$update_p_description', start_time = '$update_p_start_time' WHERE id = '$update_p_id'");
    
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] text-slate-800 selection:bg-indigo-500 selection:text-white flex min-h-screen flex-col md:flex-row overflow-hidden">
    <div class="md:hidden fixed top-0 left-0 right-0 h-16 bg-white/90 backdrop-blur-lg border-b border-slate-200 z-50 flex items-center justify-between px-6 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">B</div>
            <div class="font-extrabold text-xl tracking-tight text-slate-800">BOT <span class="text-indigo-600">Admin</span></div>
        </div>
        <button onclick="document.getElementById('mobile-menu').classList.toggle('-translate-x-full')" class="text-slate-600 hover:text-indigo-600 transition-colors bg-slate-100 p-2 rounded-lg">
            <i class="fas fa-bars text-lg"></i>
        </button>
    </div>
    <aside id="mobile-menu" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-50 w-72 bg-[#090e17] border-r border-slate-800/50 flex flex-col h-screen shadow-2xl md:shadow-none">
        <div class="flex items-center justify-between p-6 md:p-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">B</div>
                <div class="text-2xl font-extrabold tracking-tight text-white">
                    BOT <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Admin</span>
                </div>
            </div>
            <button onclick="document.getElementById('mobile-menu').classList.toggle('-translate-x-full')" class="md:hidden text-slate-400 hover:text-white p-2 bg-slate-800/50 rounded-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-5 flex-1 overflow-y-auto mt-2">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-3">Main Navigation</div>
            <nav class="space-y-2">
                <a href="admin_panel.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-grid-2 text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>
                <a href="admin.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25 border-l-[3px] border-indigo-300">
                    <i class="fas fa-layer-group text-lg w-8 text-indigo-100"></i>
                    <span class="font-semibold">Manage Courses</span>
                </a>               
                <a href="add_instructor.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-chalkboard-user text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-semibold">Manage Instructors</span>
                </a>
                <a href="index.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-globe text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-semibold">View Site</span>
                    <i class="fas fa-external-link-alt text-[10px] ml-auto opacity-40"></i>
                </a>
            </nav>
        </div>
        <div class="p-5 mt-auto">
            <div class="bg-gradient-to-b from-slate-800/40 to-slate-900/80 rounded-2xl p-4 border border-slate-800/50">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-slate-700 border-2 border-slate-600 flex items-center justify-center text-slate-300 font-bold shrink-0">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold text-slate-200 truncate"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                        <div class="text-xs text-indigo-400 font-medium">Administrator</div>
                    </div>
                </div>
                <a href="logout.php" class="flex items-center justify-center w-full py-2.5 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-all duration-300 font-bold text-sm gap-2 border border-rose-500/20 hover:border-rose-500">
                    <i class="fas fa-power-off"></i> Sign Out
                </a>
            </div>
        </div>
    </aside>
    <main class="flex-1 overflow-y-auto h-screen pt-20 md:pt-0">
        <div class="p-6 md:p-12 max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Manage Courses</h1>
                    <p class="text-slate-500 mt-2">Create new educational offerings and assign schedules.</p>
                </div>
            </div>
            <?php
            if (!empty($message)) {
                $bg_color = ($message_type === 'success') ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700';
                $icon = ($message_type === 'success') ? 'fa-circle-check text-emerald-500' : 'fa-circle-exclamation text-rose-500';
                foreach ($message as $msg) {
                    echo '
                    <div class="'.$bg_color.' border p-4 mb-8 rounded-2xl flex items-center shadow-sm" role="alert">
                        <i class="fas '.$icon.' text-xl mr-3"></i>
                        <span class="font-medium">'.htmlspecialchars($msg).'</span>
                    </div>';
                }
            }
            ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10 flex items-center gap-6">
                        <div class="h-16 w-16 bg-indigo-500 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3 class="text-slate-500 font-semibold mb-1 uppercase tracking-wider text-xs">Active Courses</h3>
                            <p class="text-4xl font-extrabold text-slate-900"><?php echo $total_courses_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10 flex items-center gap-6">
                        <div class="h-16 w-16 bg-emerald-500 text-white rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30">
                            <i class="fas fa-users-viewfinder"></i>
                        </div>
                        <div>
                            <h3 class="text-slate-500 font-semibold mb-1 uppercase tracking-wider text-xs">Total Instructors</h3>
                            <p class="text-4xl font-extrabold text-slate-900"><?php echo $total_instructors_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php 
            if(isset($_GET['edit'])): 
                $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
                $edit_query = mysqli_query($conn, "SELECT p.*, ca.instructor_id as current_instructor FROM `products` p LEFT JOIN `course_activity` ca ON p.id = ca.course_id WHERE p.id = '$edit_id'");
                if(mysqli_num_rows($edit_query) > 0):
                    $fetch_edit = mysqli_fetch_assoc($edit_query);
            ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-10 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                <h2 class="text-2xl font-bold text-slate-800 mb-8 flex items-center">
                    <span class="bg-amber-100 p-2 rounded-lg mr-3"><i class="fas fa-pen-to-square text-amber-600"></i></span>
                    Update Course: <?php echo htmlspecialchars($fetch_edit['name']); ?>
                </h2>                
                <form action="admin.php" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="update_p_id" value="<?php echo $fetch_edit['id']; ?>">                   
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600 ml-1">Course Name</label>
                            <input type="text" name="update_p_name" value="<?php echo htmlspecialchars($fetch_edit['name']); ?>" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all duration-200" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600 ml-1">Price (৳)</label>
                            <input type="number" name="update_p_price" min="0" value="<?php echo htmlspecialchars($fetch_edit['price']); ?>" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all duration-200" required>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Description</label>
                        <textarea name="update_p_description" rows="3" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all duration-200" required><?php echo htmlspecialchars($fetch_edit['description']); ?></textarea>
                    </div>                   
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600 ml-1">Start Time</label>
                            <input type="datetime-local" name="update_p_start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($fetch_edit['start_time'])); ?>" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600 ml-1">Assign Instructor</label>
                            <select name="update_instructor_id" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer" required>
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
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Course Thumbnail (Optional)</label>
                        <div class="flex items-center gap-6 p-4 border border-dashed border-slate-300 rounded-2xl bg-slate-50">
                            <div class="shrink-0 h-20 w-20 rounded-xl overflow-hidden border-2 border-white shadow-md relative group">
                                <img src="uploaded_img/<?php echo htmlspecialchars($fetch_edit['image']); ?>" class="h-full w-full object-cover">
                            </div>
                            <input type="file" name="update_p_image" accept="image/png, image/jpg, image/jpeg" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-white file:border file:border-slate-200 file:text-slate-700 hover:file:bg-slate-100 transition-all cursor-pointer">
                        </div>
                    </div>
                    <div class="flex gap-4 pt-4">
                        <button type="submit" name="update_product" class="flex-1 bg-amber-500 text-white font-bold py-3.5 rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all duration-200 hover:-translate-y-0.5">Save Changes</button>
                        <a href="admin.php" class="flex-1 bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl hover:bg-slate-300 transition-all text-center flex items-center justify-center hover:-translate-y-0.5">Cancel Edit</a>
                    </div>
                </form>
            </div>
            <?php 
                endif; 
            else: 
            ?>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-10 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                <h2 class="text-2xl font-bold text-slate-800 mb-8 flex items-center">
                    <span class="bg-indigo-100 p-2 rounded-lg mr-3"><i class="fas fa-layer-group text-indigo-600"></i></span>
                    Publish New Course
                </h2>               
                <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600 ml-1">Course Title</label>
                            <input type="text" name="p_name" placeholder="E.g., Advanced PHP Development" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-600 ml-1">Price (৳)</label>
                            <input type="number" name="p_price" min="0" placeholder="0 for Free" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Course Details</label>
                        <textarea name="p_description" rows="3" placeholder="What will students learn?" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required></textarea>
                    </div>                   
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-indigo-50/30 p-6 rounded-2xl border border-indigo-50/50">
                        <div class="space-y-2">
                            <label for="p_start_time" class="text-sm font-semibold text-slate-600 ml-1">Starts At</label>
                            <input type="datetime-local" id="p_start_time" name="p_start_time" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer">
                        </div>
                        <div class="space-y-2">
                            <label for="instructor_id" class="text-sm font-semibold text-slate-600 ml-1">Lead Instructor</label>
                            <select id="instructor_id" name="instructor_id" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer" required>
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
                    <div class="space-y-2">
                        <label for="p_image" class="text-sm font-semibold text-slate-600 ml-1">Cover Image</label>
                        <input type="file" id="p_image" name="p_image" accept="image/png, image/jpg, image/jpeg" class="w-full text-sm text-slate-500 file:mr-4 file:py-3.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all cursor-pointer bg-slate-50 border border-slate-200 rounded-xl" required>
                    </div>
                    <div class="pt-2">
                        <button type="submit" name="add_product" class="w-full md:w-auto px-10 bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-600/30 transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-arrow-up"></i> Publish Course
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>           
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Course Directory</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 font-semibold tracking-wider">
                            <tr>
                                <th scope="col" class="px-8 py-5">Course Content</th>
                                <th scope="col" class="px-8 py-5">Instructor</th>
                                <th scope="col" class="px-8 py-5">Pricing</th>
                                <th scope="col" class="px-8 py-5">Timeline</th>
                                <th scope="col" class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $select_products = mysqli_query($conn, "
                                SELECT p.*, i.name as instructor_name 
                                FROM `products` p
                                LEFT JOIN `course_activity` ca ON p.id = ca.course_id
                                LEFT JOIN `instructors` i ON ca.instructor_id = i.id
                                ORDER BY p.id DESC
                            ");
                            if($select_products && mysqli_num_rows($select_products) > 0){
                               while($row = mysqli_fetch_assoc($select_products)){
                            ?>
                            <tr class="bg-white hover:bg-slate-50/80 transition-colors duration-200 group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-14 w-14 rounded-xl overflow-hidden border border-slate-200 shadow-sm shrink-0">
                                            <img src="uploaded_img/<?php echo htmlspecialchars($row['image'] ?? ''); ?>" class="h-full w-full object-cover">
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block text-base"><?php echo htmlspecialchars($row['name'] ?? ''); ?></span>
                                            <span class="text-xs text-slate-500 truncate w-48 block mt-1"><?php echo htmlspecialchars($row['description'] ?? ''); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <?php 
                                        if(!empty($row['instructor_name'])) {
                                            echo '<div class="flex items-center gap-2 font-medium text-slate-700"><i class="fas fa-user-tie text-indigo-400"></i> '.htmlspecialchars($row['instructor_name']).'</div>';
                                        } else {
                                            echo '<span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700"><i class="fas fa-circle-exclamation mr-1.5"></i> Unassigned</span>';
                                        }
                                    ?>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-sm border border-emerald-100">
                                        ৳<?php echo htmlspecialchars($row['price'] ?? '0'); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-left">
                                    <div class="relative flex flex-col pl-2 py-1">
                                        <div class="relative flex items-start gap-3">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-50 z-10 mt-1"></div>
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 leading-none mb-1">Start Date</span>
                                                <span class="text-xs font-bold text-slate-700 whitespace-nowrap">
                                                    <?php echo !empty($row['start_time']) ? date('M d, Y', strtotime($row['start_time'])) . ' <span class="text-slate-400 font-medium ml-1">' . date('h:i A', strtotime($row['start_time'])) . '</span>' : 'Not Set'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right space-x-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="admin.php?edit=<?php echo $row['id']; ?>" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit Course">
                                        <i class="fas fa-pen text-sm"></i>
                                    </a>
                                    <a href="admin.php?delete=<?php echo $row['id']; ?>" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all shadow-sm" title="Delete Course" onclick="return confirm('Delete this course permanently?');">
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                               };
                            } else {
                               echo "<tr><td colspan='5' class='text-center py-12 text-slate-500 bg-slate-50/50 rounded-b-3xl'><div class='flex flex-col items-center'><i class='fas fa-box-open text-4xl mb-3 text-slate-300'></i><p class='font-medium'>No courses available. Create one above.</p></div></td></tr>";
                            };
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
