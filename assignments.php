<?php
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

$student_name = $_SESSION['user_name'];
$student_email = $_SESSION['user_email'] ?? '';
$session_id = intval($_SESSION['id'] ?? $_SESSION['user_id'] ?? 0);

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

$message = '';
$cart_count_query = @mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id IN ('$id_list')");
$cart_count_data = $cart_count_query ? mysqli_fetch_assoc($cart_count_query) : ['count' => 0];
$cart_count = $cart_count_data['count'] ?? 0;

if (isset($_POST['submit_assignment'])) {
    $assignment_id = mysqli_real_escape_string($conn, $_POST['assignment_id']);
    $file_name = $_FILES['assignment_file']['name'];
    $file_tmp = $_FILES['assignment_file']['tmp_name'];
    
    if (!is_dir('uploaded_assignments')) {
        mkdir('uploaded_assignments', 0777, true);
    }
    $clean_file_name = preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);
    $file_folder = 'uploaded_assignments/' . time() . '_' . $clean_file_name;
    if (!empty($file_name)) {
        if (move_uploaded_file($file_tmp, $file_folder)) {
            $insert_submission = mysqli_query($conn, "INSERT INTO submissions (assignment_id, user_id, file_path, status) VALUES ('$assignment_id', '$primary_user_id', '$file_folder', 'Pending')");
            if ($insert_submission) {
                $message = "success|Assignment submitted successfully!";
            } else {
                $message = "error|Failed to save submission to the database.";
            }
        } else {
            $message = "error|Failed to upload file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments | BOT STUDENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        input[type="file"]::file-selector-button { transition: all 0.2s ease-in-out; }
        
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
                    My <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Assignments</span>
                </h1>
                <p class="text-slate-400 font-medium text-lg">Track your tasks, submit work, and view grades.</p>
            </div>
            <div class="glass p-1.5 rounded-2xl flex gap-1.5 border border-slate-700/50 shadow-xl">
                <div class="px-5 py-2.5 bg-slate-800/80 rounded-xl text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Tasks</p>
                    <p class="text-sm font-extrabold text-rose-400 flex items-center justify-center gap-1.5"><i class="fas fa-clock text-xs"></i> Pending Review</p>
                </div>
            </div>
        </header>

        <?php if ($message != ''): 
            $msg_parts = explode('|', $message);
            $type = $msg_parts[0];
            $text = $msg_parts[1] ?? $message;
        ?>
            <div class="glass border-l-4 <?php echo $type === 'success' ? 'border-emerald-500' : 'border-rose-500'; ?> rounded-2xl p-4 mb-8 shadow-lg flex items-center gap-3">
                <i class="fas <?php echo $type === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-rose-500'; ?> text-xl"></i>
                <p class="text-slate-200 font-medium"><?php echo htmlspecialchars($text); ?></p>
            </div>
        <?php endif; ?>

        <?php
        $assignments_query = "SELECT a.*, p.name as course_name, s.status, s.grade 
                              FROM assignments a 
                              JOIN products p ON a.course_id = p.id 
                              JOIN enrollments e ON p.id = e.course_id 
                              LEFT JOIN submissions s ON a.id = s.assignment_id AND s.user_id IN ('$id_list')
                              WHERE e.user_id IN ('$id_list')";
        $assignments_result = @mysqli_query($conn, $assignments_query);
        
        if ($assignments_result && mysqli_num_rows($assignments_result) > 0) {
            while ($assignment = mysqli_fetch_assoc($assignments_result)) {
        ?>
        <div class="glass border border-slate-700/50 rounded-3xl p-8 mb-6 shadow-xl relative overflow-hidden group hover:border-indigo-500/30 transition-all duration-300">
            <div class="absolute top-0 right-0 p-8">
                <?php if ($assignment['status'] == 'Pending'): ?>
                    <span class="bg-amber-500/10 text-amber-400 px-4 py-2 rounded-xl text-xs font-bold border border-amber-500/20 shadow-lg shadow-amber-500/10 flex items-center gap-2">
                        <i class="fas fa-hourglass-half"></i> Under Review
                    </span>
                <?php elseif ($assignment['status'] == 'Graded'): ?>
                    <span class="bg-emerald-500/10 text-emerald-400 px-4 py-2 rounded-xl text-xs font-bold border border-emerald-500/20 shadow-lg shadow-emerald-500/10 flex items-center gap-2">
                        <i class="fas fa-check-double"></i> Graded: <?php echo htmlspecialchars($assignment['grade']); ?>
                    </span>
                <?php else: ?>
                    <span class="bg-rose-500/10 text-rose-400 px-4 py-2 rounded-xl text-xs font-bold border border-rose-500/20 shadow-lg shadow-rose-500/10 flex items-center gap-2">
                        <i class="fas fa-exclamation"></i> Not Submitted
                    </span>
                <?php endif; ?>
            </div>

            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20">
                        <i class="fas fa-book text-indigo-400 text-sm"></i>
                    </div>
                    <p class="text-indigo-400 font-bold text-sm tracking-wide uppercase"><?php echo htmlspecialchars($assignment['course_name']); ?></p>
                </div>
                
                <h3 class="text-2xl font-bold text-white mb-3 tracking-tight"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                <p class="text-slate-400 leading-relaxed mb-8"><?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                
                <?php if (!$assignment['status']): ?>
                    <form method="post" enctype="multipart/form-data" class="mt-6 space-y-4 max-w-md">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                        
                        <label class="block relative group cursor-pointer">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-cloud-upload-alt text-slate-400 group-hover:text-indigo-400 transition-colors"></i>
                            </div>
                            <input type="file" name="assignment_file" required 
                                class="block w-full text-sm text-slate-400 
                                file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 
                                file:text-sm file:font-bold file:bg-indigo-500 file:text-white 
                                hover:file:bg-indigo-400 file:transition-all file:cursor-pointer
                                border border-slate-700/50 rounded-xl bg-slate-900/50 
                                hover:border-indigo-500/50 transition-all cursor-pointer">
                        </label>

                        <button type="submit" name="submit_assignment" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-3.5 rounded-xl text-sm font-bold uppercase tracking-wider transition-all shadow-lg shadow-indigo-600/20 transform active:scale-95 flex items-center justify-center gap-2">
                            Upload File <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php
            }
        } else {
            echo '
            <div class="glass border border-slate-700/50 rounded-[2rem] p-16 text-center shadow-xl max-w-2xl mx-auto mt-10">
                <div class="w-20 h-20 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-700">
                    <i class="fas fa-check-double text-3xl text-emerald-500/50"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">You\'re all caught up!</h3>
                <p class="text-slate-400">No assignments are currently pending for your enrolled courses.</p>
            </div>';
        }
        ?>
    </main>
</body>
</html>
