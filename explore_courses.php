<?php
include 'config.php';
session_start();

// Safely get the user ID
$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;

if (!isset($_SESSION['user_name']) || $user_id === null) {
    header('location: login_form.php');
    exit();
}

// Handle Add to Cart action
if (isset($_POST['add_to_cart'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id' AND course_id = '$course_id'");
    
    if (mysqli_num_rows($check_cart) == 0) {
        mysqli_query($conn, "INSERT INTO cart (user_id, course_id) VALUES ('$user_id', '$course_id')");
        $success_msg = "Course added to cart!";
    } else {
        $error_msg = "Course is already in your cart.";
    }
}

// Fetch available courses
$courses_query = mysqli_query($conn, "SELECT * FROM products");

// Fetch user's cart items
$cart_items = [];
$cart_query = mysqli_query($conn, "SELECT course_id FROM cart WHERE user_id = '$user_id'");
if($cart_query) {
    while($row = mysqli_fetch_assoc($cart_query)) $cart_items[] = $row['course_id'];
}

// Fetch user's enrolled courses
$enrolled_items = [];
$enrolled_query = mysqli_query($conn, "SELECT course_id FROM enrollments WHERE user_id = '$user_id'");
if($enrolled_query) {
    while($row = mysqli_fetch_assoc($enrolled_query)) $enrolled_items[] = $row['course_id'];
}

$cart_count = count($cart_items);

// FIXED: Defined assignment count to prevent the sidebar from crashing
$assignment_count = 0; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Courses - BOT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#0f172a] text-white flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#1e293b] flex flex-col border-r border-slate-800 shadow-xl z-10 hidden md:flex">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-2xl font-bold tracking-tight">BOT <span class="text-[#6366f1]">Student</span></h2>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="student_panel.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors">
                My Enrolled Courses
            </a>
            <a href="explore_courses.php" class="flex items-center gap-3 px-4 py-3 bg-[#6366f1] text-white rounded-lg font-medium shadow-sm transition-colors">
                Explore Courses
            </a>
            <a href="assignments.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors justify-between">
                My Assignments
                <?php if($assignment_count > 0): ?>
                    <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $assignment_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="cart.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors justify-between">
                My Cart
                <?php if($cart_count > 0): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-slate-800 hover:text-red-300 rounded-lg font-medium transition-colors mt-auto">
                Logout
            </a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <?php if(isset($success_msg)): ?>
            <div class="bg-green-600/20 border border-green-500 text-green-400 p-4 rounded-lg mb-6 flex justify-between items-center">
                <?php echo $success_msg; ?>
                <a href="cart.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition-colors">View Cart</a>
            </div>
        <?php endif; ?>
        <?php if(isset($error_msg)): ?>
            <div class="bg-red-600/20 border border-red-500 text-red-400 p-4 rounded-lg mb-6"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <header class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold mb-2">Explore <span class="text-[#818cf8]">Courses</span></h1>
                <p class="text-slate-400">Discover new skills and level up your career.</p>
            </div>
            <a href="cart.php" class="bg-[#1e293b] border border-slate-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                Go to Cart
                <?php if($cart_count > 0): ?>
                    <span class="bg-[#6366f1] text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php 
            if($courses_query && mysqli_num_rows($courses_query) > 0) {
                while($course = mysqli_fetch_assoc($courses_query)): 
                    $is_enrolled = in_array($course['id'], $enrolled_items);
                    $is_in_cart = in_array($course['id'], $cart_items);
            ?>
            <div class="bg-[#1e293b] rounded-xl shadow-lg overflow-hidden border border-slate-700 flex flex-col hover:border-slate-600 transition-colors">
                <img class="h-48 w-full object-cover" src="uploaded_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image">
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="text-lg font-bold text-white mb-2 line-clamp-1"><?php echo htmlspecialchars($course['name']); ?></h3>
                    <p class="text-[#818cf8] font-bold text-xl mb-4">৳<?php echo number_format($course['price'], 2); ?></p>
                    <div class="mt-auto">
                        <?php if($is_enrolled): ?>
                            <button disabled class="w-full bg-slate-700 text-slate-400 font-medium py-2 rounded cursor-not-allowed">Already Enrolled</button>
                        <?php elseif($is_in_cart): ?>
                            <a href="cart.php" class="block w-full text-center bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 rounded transition-colors">View in Cart</a>
                        <?php else: ?>
                            <form method="post">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <button type="submit" name="add_to_cart" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-medium py-2 rounded transition-colors">Add to Cart</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; } else { echo '<div class="col-span-full text-slate-400">No courses available.</div>'; } ?>
        </div>
    </main>
</body>
</html>