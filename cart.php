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

if(isset($_GET['remove'])){
    $remove_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE id = '$remove_id' AND user_id IN ('$id_list')");
    header('location:cart.php');
    exit();
}

$query = "SELECT cart.*, products.name, products.image, products.price FROM cart JOIN products ON cart.course_id = products.id WHERE cart.user_id IN ('$id_list')";
$result = @mysqli_query($conn, $query);
$total = 0;
$item_count = $result ? mysqli_num_rows($result) : 0;
$cart_count_query = @mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id IN ('$id_list')");
$cart_count_data = $cart_count_query ? mysqli_fetch_assoc($cart_count_query) : ['count' => 0];
$cart_count = $cart_count_data['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart | BOT STUDENT</title>
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
                    Shopping <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Cart</span>
                </h1>
                <p class="text-slate-400 font-medium text-lg">Review your selected courses before checkout.</p>
            </div>
            <div class="glass p-1.5 rounded-2xl flex gap-1.5 border border-slate-700/50 shadow-xl">
                <div class="px-5 py-2.5 bg-slate-800/80 rounded-xl text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Items</p>
                    <p class="text-sm font-extrabold text-indigo-400"><?= $item_count ?> Selected</p>
                </div>
            </div>
        </header>
        <?php if ($item_count > 0): ?>
            <div class="flex flex-col xl:flex-row gap-8 items-start">
                <div class="flex-1 w-full space-y-4">
                    <?php while ($row = mysqli_fetch_assoc($result)): $total += $row['price']; ?>
                        <div class="glass p-4 rounded-3xl flex items-center gap-6 border border-slate-700/50 hover:border-indigo-500/50 transition-colors group relative overflow-hidden">
                            <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" alt="Course" class="w-32 h-24 object-cover rounded-2xl shadow-md">
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Course</p>
                                <h3 class="text-xl font-bold text-white mb-1"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="text-white font-bold text-lg">৳<?php echo number_format($row['price'], 2); ?></p>
                            </div>
                            <a href="cart.php?remove=<?php echo $row['id']; ?>" class="mr-4 w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-white hover:bg-rose-500 transition-all shadow-lg shadow-transparent hover:shadow-rose-500/25" title="Remove">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="w-full xl:w-96 shrink-0 sticky top-0">
                    <div class="glass p-8 rounded-[2rem] border border-slate-700/50 shadow-2xl relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
                        <h2 class="text-xl font-extrabold text-white mb-6 flex items-center gap-3">
                            <i class="fas fa-receipt text-indigo-400"></i> Summary
                        </h2>                     
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center text-slate-400 font-medium">
                                <span>Subtotal (<?php echo $item_count; ?> items)</span>
                                <span>৳<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-400 font-medium">
                                <span>Taxes</span>
                                <span>Calculated at checkout</span>
                            </div>
                        </div>
                        <div class="border-t border-slate-700/50 pt-6 mb-8">
                            <div class="flex justify-between items-end">
                                <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">Total</span>
                                <span class="text-3xl font-black text-white tracking-tighter">৳<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                        <a href="checkout.php" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg shadow-indigo-500/25 transform hover:-translate-y-0.5">
                            Proceed to Checkout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="glass border border-slate-700/50 rounded-[2rem] p-16 text-center shadow-2xl max-w-2xl mx-auto mt-10 relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiLz4KPC9zdmc+')] opacity-20"></div>
                <div class="w-24 h-24 bg-gradient-to-br from-slate-800 to-slate-900 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-700/50 relative z-10 shadow-inner">
                    <i class="fas fa-shopping-basket text-4xl text-slate-400"></i>
                </div>
                <h2 class="text-3xl font-black text-white mb-4 relative z-10">Your cart is empty</h2>
                <p class="text-slate-400 mb-8 max-w-sm mx-auto relative z-10 font-medium">Looks like you haven't added any courses yet. Discover your next skill today.</p>
                <a href="explore_courses.php" class="relative z-10 inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-400 text-white font-bold py-3.5 px-8 rounded-xl transition-all shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-search"></i> Explore Courses
                </a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
