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
@mysqli_query($conn, "CREATE TABLE IF NOT EXISTS lesson_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    progress_percent INT DEFAULT 0
)");
if (isset($_POST['update_progress'])) {
    $lesson_id = intval($_POST['lesson_id']);
    $progress = intval($_POST['progress']);
    $progress = max(0, min(100, $progress));
    $check = @mysqli_query($conn, "SELECT id, progress_percent FROM lesson_progress WHERE user_id='$primary_user_id' AND lesson_id='$lesson_id'");
    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        if ($progress > $row['progress_percent']) {
            @mysqli_query($conn, "UPDATE lesson_progress SET progress_percent='$progress' WHERE user_id='$primary_user_id' AND lesson_id='$lesson_id'");
        }
    } else {
        @mysqli_query($conn, "INSERT INTO lesson_progress (user_id, lesson_id, progress_percent) VALUES ('$primary_user_id', '$lesson_id', '$progress')");
    }
    $course_check = @mysqli_query($conn, "SELECT course_id FROM lessons WHERE id='$lesson_id'");
    if($course_check && mysqli_num_rows($course_check) > 0 && $progress == 100) {
        $cid = mysqli_fetch_assoc($course_check)['course_id'];       
        $cert_exists = @mysqli_query($conn, "SELECT id FROM certificates WHERE user_id='$primary_user_id' AND course_id='$cid'");
        if(!$cert_exists || mysqli_num_rows($cert_exists) == 0) {
            @mysqli_query($conn, "INSERT INTO certificates (user_id, course_id, issue_date) VALUES ('$primary_user_id', '$cid', NOW())");
        }
    }
    exit();
}
$cart_count = 0;
$cart_count_query = @mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id IN ('$id_list')");
if ($cart_count_query) {
    $cart_count_data = mysqli_fetch_assoc($cart_count_query);
    $cart_count = $cart_count_data['count'] ?? 0;
}
$view_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$view_lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;
$enrolled_courses = [];
$added_course_ids = [];
$enrollments_query = "SELECT p.*, e.status as enrollment_status FROM enrollments e JOIN products p ON e.course_id = p.id WHERE e.user_id IN ('$id_list') AND (LOWER(e.status) IN ('approved', 'confirmed', 'completed', 'success', 'active') OR e.status IS NULL OR e.status = '')";
$res1 = @mysqli_query($conn, $enrollments_query);
if ($res1) {
    while ($row = mysqli_fetch_assoc($res1)) {
        $enrolled_courses[] = $row;
        $added_course_ids[] = $row['id'];
    }
}
$orders_query = "SELECT p.*, 'completed' as enrollment_status FROM orders o JOIN products p ON (o.course_id = p.id OR o.product_id = p.id) WHERE o.user_id IN ('$id_list') AND LOWER(o.payment_status) IN ('completed', 'success', 'approved')";
$res2 = @mysqli_query($conn, $orders_query);
if ($res2) {
    while ($row = mysqli_fetch_assoc($res2)) {
        if (!in_array($row['id'], $added_course_ids)) {
            $enrolled_courses[] = $row;
            $added_course_ids[] = $row['id'];
        }
    }
}
$certs_course_query = "SELECT p.*, 'completed' as enrollment_status FROM certificates c JOIN products p ON c.course_id = p.id WHERE c.user_id IN ('$id_list')";
$res3 = @mysqli_query($conn, $certs_course_query);
if ($res3) {
    while ($row = mysqli_fetch_assoc($res3)) {
        if (!in_array($row['id'], $added_course_ids)) {
            $enrolled_courses[] = $row;
            $added_course_ids[] = $row['id'];
        }
    }
}

$enrolled_count = count($enrolled_courses);
$completed_count = 0;
$active_courses_list = [];
$completed_courses_list = [];
foreach ($enrolled_courses as &$course) {
    $cid = $course['id'];
    $total_lessons_q = @mysqli_query($conn, "SELECT COUNT(*) as total FROM lessons WHERE course_id = '$cid'");
    $total_lessons = $total_lessons_q ? (mysqli_fetch_assoc($total_lessons_q)['total'] ?? 0) : 0;   
    $avg_progress = 0;
    if ($total_lessons > 0) {
        $watched_lessons_q = @mysqli_query($conn, "SELECT SUM(progress_percent) as total_prog FROM lesson_progress lp JOIN lessons l ON lp.lesson_id = l.id WHERE l.course_id = '$cid' AND lp.user_id IN ('$id_list')");
        if ($watched_lessons_q) {
            $total_prog = mysqli_fetch_assoc($watched_lessons_q)['total_prog'] ?? 0;
            $avg_progress = min(100, round($total_prog / $total_lessons));
        }
    }
    $cert_check = @mysqli_query($conn, "SELECT id FROM certificates WHERE course_id = '$cid' AND user_id IN ('$id_list')");
    if (($cert_check && mysqli_num_rows($cert_check) > 0) || strtolower($course['enrollment_status'] ?? '') == 'completed') {
        $avg_progress = 100;
    }
    $course['progress'] = $avg_progress;
    if ($avg_progress == 100) {
        $completed_count++;
        $completed_courses_list[] = $course;
    } else {
        $active_courses_list[] = $course;
    }
}
unset($course);
$c_name = 'Course Viewer';
if ($view_course_id > 0) {
    $course_details_query = @mysqli_query($conn, "SELECT name FROM products WHERE id = '$view_course_id'");
    if ($course_details_query) {
        $course_details = mysqli_fetch_assoc($course_details_query);
        $c_name = $course_details['name'] ?? 'Course Viewer';
    }
    $lessons_query = @mysqli_query($conn, "SELECT * FROM lessons WHERE course_id = '$view_course_id' ORDER BY id ASC");
}
$my_certificates = [];
$certificates_query = @mysqli_query($conn, "SELECT c.course_id, p.name as course_name FROM certificates c JOIN products p ON c.course_id = p.id WHERE c.user_id IN ('$id_list')");
if ($certificates_query) {
    while ($cert = mysqli_fetch_assoc($certificates_query)) {
        $my_certificates[] = $cert;
    }
}
$total_certificates = count($my_certificates);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Panel | Elevate Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: radial-gradient(circle at top right, #1e1b4b, #0f172a); }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .course-card:hover .play-btn { transform: scale(1.1); opacity: 1; }
        
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
            $is_course_view = isset($_GET['course_id']) ? true : false;
            ?>
            <h3>Dashboard</h3>
            <ul>
                <li><a href="index.php" <?= $current_page == 'index.php' ? 'class="active"' : '' ?>><i class="fas fa-home w-5 text-center"></i> Home</a></li>
                <li><a href="student_panel.php" <?= ($current_page == 'student_panel.php' && !$is_course_view) ? 'class="active"' : '' ?>><i class="fas fa-th-large w-5 text-center"></i> Dashboard</a></li>
                <li><a href="explore_courses.php" <?= $current_page == 'explore_courses.php' ? 'class="active"' : '' ?>><i class="fas fa-compass w-5 text-center"></i> Explore</a></li>
            </ul>           
            <h3>Settings</h3>
            <ul>
                <li><a href="assignments.php" <?= $current_page == 'assignments.php' ? 'class="active"' : '' ?>><i class="fas fa-tasks w-5 text-center"></i> Assignments</a></li>
                <li>
                    <a href="cart.php" <?= $current_page == 'cart.php' ? 'class="active"' : '' ?>>
                        <i class="fas fa-shopping-cart w-5 text-center"></i> My Cart
                        <?php if($cart_count > 0): ?>
                            <span class="bg-rose-500 text-white text-xs font-bold px-2 py-0.5 rounded-lg shadow-sm shadow-rose-500/20 ml-auto"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
            <?php if($total_certificates > 0): ?>
                <h3>My Certificates</h3>
                <ul>
                    <?php foreach($my_certificates as $cert): ?>
                    <li>
                        <a href="generate_certificate.php?course_id=<?= $cert['course_id'] ?>" target="_blank" class="!text-emerald-400 hover:!text-emerald-300">
                            <i class="fas fa-award w-5 text-center"></i> <?= htmlspecialchars(strlen($cert['course_name']) > 15 ? substr($cert['course_name'], 0, 15).'...' : $cert['course_name']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            
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
    <main class="flex-1 overflow-y-auto p-8 lg:p-12 custom-scrollbar relative">      
        <?php if($view_course_id > 0): ?>
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <a href="student_panel.php" class="text-slate-400 hover:text-white flex items-center gap-2 group transition-all bg-slate-800/50 px-4 py-2 rounded-xl border border-slate-700/50">
                        <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform text-sm"></i>
                        <span class="font-semibold text-sm">Back to Dashboard</span>
                    </a>
                </div>
                <h1 class="text-3xl lg:text-4xl font-extrabold text-white mb-8 tracking-tight"><?= htmlspecialchars($c_name) ?></h1>
                <?php 
                $active_lesson = null;
                if(isset($lessons_query) && $lessons_query && mysqli_num_rows($lessons_query) > 0) {
                    mysqli_data_seek($lessons_query, 0);
                    $first_lesson = null;                  
                    while($lesson = mysqli_fetch_assoc($lessons_query)) {
                        if(!$first_lesson) $first_lesson = $lesson;
                        if($view_lesson_id > 0 && $lesson['id'] == $view_lesson_id) {
                            $active_lesson = $lesson;
                            break;
                        }
                    }
                    if(!$active_lesson) $active_lesson = $first_lesson;
                }
                ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-700/50 bg-[#020617] group relative ring-1 ring-white/10">
                            <div class="aspect-video w-full flex items-center justify-center bg-slate-900 relative">
                                <?php
                                if($active_lesson) {
                                    if(!empty($active_lesson['video_url'])) {
                                        echo '<iframe id="yt-player" class="w-full h-full relative z-10" src="'.htmlspecialchars($active_lesson['video_url']).(strpos($active_lesson['video_url'], '?') !== false ? '&' : '?').'enablejsapi=1" frameborder="0" allowfullscreen></iframe>';
                                    } else if(!empty($active_lesson['video_file'])) {
                                        echo '<video id="html-player" controls class="w-full h-full object-cover relative z-10"><source src="uploaded_videos/'.htmlspecialchars($active_lesson['video_file']).'" type="video/mp4"></video>';
                                    } else if(!empty($active_lesson['content_link'])) {
                                        $content = $active_lesson['content_link'];
                                        if (strpos($content, 'youtube.com') !== false || strpos($content, 'youtu.be') !== false) {
                                            $youtube_id = '';
                                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $content, $match)) {
                                                $youtube_id = $match[1];
                                            }
                                            echo '<iframe id="yt-player" class="w-full h-full relative z-10" src="https://www.youtube.com/embed/'.$youtube_id.'?enablejsapi=1" frameborder="0" allowfullscreen></iframe>';
                                        } else {
                                            echo '<video id="html-player" controls class="w-full h-full relative z-10"><source src="'.htmlspecialchars($content).'" type="video/mp4"></video>';
                                        }
                                    } else {
                                        echo '<p class="text-slate-500 font-medium relative z-10">No video source provided.</p>';
                                    }
                                } else {
                                    echo '<p class="text-slate-500 font-medium relative z-10">No video content available yet.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>                   
                    <div class="h-full">
                        <div class="glass rounded-3xl p-6 h-full flex flex-col border border-slate-700/50">
                            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                                <i class="fas fa-list-ul text-indigo-400"></i> Course Content
                            </h3>
                            <div class="space-y-3 overflow-y-auto custom-scrollbar flex-1 pr-2">
                                <?php 
                                if(isset($lessons_query) && $lessons_query && mysqli_num_rows($lessons_query) > 0) {
                                    mysqli_data_seek($lessons_query, 0);
                                    $lesson_count = 1;
                                    while($lesson = mysqli_fetch_assoc($lessons_query)): 
                                        $is_playing = ($active_lesson && $active_lesson['id'] == $lesson['id']);
                                ?>
                                <a href="?course_id=<?= $view_course_id ?>&lesson_id=<?= $lesson['id'] ?>" class="relative p-3.5 border rounded-2xl flex items-center gap-4 transition-all group cursor-pointer <?= $is_playing ? 'bg-indigo-500/10 border-indigo-500/30' : 'bg-slate-800/40 hover:bg-indigo-500/10 border-slate-700/50 hover:border-indigo-500/30' ?>">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold transition-colors <?= $is_playing ? 'bg-indigo-500 text-white' : 'bg-slate-800 group-hover:bg-indigo-500 text-slate-400 group-hover:text-white' ?>">
                                        <?= $lesson_count ?>
                                    </div>
                                    <div class="flex-1 pr-6">
                                        <p class="text-sm font-semibold transition-colors line-clamp-1 <?= $is_playing ? 'text-white' : 'text-slate-300 group-hover:text-white' ?>"><?= htmlspecialchars($lesson['title'] ?? 'Lesson '.$lesson_count) ?></p>
                                    </div>
                                </a>
                                <?php 
                                    $lesson_count++;
                                    endwhile; 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <header class="mb-14 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-4xl md:text-3xl font-black text-white mb-3 tracking-tighter">
                        Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </h1>
                    <p class="text-slate-400 font-medium text-lg">You have <span class="text-white font-bold bg-slate-800 px-2 py-0.5 rounded-md mx-1"><?= count($active_courses_list) ?></span> active courses to complete.</p>
                </div>
                <div class="glass p-1.5 rounded-2xl flex gap-1.5 border border-slate-700/50 shadow-xl">
                    <div class="px-5 py-2.5 bg-slate-800/80 rounded-xl text-center">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Status</p>
                        <p class="text-sm font-extrabold text-indigo-400 flex items-center gap-1.5"><i class="fas fa-check-circle text-xs"></i> Active</p>
                    </div>
                </div>
            </header>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-14">
                <div class="glass p-8 rounded-[2rem] relative overflow-hidden group border border-slate-700/50 hover:border-indigo-500/30 transition-colors">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>
                    <div class="flex justify-between items-start mb-6">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Enrolled Courses</p>
                        <i class="fas fa-book-open text-indigo-400/50 text-xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl font-black text-white tracking-tighter"><?= $enrolled_count ?></h3>
                    </div>
                </div>               
                <div class="glass p-8 rounded-[2rem] relative overflow-hidden group border border-slate-700/50 hover:border-purple-500/30 transition-colors">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all duration-500"></div>
                    <div class="flex justify-between items-start mb-6">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Completed</p>
                        <i class="fas fa-trophy text-purple-400/50 text-xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl font-black text-white tracking-tighter"><?= $completed_count ?></h3>
                        <span class="text-slate-500 font-bold text-sm">/ <?= $enrolled_count ?></span>
                    </div>
                </div>
                <div class="glass p-8 rounded-[2rem] relative overflow-hidden group border border-slate-700/50 hover:border-emerald-500/30 transition-colors">
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                    <div class="flex justify-between items-start mb-6">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Certificates</p>
                        <i class="fas fa-certificate text-emerald-400/50 text-xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-5xl font-black text-white tracking-tighter"><?= $total_certificates ?></h3>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between mb-8 mt-10">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-2 h-8 bg-indigo-500 rounded-full"></span>
                    Active Learning
                </h2>
            </div>
            <?php if(count($active_courses_list) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 pb-10">
                <?php foreach($active_courses_list as $course): 
                    $cp = $course['progress'];
                ?>
                <div class="course-card relative group bg-[#111827] rounded-[2rem] overflow-hidden border border-slate-800 hover:border-indigo-500/50 transition-all duration-500 shadow-xl hover:shadow-indigo-500/10 flex flex-col">
                    <div class="relative h-52 overflow-hidden">
                        <img class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" src="uploaded_img/<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['name']) ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-[#111827]/40 to-transparent opacity-90"></div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[2px]">
                            <a href="student_panel.php?course_id=<?= $course['id'] ?>" class="play-btn w-16 h-16 bg-white/90 text-indigo-600 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(99,102,241,0.5)] transition-transform">
                                <i class="fas fa-play text-xl ml-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="p-7 flex-1 flex flex-col relative z-10 -mt-10">
                        <div class="bg-[#1e293b] w-max px-3 py-1 rounded-lg border border-slate-700 mb-4 shadow-lg">
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">In Progress</p>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3 line-clamp-2 leading-snug group-hover:text-indigo-300 transition-colors"><?= htmlspecialchars($course['name']) ?></h3>
                        <div class="mt-auto pt-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Overall Progress</span>
                                <span class="text-xs font-bold text-indigo-400"><?= $cp ?>%</span>
                            </div>
                            <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden border border-slate-700/50">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-full rounded-full relative" style="width: <?= $cp ?>%">
                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="glass border border-slate-700/50 rounded-[2rem] p-8 text-center shadow-xl mb-10">
                <p class="text-slate-400">You don't have any courses currently in progress.</p>
            </div>
            <?php endif; ?>
            <div class="flex items-center justify-between mb-8 mt-10">
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <span class="w-2 h-8 bg-emerald-500 rounded-full"></span>
                    Completed Data & Certificates
                </h2>
            </div>
            <?php if(count($completed_courses_list) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 pb-10">
                <?php foreach($completed_courses_list as $course): 
                    $cp = $course['progress'];
                ?>
                <div class="course-card relative group bg-[#064e3b]/10 rounded-[2rem] overflow-hidden border border-emerald-900/50 hover:border-emerald-500/50 transition-all duration-500 shadow-xl flex flex-col">
                    <div class="relative h-52 overflow-hidden opacity-80 mix-blend-luminosity hover:mix-blend-normal transition-all">
                        <img class="h-full w-full object-cover" src="uploaded_img/<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['name']) ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#111827] via-[#111827]/60 to-transparent opacity-90"></div>
                    </div>
                    <div class="p-7 flex-1 flex flex-col relative z-10 -mt-10">
                        <div class="bg-emerald-900/50 w-max px-3 py-1 rounded-lg border border-emerald-700/50 mb-4 shadow-lg backdrop-blur-sm">
                            <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1"><i class="fas fa-check-circle"></i> Completed</p>
                        </div>
                        <h3 class="text-xl font-bold text-slate-300 mb-3 line-clamp-2 leading-snug group-hover:text-emerald-300 transition-colors"><?= htmlspecialchars($course['name']) ?></h3>
                        
                        <div class="mt-auto pt-4 border-t border-slate-700/50 flex gap-2">
                            <a href="student_panel.php?course_id=<?= $course['id'] ?>" class="flex-1 text-center py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl transition-all">
                                Review
                            </a>
                            <a href="generate_certificate.php?course_id=<?= $course['id'] ?>" target="_blank" class="flex-1 text-center py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex justify-center items-center gap-2">
                                <i class="fas fa-certificate"></i> View Certificate
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="glass border border-slate-700/50 rounded-[2rem] p-10 text-center shadow-xl mb-10">
                <div class="w-16 h-16 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-700">
                    <i class="fas fa-history text-2xl text-slate-500"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">No past data</h3>
                <p class="text-slate-400 max-w-md mx-auto text-sm">Courses you complete 100% will appear here along with your verifiable BOT Learning Certificates.</p>
            </div>
            <?php endif; ?>

        <?php endif; ?>
    </main>
</body>
</html>
