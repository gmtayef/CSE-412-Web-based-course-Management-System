<?php
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
@include 'config.php';
session_start();

$student_name = $_SESSION['user_name'];
$student_email = $_SESSION['user_email'] ?? '';
$session_id = intval($_SESSION['id'] ?? $_SESSION['user_id'] ?? 0);

if (!isset($_SESSION['user_name'])) {
    header('location: login_form.php');
    exit();
}

$valid_ids = [];
if ($session_id > 0) {
    $valid_ids[] = $session_id;
}

$query_condition = !empty($student_email) ? "email = '$student_email'" : "name = '$student_name'";

$check_user = @mysqli_query($conn, "SELECT id FROM user_form WHERE $query_condition");
if ($check_user && mysqli_num_rows($check_user) > 0) {
    while ($row = mysqli_fetch_assoc($check_user)) {
        $valid_ids[] = $row['id'];
    }
}

$check_old = @mysqli_query($conn, "SELECT id FROM students WHERE $query_condition");
if ($check_old && mysqli_num_rows($check_old) > 0) {
    while ($row = mysqli_fetch_assoc($check_old)) {
        $valid_ids[] = $row['id'];
    }
}

$valid_ids = array_values(array_unique(array_filter($valid_ids)));
if (empty($valid_ids)) {
    $valid_ids[] = $session_id > 0 ? $session_id : 0; 
}
$id_list = implode("','", $valid_ids);
$primary_user_id = $valid_ids[0];

$cart_count_query = @mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id IN ('$id_list')");
$cart_count_data = $cart_count_query ? mysqli_fetch_assoc($cart_count_query) : ['count' => 0];
$cart_count = $cart_count_data['count'] ?? 0;

if (isset($_POST['add_to_cart'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id IN ('$id_list') AND course_id = '$course_id'");
    if ($check_cart && mysqli_num_rows($check_cart) == 0) {
        mysqli_query($conn, "INSERT INTO cart (user_id, course_id) VALUES ('$primary_user_id', '$course_id')");
        $success_msg = "Course added to cart!";
        $cart_count++; 
    } else {
        $error_msg = "Course is already in your cart.";
    }
}

$courses_query = @mysqli_query($conn, "SELECT * FROM products");
$cart_items = [];
$cart_query = @mysqli_query($conn, "SELECT course_id FROM cart WHERE user_id IN ('$id_list')");
if($cart_query) {
    while($row = mysqli_fetch_assoc($cart_query)) {
        $cart_items[] = $row['course_id'];
    }
}

$enrolled_items = [];
$enroll_query = @mysqli_query($conn, "SELECT course_id FROM enrollments WHERE user_id IN ('$id_list')");
if($enroll_query) {
    while($row = mysqli_fetch_assoc($enroll_query)) {
        $enrolled_items[] = $row['course_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Courses | BOT STUDENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        
        .refined-menu { width: 240px; font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        .refined-menu h3 { font-size: 0.75rem; font-weight: 700; margin: 16px 0 8px 0; padding: 0 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .refined-menu ul { list-style: none; padding: 0; margin: 0 0 16px 0; }
        .refined-menu ul:last-child { margin-bottom: 0; }
        .refined-menu li { margin-bottom: 4px; }
        .refined-menu a { display: flex; align-items: center; gap: 12px; padding: 10px 12px; color: #94a3b8; text-decoration: none; font-size: 0.875rem; font-weight: 600; border-radius: 12px; transition: all 0.2s ease; }
        .refined-menu a:hover { background-color: rgba(30, 41, 59, 0.8); color: #f8fafc; }
        .refined-menu a.active { background-color: #6366f1; color: #ffffff; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.25); }
    </style>
</head>
<body class="text-slate-200 flex h-screen overflow-hidden selection:bg-indigo-500 selection:text-white">
   <aside class="w-72 m-4 mr-0 rounded-[2rem] glass flex flex-col shadow-2xl z-20 border-r border-slate-800/50 shrink-0 h-[calc(100vh-2rem)]">
        <div class="p-8 pb-4 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight text-white">BOT<span class="text-indigo-400 text-sm ml-1 font-semibold tracking-wide">STUDENT</span></h2>
            </div>
        </div>
        
        <nav class="refined-menu flex-1 px-4 py-6 overflow-y-auto custom-scrollbar">
            <?php 
            $current_page = basename($_SERVER['PHP_SELF']);
            $view_course_id = isset($_GET['course_id']) ? true : false;
            ?>
            <h3>Dashboard</h3>
            <ul>
                <li><a href="index.php" <?= $current_page == 'index.php' ? 'class="active"' : '' ?>><i class="fas fa-home w-5 text-center"></i> Home</a></li>
                <li><a href="student_panel.php" <?= ($current_page == 'student_panel.php' && !$view_course_id) ? 'class="active"' : '' ?>><i class="fas fa-th-large w-5 text-center"></i> Dashboard</a></li>
                <li><a href="explore_courses.php" <?= $current_page == 'explore_courses.php' ? 'class="active"' : '' ?>><i class="fas fa-compass w-5 text-center"></i> Explore</a></li>
            </ul>
            
            <h3>Settings</h3>
            <ul>
                <li><a href="assignments.php" <?= $current_page == 'assignments.php' ? 'class="active"' : '' ?>><i class="fas fa-tasks w-5 text-center"></i> Assignments</a></li>
                <li>
                    <a href="cart.php" <?= $current_page == 'cart.php' ? 'class="active"' : '' ?>>
                        <i class="fas fa-shopping-cart w-5 text-center"></i> My Cart
                        <?php if(isset($cart_count) && $cart_count > 0): ?>
                            <span class="bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded-lg shadow-sm shadow-rose-500/20 ml-auto"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-5 mt-auto shrink-0">
            <div class="bg-slate-800/40 rounded-2xl p-4 border border-slate-700/50 mb-4 backdrop-blur-sm">
                <p class="text-[10px] text-slate-500 font-bold mb-3 uppercase tracking-widest">Student Profile</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-inner border border-white/10">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-white truncate"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                        <p class="text-xs text-indigo-400 font-medium">Premium Member</p>
                    </div>
                </div>
            </div>
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-rose-400 hover:bg-rose-500/10 rounded-2xl transition-all font-semibold text-sm">
                <i class="fas fa-power-off w-5 text-center"></i> Logout
            </a>
        </div>
    </aside>
    <main class="flex-1 overflow-y-auto p-8 lg:p-12 custom-scrollbar">
        <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl md:text-5xl font-black text-white mb-2 tracking-tighter">
                    Explore <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Courses</span>
                </h1>
                <p class="text-slate-400 font-medium text-lg">Discover new skills and level up your knowledge.</p>
            </div>
        </header>

        <?php if(isset($success_msg)): ?>
            <div class="glass border-l-4 border-emerald-500 rounded-2xl p-4 mb-8 shadow-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                <p class="text-slate-200 font-medium"><?php echo $success_msg; ?></p>
            </div>
        <?php endif; ?>
        <?php if(isset($error_msg)): ?>
            <div class="glass border-l-4 border-rose-500 rounded-2xl p-4 mb-8 shadow-lg flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-rose-500 text-xl"></i>
                <p class="text-slate-200 font-medium"><?php echo $error_msg; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php 
            if($courses_query && mysqli_num_rows($courses_query) > 0){
                while($course = mysqli_fetch_assoc($courses_query)): 
            ?>
            <div class="glass border border-slate-700/50 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl hover:border-indigo-500/50 transition-all duration-300 group flex flex-col">
                <div class="relative h-48 overflow-hidden bg-slate-800">
                    <img src="uploaded_img/<?php echo htmlspecialchars($course['image']); ?>" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
                    <div class="absolute top-4 right-4 bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-full border border-slate-700">
                        <p class="text-indigo-400 font-black text-sm">৳<?php echo number_format($course['price'], 2); ?></p>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-xl font-bold text-white mb-2 line-clamp-2"><?php echo htmlspecialchars($course['name']); ?></h3>
                    <div class="mt-auto pt-6">
                        <?php if(in_array($course['id'], $enrolled_items)): ?>
                            <a href="student_panel.php?course_id=<?php echo $course['id']; ?>" class="block w-full bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 border border-emerald-500/30 text-center font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-play-circle"></i> Continue
                            </a>
                        <?php elseif(in_array($course['id'], $cart_items)): ?>
                            <a href="cart.php" class="block w-full bg-slate-700 hover:bg-slate-600 text-white text-center font-bold py-3.5 rounded-xl transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-cart"></i> View Cart
                            </a>
                        <?php else: ?>
                            <form method="post">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <button type="submit" name="add_to_cart" class="w-full bg-indigo-500 hover:bg-indigo-400 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2 transform active:scale-95">
                                    <i class="fas fa-plus"></i> Add to Cart
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; } else { ?>
                <div class="col-span-full glass rounded-[2rem] border border-slate-700/50 p-16 text-center shadow-xl">
                    <div class="w-20 h-20 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-700">
                        <i class="fas fa-box-open text-3xl text-slate-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No courses found</h3>
                    <p class="text-slate-400">Check back later for new and exciting content.</p>
                </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>
