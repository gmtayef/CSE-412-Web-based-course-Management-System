<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}


$user_id = intval($_SESSION['id'] ?? $_SESSION['user_id'] ?? 0);


$enrollments_query = "SELECT products.* FROM enrollments 
                      JOIN products ON enrollments.course_id = products.id 
                      WHERE enrollments.user_id = '$user_id'";
                      
$purchased_courses_result = mysqli_query($conn, $enrollments_query);
$has_purchased_courses = mysqli_num_rows($purchased_courses_result) > 0;


$cart_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id = '$user_id'");
$cart_count_data = mysqli_fetch_assoc($cart_count_query);
$cart_count = $cart_count_data['count'] ?? 0;


$assignment_count = 0; 

$assignment_query_sql = "
    SELECT COUNT(*) as count FROM assignments 
    WHERE course_id IN (SELECT course_id FROM enrollments WHERE user_id = '$user_id') 
    AND id NOT IN (SELECT assignment_id FROM submissions WHERE user_id = '$user_id')
";
$assignment_query = mysqli_query($conn, $assignment_query_sql);

if ($assignment_query) {
    $assignment_data = mysqli_fetch_assoc($assignment_query);
    $assignment_count = $assignment_data['count'] ?? 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Panel - BOT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
    </style>
</head>
<body class="bg-[#0f172a] text-white flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#1e293b] flex flex-col border-r border-slate-800 shadow-xl z-10 hidden md:flex">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-2xl font-bold tracking-tight">BOT <span class="text-[#6366f1]">Student</span></h2>
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="student_panel.php" class="flex items-center gap-3 px-4 py-3 bg-[#6366f1] text-white rounded-lg font-medium shadow-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                My Enrolled Courses
            </a>
            
            <a href="explore_courses.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Explore Courses
            </a>

            <a href="assignments.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    My Assignments
                </div>
                <?php if($assignment_count > 0): ?>
                    <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $assignment_count; ?></span>
                <?php endif; ?>
            </a>

            <a href="cart.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    My Cart
                </div>
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
        
        <?php if (isset($_GET['checkout']) && $_GET['checkout'] == 'success'): ?>
        <div class="bg-green-600/20 border border-green-500 text-green-400 p-4 rounded mb-6">
            Payment successful! You have been enrolled in your new courses.
        </div>
        <?php endif; ?>

        <header class="mb-10 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome back, <span class="text-[#818cf8]"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!</h1>
                <p class="text-slate-400">Continue your learning journey. Your purchased courses are listed below.</p>
            </div>
            
            <a href="cart.php" class="bg-[#1e293b] hover:bg-slate-700 border border-slate-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2 max-w-max">
                Go to Cart
                <?php if($cart_count > 0): ?>
                    <span class="bg-[#6366f1] text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </header>

        <?php if($assignment_count > 0): ?>
        <div class="bg-yellow-500/10 border border-yellow-500/50 rounded-xl p-4 mb-8 flex justify-between items-center">
            <div class="flex items-center gap-3 text-yellow-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-medium">You have <?php echo $assignment_count; ?> pending assignment(s) to submit!</span>
            </div>
            <a href="assignments.php" class="bg-yellow-500 hover:bg-yellow-600 text-slate-900 font-bold px-4 py-2 rounded transition-colors text-sm">View Assignments</a>
        </div>
        <?php endif; ?>

        <?php if (!$has_purchased_courses): ?>
            <div class="bg-[#1e293b] rounded-xl p-16 flex flex-col items-center justify-center text-center shadow-sm border border-slate-800">
                <p class="text-slate-300 mb-4 font-medium">You have not purchased any courses yet.</p>
                <div class="flex gap-4">
                    <a href="explore_courses.php" class="text-[#818cf8] hover:text-[#a5b4fc] transition-colors font-medium border border-[#818cf8] px-4 py-2 rounded">
                        Explore Courses &rarr;
                    </a>
                    <?php if($cart_count > 0): ?>
                    <a href="cart.php" class="bg-[#6366f1] text-white hover:bg-[#4f46e5] transition-colors font-medium px-4 py-2 rounded">
                        Checkout Cart
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while($course = mysqli_fetch_assoc($purchased_courses_result)): ?>
                <div class="bg-[#1e293b] rounded-xl shadow-lg overflow-hidden border border-slate-700">
                    <img class="h-48 w-full object-cover" src="uploaded_img/<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($course['name']); ?></h3>
                        <p class="text-slate-400 mb-4 text-sm line-clamp-2">Start learning and tracking your progress now!</p>
                        <a href="course_player.php?id=<?php echo $course['id']; ?>" class="block text-center bg-[#6366f1] hover:bg-[#4f46e5] text-white font-medium py-2 rounded transition-colors mb-2">
                            Continue Learning
                        </a>
                        <a href="assignments.php?course_id=<?php echo $course['id']; ?>" class="block text-center bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 rounded transition-colors text-sm">
                            View Assignments
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>