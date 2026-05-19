<?php
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
@include 'config.php';
session_start();

if (!isset($_SESSION['instructor_name'])) {
    header('location:login_form.php');
    exit();
}

$instructor_name = $_SESSION['instructor_name'];
$instructor_email = $_SESSION['instructor_email']; 

$session_id = $_SESSION['instructor_id'] ?? $_SESSION['id'] ?? 0;
$valid_ids = [];

if ($session_id > 0) {
    $valid_ids[] = $session_id;
}

$has_students = false;
$has_instructors = false;
$tables_res = @mysqli_query($conn, "SHOW TABLES");
if ($tables_res) {
    while ($row = mysqli_fetch_array($tables_res)) {
        if ($row[0] == 'students') $has_students = true;
        if ($row[0] == 'instructors') $has_instructors = true;
    }
}

$check_inst = @mysqli_query($conn, "SELECT id FROM instructors WHERE email = '$instructor_email'");
if ($check_inst && mysqli_num_rows($check_inst) > 0) {
    while ($row = mysqli_fetch_assoc($check_inst)) {
        $valid_ids[] = $row['id'];
    }
}

$check_user = @mysqli_query($conn, "SELECT id FROM user_form WHERE email = '$instructor_email'");
if ($check_user && mysqli_num_rows($check_user) > 0) {
    while ($row = mysqli_fetch_assoc($check_user)) {
        $valid_ids[] = $row['id'];
    }
}

$valid_ids = array_values(array_unique(array_filter($valid_ids)));
if (empty($valid_ids)) {
    $valid_ids[] = 0; 
}
$id_list = implode("','", $valid_ids);
$primary_instructor_id = $valid_ids[0]; 

$message = [];
$tab = $_GET['tab'] ?? 'dashboard';

$courses_query = @mysqli_query($conn, "SELECT id, name FROM products WHERE instructor_id IN ('$id_list')");
$my_courses = [];
if ($courses_query && mysqli_num_rows($courses_query) > 0) {
    while ($row = mysqli_fetch_assoc($courses_query)) {
        $my_courses[] = $row;
    }
}

if (isset($_POST['issue_certificate'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    
    $check_cert = @mysqli_query($conn, "SELECT id FROM certificates WHERE user_id='$student_id' AND course_id='$course_id'");
    
    if($check_cert && mysqli_num_rows($check_cert) == 0) {
        $insert_cert = @mysqli_query($conn, "INSERT INTO certificates (user_id, course_id, instructor_id) VALUES ('$student_id', '$course_id', '$primary_instructor_id')");
        if($insert_cert) {
            $message[] = "success|Certificate issued successfully!";
        } else {
            $message[] = "error|Failed to issue certificate.";
        }
    } else {
        $message[] = "error|Certificate already issued for this student.";
    }
}

if (isset($_POST['add_content'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $lesson_title = mysqli_real_escape_string($conn, $_POST['lesson_title']);
    $content_type = mysqli_real_escape_string($conn, $_POST['content_type']);
    $youtube_url = mysqli_real_escape_string($conn, $_POST['youtube_url']);    
    
    $verify_course = @mysqli_query($conn, "SELECT id FROM products WHERE id = '$course_id' AND instructor_id IN ('$id_list')");   
    
    if ($verify_course && mysqli_num_rows($verify_course) > 0) {
        $video_file = $_FILES['video_file']['name'];
        $video_tmp_name = $_FILES['video_file']['tmp_name'];
        $clean_video_name = preg_replace("/[^a-zA-Z0-9.]/", "_", $video_file);
        $video_folder = 'uploaded_videos/' . time() . '_' . $clean_video_name;

        $content_link = '';
        $upload_success = false;

        if ($content_type == 'video' && !empty($video_file)) {
            if (!is_dir('uploaded_videos')) mkdir('uploaded_videos', 0777, true);
            if(move_uploaded_file($video_tmp_name, $video_folder)){
                $content_link = $video_folder;
                $upload_success = true;
            }
        } elseif ($content_type == 'youtube' && !empty($youtube_url)) {
            $content_link = $youtube_url;
            $upload_success = true;
        }

        if ($upload_success) {
            $insert_lesson = @mysqli_query($conn, "INSERT INTO lessons (course_id, title, content_type, content_link) VALUES ('$course_id', '$lesson_title', '$content_type', '$content_link')");
            if($insert_lesson){
                $message[] = "success|Lesson added successfully to your course!";
            } else {
                $message[] = "error|Failed to save lesson to the database.";
            }
        } else {
            $message[] = "error|Please provide a valid video file or YouTube URL.";
        }
    } else {
        $message[] = "error|You do not have permission to add content to this course.";
    }
}

if (isset($_POST['delete_lesson'])) {
    $lesson_id = mysqli_real_escape_string($conn, $_POST['lesson_id']);
    
    $get_lesson = @mysqli_query($conn, "SELECT l.content_link, l.content_type FROM lessons l JOIN products p ON l.course_id = p.id WHERE l.id = '$lesson_id' AND p.instructor_id IN ('$id_list')");
    
    if ($get_lesson && mysqli_num_rows($get_lesson) > 0) {
        $lesson_data = mysqli_fetch_assoc($get_lesson);
        
        if ($lesson_data['content_type'] == 'video' && file_exists($lesson_data['content_link'])) {
            unlink($lesson_data['content_link']);
        }       
        $delete_query = @mysqli_query($conn, "DELETE FROM lessons WHERE id = '$lesson_id'");
        if ($delete_query) {
            $message[] = "success|Lesson deleted successfully!";
        } else {
            $message[] = "error|Could not delete lesson.";
        }
    }
}

if (isset($_POST['save_assignment'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $current_date = date('Y-m-d');
    
    if ($due_date < $current_date) {
        $message[] = "error|The assignment due date cannot be in the past.";
    } else {
        $verify_course = @mysqli_query($conn, "SELECT id FROM products WHERE id = '$course_id' AND instructor_id IN ('$id_list')");
        if ($verify_course && mysqli_num_rows($verify_course) > 0) {
            $insert_query = "INSERT INTO assignments (course_id, title, description, due_date) VALUES ('$course_id', '$title', '$description', '$due_date')";
            if(@mysqli_query($conn, $insert_query)) {
                $message[] = "success|Assignment created successfully for your course!";
            } else {
                $message[] = "error|Could not save assignment to database.";
            }
        } else {
            $message[] = "error|You do not have permission to add assignments to this course.";
        }
    }
}

if (isset($_POST['delete_assignment'])) {
    $assignment_id = mysqli_real_escape_string($conn, $_POST['assignment_id']);
    
    $verify_assign = @mysqli_query($conn, "SELECT a.id FROM assignments a JOIN products p ON a.course_id = p.id WHERE a.id = '$assignment_id' AND p.instructor_id IN ('$id_list')");
    
    if ($verify_assign && mysqli_num_rows($verify_assign) > 0) {
        $delete_query = @mysqli_query($conn, "DELETE FROM assignments WHERE id = '$assignment_id'");
        if ($delete_query) {
            $message[] = "success|Assignment deleted successfully!";
        } else {
            $message[] = "error|Could not delete assignment.";
        }
    }
}

if (isset($_POST['submit_grade'])) {
    $submission_id = mysqli_real_escape_string($conn, $_POST['submission_id']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    $update_query = "UPDATE submissions SET grade='$grade', feedback='$feedback', status='Graded' WHERE id='$submission_id'";
    $run_update = @mysqli_query($conn, $update_query);
    if($run_update){
        $message[] = "success|Grade submitted successfully!";
    } else {
        $message[] = "error|Could not submit grade.";
    }
}

$students_q = @mysqli_query($conn, "
    SELECT COUNT(DISTINCT link.user_id) as total_students 
    FROM (
        SELECT user_id, course_id FROM enrollments
        UNION
        SELECT sub.user_id, a.course_id FROM submissions sub JOIN assignments a ON sub.assignment_id = a.id
    ) AS link
    JOIN products p ON link.course_id = p.id 
    WHERE p.instructor_id IN ('$id_list')
");
$total_students = $students_q ? (mysqli_fetch_assoc($students_q)['total_students'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Portal - Elevate Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style> 
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0B1120; } 
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        input[type="number"]::-webkit-inner-spin-button, 
        input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="text-slate-200 flex h-screen overflow-hidden selection:bg-indigo-500/30">

    <aside class="w-72 bg-[#0f172a] border-r border-slate-800/60 flex flex-col z-20 shrink-0 relative">
        <div class="absolute top-0 left-0 w-full h-32 bg-indigo-600/10 blur-3xl -z-10"></div>

        <div class="p-8 border-b border-slate-800/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-layer-group text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-white">BOT <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Tutor</span></h2>
            </div>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Menu</p>
            
            <a href="?tab=dashboard" class="group flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 <?php echo $tab == 'dashboard' ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-border-all w-5 text-center <?php echo $tab == 'dashboard' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400 transition-colors'; ?>"></i> 
                Dashboard
            </a>
            <a href="?tab=courses" class="group flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 <?php echo $tab == 'courses' ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-photo-video w-5 text-center <?php echo $tab == 'courses' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400 transition-colors'; ?>"></i> 
                Manage Content
            </a>
            <a href="?tab=assignments" class="group flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 <?php echo $tab == 'assignments' ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-file-signature w-5 text-center <?php echo $tab == 'assignments' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400 transition-colors'; ?>"></i> 
                Assignments
            </a>
            <a href="?tab=grading" class="group flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 <?php echo $tab == 'grading' ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-check-double w-5 text-center <?php echo $tab == 'grading' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400 transition-colors'; ?>"></i> 
                Grade Students
            </a>
            <a href="?tab=progress" class="group flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 <?php echo $tab == 'progress' ? 'bg-gradient-to-r from-indigo-600/90 to-purple-600/90 text-white shadow-lg shadow-indigo-900/20' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'; ?>">
                <i class="fas fa-chart-pie w-5 text-center <?php echo $tab == 'progress' ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400 transition-colors'; ?>"></i> 
                Student Progress
            </a>
            <a href="index.php" class="group flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 text-slate-400 hover:bg-slate-800/50 hover:text-slate-200">
                <i class="fas fa-globe w-5 text-center text-slate-500 group-hover:text-indigo-400 transition-colors"></i> 
                Home Page
            </a>
        </nav>

        <div class="p-6 border-t border-slate-800/60">
            <div class="flex items-center gap-3 mb-6 px-2">
                <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-indigo-400 font-bold uppercase">
                    <?php echo substr($instructor_name, 0, 1); ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate"><?php echo htmlspecialchars($instructor_name); ?></p>
                    <p class="text-xs text-slate-500 truncate">Instructor</p>
                </div>
            </div>
            <a href="logout.php" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-slate-800/50 hover:bg-red-500/10 text-slate-300 hover:text-red-400 rounded-lg text-sm font-medium transition-colors border border-transparent hover:border-red-500/20">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </a>
        </div>
    </aside>
    <main class="flex-1 overflow-y-auto bg-[#0B1120] relative">
        <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-indigo-900/20 to-transparent pointer-events-none"></div>
        <div class="max-w-6xl mx-auto p-8 lg:p-12 relative fade-in">

            <?php if (!empty($message)): ?>
                <div class="mb-8 space-y-3">
                <?php foreach($message as $msg): 
                    $msg_parts = explode('|', $msg);
                    $type = $msg_parts[0];
                    $text = $msg_parts[1] ?? $msg;
                    $is_error = $type === 'error';
                ?>
                    <div class="flex items-center gap-3 p-4 rounded-xl border <?php echo $is_error ? 'bg-red-500/10 border-red-500/20 text-red-400' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400'; ?> backdrop-blur-md">
                        <i class="fas <?php echo $is_error ? 'fa-exclamation-circle' : 'fa-check-circle'; ?> text-lg"></i>
                        <p class="text-sm font-medium"><?php echo $text; ?></p>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($tab == 'dashboard'): ?>
                <header class="mb-10">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Welcome back, <?php echo htmlspecialchars($instructor_name); ?>! 👋</h1>
                    <p class="text-slate-40 text-lg">Here's an overview of your teaching hub today.</p>
                </header>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="glass-panel p-8 rounded-2xl relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-slate-400 font-medium mb-1">Active Courses</p>
                                <h3 class="text-5xl font-black text-white"><?php echo count($my_courses); ?></h3>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-2xl shadow-[0_0_15px_rgba(99,102,241,0.1)] group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-panel p-8 rounded-2xl relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-slate-400 font-medium mb-1">Total Enrolled Students</p>
                                <h3 class="text-5xl font-black text-white"><?php echo $total_students; ?></h3>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-2xl shadow-[0_0_15px_rgba(16,185,129,0.1)] group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($tab == 'progress'): ?>
                <header class="mb-10">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Student Progress Tracker</h1>
                    <p class="text-slate-400">Monitor completion rates for videos and assignments across all your courses.</p>
                </header>
                <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-800/40 border-b border-slate-700/50">
                                <tr>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Student</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Course</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase w-1/3">Video Progress</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Assignments</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                <?php
                                $enrollment_query = "
                                    SELECT link.user_id, 
                                           COALESCE(u.name" . ($has_students ? ", s.name" : "") . ($has_instructors ? ", i.name" : "") . ", 'Unknown Student') AS student_name,
                                           COALESCE(u.email" . ($has_students ? ", s.email" : "") . ($has_instructors ? ", i.email" : "") . ", 'N/A') AS email,
                                           p.id AS course_id, p.name AS course_name 
                                    FROM (
                                        SELECT user_id, course_id FROM enrollments
                                        UNION
                                        SELECT sub.user_id, a.course_id FROM submissions sub JOIN assignments a ON sub.assignment_id = a.id
                                    ) AS link
                                    JOIN products p ON link.course_id = p.id
                                    LEFT JOIN user_form u ON link.user_id = u.id
                                    " . ($has_students ? "LEFT JOIN students s ON link.user_id = s.id" : "") . "
                                    " . ($has_instructors ? "LEFT JOIN instructors i ON link.user_id = i.id" : "") . "
                                    WHERE p.instructor_id IN ('$id_list')
                                    ORDER BY p.name ASC, student_name ASC
                                ";
                                
                                $enrolled_students = @mysqli_query($conn, $enrollment_query);
                                if ($enrolled_students && mysqli_num_rows($enrolled_students) > 0) {
                                    while ($student = mysqli_fetch_assoc($enrolled_students)) {
                                        $uid = $student['user_id'];
                                        $cid = $student['course_id'];
                                        $assign_total_q = @mysqli_query($conn, "SELECT COUNT(*) as total FROM assignments WHERE course_id = '$cid'");
                                        $total_assignments = $assign_total_q ? mysqli_fetch_assoc($assign_total_q)['total'] : 0;
                                        $assign_sub_q = @mysqli_query($conn, "SELECT COUNT(DISTINCT a.id) as total FROM submissions sub JOIN assignments a ON sub.assignment_id = a.id WHERE a.course_id = '$cid' AND sub.user_id = '$uid'");
                                        $submitted_assignments = $assign_sub_q ? mysqli_fetch_assoc($assign_sub_q)['total'] : 0;
                                        $total_lessons_q = @mysqli_query($conn, "SELECT COUNT(*) as total FROM lessons WHERE course_id = '$cid'");
                                        $total_lessons = $total_lessons_q ? (mysqli_fetch_assoc($total_lessons_q)['total'] ?? 0) : 0;
                                        $avg_video_progress = 0;
                                        if ($total_lessons > 0) {
                                            $watched_lessons_q = @mysqli_query($conn, "SELECT SUM(lp.progress_percent) as total_prog FROM lesson_progress lp JOIN lessons l ON lp.lesson_id = l.id WHERE l.course_id = '$cid' AND lp.user_id = '$uid'");
                                            if ($watched_lessons_q) {
                                                $total_prog = mysqli_fetch_assoc($watched_lessons_q)['total_prog'] ?? 0;
                                                $avg_video_progress = min(100, round($total_prog / $total_lessons));
                                            }
                                        }
                                        $is_done = ($total_assignments > 0) ? ($submitted_assignments >= $total_assignments) : true;
                                ?>
                                    <tr class="hover:bg-slate-800/30 transition-colors group">
                                        <td class="p-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-slate-700/50 flex items-center justify-center text-xs font-bold text-slate-300">
                                                    <?php echo substr($student['student_name'], 0, 1); ?>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-white"><?php echo htmlspecialchars($student['student_name']); ?></div>
                                                    <div class="text-xs text-slate-500"><?php echo htmlspecialchars($student['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-5 text-sm text-slate-300 font-medium">
                                            <?php echo htmlspecialchars($student['course_name']); ?>
                                        </td>
                                        <td class="p-5">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-bold <?php echo $avg_video_progress == 100 ? 'text-emerald-400' : 'text-indigo-400'; ?>"><?php echo $avg_video_progress; ?>%</span>
                                                <span class="text-[10px] uppercase tracking-wider text-slate-500">Watched</span>
                                            </div>
                                            <div class="w-full bg-slate-800/80 rounded-full h-1.5 overflow-hidden">
                                                <div class="<?php echo $avg_video_progress == 100 ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-gradient-to-r from-indigo-500 to-purple-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]'; ?> h-full rounded-full transition-all duration-1000 relative" style="width: <?php echo $avg_video_progress; ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="p-5">
                                            <?php if ($total_assignments > 0): ?>
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold <?php echo $is_done ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'; ?>">
                                                    <?php if($is_done): ?><i class="fas fa-check-circle"></i><?php else: ?><i class="fas fa-clock"></i><?php endif; ?>
                                                    <?php echo $submitted_assignments; ?> / <?php echo $total_assignments; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-500 bg-slate-800/50 px-3 py-1 rounded-full">None Required</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-5">
                                            <?php
                                            $cert_check = @mysqli_query($conn, "SELECT id FROM certificates WHERE user_id='$uid' AND course_id='$cid'");
                                            $has_certificate = $cert_check ? (mysqli_num_rows($cert_check) > 0) : false;
                                            
                                            if ($has_certificate): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500/10 text-emerald-400 rounded-full text-xs font-bold border border-emerald-500/20">
                                                    <i class="fas fa-award"></i> Issued
                                                </span>
                                            <?php elseif ($avg_video_progress == 100 && $is_done): ?>
                                                <form method="POST" action="?tab=progress">
                                                    <input type="hidden" name="student_id" value="<?php echo $uid; ?>">
                                                    <input type="hidden" name="course_id" value="<?php echo $cid; ?>">
                                                    <button type="submit" name="issue_certificate" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-lg transition-all shadow-md">
                                                        Issue Certificate
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-slate-500 text-xs font-medium"><i class="fas fa-lock text-slate-600"></i> Incomplete</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="p-12 text-center text-slate-500"><div class="w-16 h-16 mx-auto bg-slate-800/50 rounded-full flex items-center justify-center mb-3"><i class="fas fa-users-slash text-2xl"></i></div><p>No students have enrolled in your courses yet.</p></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($tab == 'courses'): ?>
                <header class="mb-10">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Manage Content</h1>
                    <p class="text-slate-400">Upload new video lessons or attach YouTube links to your courses.</p>
                </header>
                <div class="glass-panel p-8 md:p-10 rounded-2xl shadow-2xl mb-12 border border-slate-700/50">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-indigo-400"></i> Publish New Lesson
                    </h2>                  
                    <form action="?tab=courses" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-300">Target Course</label>
                                <select name="course_id" class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none" required style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                    <?php if(empty($my_courses)): ?>
                                        <option value="" disabled selected>No active courses found.</option>
                                    <?php else: ?>
                                        <option value="" disabled selected>Select a course...</option>
                                        <?php foreach($my_courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-300">Lesson Title</label>
                                <input type="text" name="lesson_title" placeholder="e.g. Introduction to CSS Variables" class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" required>
                            </div>
                        </div>
                        <div class="p-6 bg-slate-900/50 rounded-xl border border-slate-800/80">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-300">Content Format</label>
                                    <select name="content_type" id="content_type" class="w-full bg-[#0f172a] border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none" onchange="toggleContentInput()" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                        <option value="youtube">YouTube URL (Recommended)</option>
                                        <option value="video">Direct MP4 Upload</option>
                                    </select>
                                </div>                              
                                <div id="youtube_input_group" class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-300">YouTube Video URL</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fab fa-youtube text-red-500"></i>
                                        </div>
                                        <input type="url" name="youtube_url" placeholder="https://youtube.com/watch?v=..." class="w-full bg-[#0f172a] border border-slate-700 rounded-xl pl-10 pr-4 py-3.5 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                                    </div>
                                </div>
                                <div id="video_input_group" class="hidden space-y-2">
                                    <label class="text-sm font-semibold text-slate-300">Upload Video File</label>
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-700 border-dashed rounded-xl cursor-pointer bg-[#0f172a] hover:bg-slate-800/50 hover:border-indigo-500 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <i class="fas fa-cloud-upload-alt text-2xl text-indigo-400 mb-2"></i>
                                                <p class="mb-1 text-sm text-slate-400"><span class="font-semibold text-indigo-400">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-slate-500">MP4, MKV (MAX. 500MB)</p>
                                            </div>
                                            <input type="file" name="video_file" accept="video/mp4,video/mkv" class="hidden">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" name="add_content" class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all w-full md:w-auto" <?php echo empty($my_courses) ? 'disabled' : ''; ?>>
                                <i class="fas fa-paper-plane mr-2"></i> Publish Lesson
                            </button>
                        </div>
                    </form>
                </div>                
                <script>
                    function toggleContentInput() {
                        const type = document.getElementById('content_type').value;
                        document.getElementById('youtube_input_group').style.display = (type === 'youtube') ? 'block' : 'none';
                        document.getElementById('video_input_group').style.display = (type === 'video') ? 'block' : 'none';
                    }
                </script>
                <h2 class="text-xl font-bold text-white mb-6">Lesson Library</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php
                    $lessons_query = @mysqli_query($conn, "SELECT l.*, p.name as course_name FROM lessons l JOIN products p ON l.course_id = p.id WHERE p.instructor_id IN ('$id_list') ORDER BY l.id DESC");
                    
                    if ($lessons_query && mysqli_num_rows($lessons_query) > 0) {
                        while ($lesson = mysqli_fetch_assoc($lessons_query)) {
                            $is_youtube = $lesson['content_type'] == 'youtube';
                    ?>
                        <div class="glass-panel rounded-2xl p-6 flex flex-col h-full relative group border border-slate-700/50 hover:border-indigo-500/50 transition-all shadow-lg hover:shadow-indigo-500/10">
                            <div class="flex items-center gap-2 mb-4">
                                <?php if($is_youtube): ?>
                                    <span class="bg-red-500/10 text-red-400 text-xs px-2.5 py-1 rounded-md font-bold flex items-center gap-1.5"><i class="fab fa-youtube"></i> YouTube</span>
                                <?php else: ?>
                                    <span class="bg-indigo-500/10 text-indigo-400 text-xs px-2.5 py-1 rounded-md font-bold flex items-center gap-1.5"><i class="fas fa-file-video"></i> MP4 File</span>
                                <?php endif; ?>
                                <span class="text-xs text-slate-400 font-medium truncate bg-slate-800/50 px-2 py-1 rounded-md"><?php echo htmlspecialchars($lesson['course_name']); ?></span>
                            </div>
                            <h3 class="text-lg text-white font-bold mb-4 line-clamp-2 leading-tight"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                            
                            <div class="mt-auto pt-5 border-t border-slate-700/50 flex justify-between items-center">
                                <a href="<?php echo htmlspecialchars($lesson['content_link']); ?>" target="_blank" class="text-indigo-400 hover:text-indigo-300 text-sm font-semibold transition-colors flex items-center gap-1.5">
                                    <i class="fas fa-play-circle"></i> Preview
                                </a>
                                <form action="?tab=courses" method="POST" onsubmit="return confirm('Are you sure you want to delete this lesson? This action cannot be undone.');">
                                    <input type="hidden" name="lesson_id" value="<?php echo $lesson['id']; ?>">
                                    <button type="submit" name="delete_lesson" class="w-8 h-8 rounded-lg bg-slate-800/50 text-slate-400 hover:bg-red-500/20 hover:text-red-400 flex items-center justify-center transition-colors" title="Delete Lesson">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php 
                        }
                    } else {
                        echo '<div class="col-span-full p-12 text-center glass-panel rounded-2xl"><div class="w-16 h-16 mx-auto bg-slate-800/50 rounded-full flex items-center justify-center mb-4"><i class="fas fa-film text-2xl text-slate-500"></i></div><p class="text-slate-400">Your library is empty. Upload your first lesson above.</p></div>';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <?php if ($tab == 'assignments'): ?>
                <header class="mb-10">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Assignments Setup</h1>
                    <p class="text-slate-400">Create actionable tasks for your students to test their knowledge.</p>
                </header>
                <div class="glass-panel p-8 md:p-10 rounded-2xl shadow-2xl mb-12 border border-slate-700/50">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <i class="fas fa-plus-square text-indigo-400"></i> New Assignment
                    </h2>              
                    <form action="?tab=assignments" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-300">Target Course</label>
                                <select name="course_id" class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all appearance-none" required style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394A3B8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto;">
                                    <?php if(empty($my_courses)): ?>
                                        <option value="" disabled selected>No courses available.</option>
                                    <?php else: ?>
                                        <option value="" disabled selected>Select a course...</option>
                                        <?php foreach($my_courses as $course): ?>
                                            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-slate-300">Due Date</label>
                                <input type="date" name="due_date" min="<?php echo date('Y-m-d'); ?>" class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" required>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-300">Assignment Title</label>
                            <input type="text" name="title" placeholder="e.g. Build a Responsive Portfolio Website" class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-300">Instructions & Guidelines</label>
                            <textarea name="description" rows="5" placeholder="Clearly describe what the students need to do, the requirements, and how they should submit..." class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-xl px-4 py-3.5 text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all resize-none" required></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="save_assignment" class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transform hover:-translate-y-0.5 transition-all w-full md:w-auto" <?php echo empty($my_courses) ? 'disabled' : ''; ?>>
                                <i class="fas fa-check mr-2"></i> Create Assignment
                            </button>
                        </div>
                    </form>
                </div>
                <h2 class="text-xl font-bold text-white mb-6">Active Assignments</h2>
                <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl border border-slate-700/50">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-800/40 border-b border-slate-700/50">
                                <tr>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase w-1/2">Assignment Details</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Course</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Due Date</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                <?php
                                $fetch_assignments = @mysqli_query($conn, "
                                    SELECT a.*, p.name as course_name 
                                    FROM assignments a 
                                    JOIN products p ON a.course_id = p.id 
                                    WHERE p.instructor_id IN ('$id_list') 
                                    ORDER BY a.due_date DESC
                                ");
                                if ($fetch_assignments && mysqli_num_rows($fetch_assignments) > 0) {
                                    while ($assign = mysqli_fetch_assoc($fetch_assignments)) {
                                        $is_past = $assign['due_date'] < date('Y-m-d');
                                ?>
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="p-5">
                                            <div class="font-bold text-white mb-1"><?php echo htmlspecialchars($assign['title']); ?></div>
                                            <div class="text-xs text-slate-400 line-clamp-2 pr-4 leading-relaxed" title="<?php echo htmlspecialchars($assign['description']); ?>">
                                                <?php echo htmlspecialchars($assign['description']); ?>
                                            </div>
                                        </td>
                                        <td class="p-5">
                                            <span class="inline-block px-3 py-1 bg-slate-800 rounded-md text-xs font-medium text-slate-300 border border-slate-700">
                                                <?php echo htmlspecialchars($assign['course_name']); ?>
                                            </span>
                                        </td>
                                        <td class="p-5">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold <?php echo $is_past ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20'; ?>">
                                                <i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($assign['due_date'])); ?>
                                            </span>
                                        </td>
                                        <td class="p-5 text-right">
                                            <form action="?tab=assignments" method="POST" onsubmit="return confirm('Delete this assignment permanently?');">
                                                <input type="hidden" name="assignment_id" value="<?php echo $assign['id']; ?>">
                                                <button type="submit" name="delete_assignment" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-slate-800/50 text-slate-400 hover:bg-red-500/20 hover:text-red-400 transition-colors" title="Delete Assignment">
                                                    <i class="fas fa-trash-alt text-sm"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="p-12 text-center text-slate-500"><div class="w-16 h-16 mx-auto bg-slate-800/50 rounded-full flex items-center justify-center mb-3"><i class="fas fa-folder-open text-2xl"></i></div><p>No assignments created yet.</p></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($tab == 'grading'): ?>
                <header class="mb-10">
                    <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Grade Submissions</h1>
                    <p class="text-slate-400">Review student work, provide valuable feedback, and issue grades.</p>
                </header>
                <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl border border-slate-700/50">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-slate-800/40 border-b border-slate-700/50">
                                <tr>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Student</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Assignment</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Work</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase">Status</th>
                                    <th class="p-5 text-xs font-bold tracking-wider text-slate-400 uppercase text-right">Evaluation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                <?php
                                $submissions_query = "
                                    SELECT 
                                        sub.id AS submission_id, sub.file_path, sub.grade, sub.feedback, sub.status,
                                        a.title AS assignment_title, 
                                        COALESCE(u.name" . ($has_students ? ", s.name" : "") . ($has_instructors ? ", i.name" : "") . ", 'Unknown Student') AS student_name
                                    FROM submissions sub
                                    JOIN assignments a ON sub.assignment_id = a.id
                                    JOIN products p ON a.course_id = p.id
                                    LEFT JOIN user_form u ON sub.user_id = u.id
                                    " . ($has_students ? "LEFT JOIN students s ON sub.user_id = s.id" : "") . "
                                    " . ($has_instructors ? "LEFT JOIN instructors i ON sub.user_id = i.id" : "") . "
                                    WHERE p.instructor_id IN ('$id_list')
                                    ORDER BY sub.status ASC, sub.id DESC
                                ";
                                
                                $fetch_submissions = @mysqli_query($conn, $submissions_query);

                                if ($fetch_submissions && mysqli_num_rows($fetch_submissions) > 0) {
                                    while ($sub = mysqli_fetch_assoc($fetch_submissions)) {
                                        $is_graded = $sub['status'] == 'Graded';
                                ?>
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="p-5 font-semibold text-white"><?php echo htmlspecialchars($sub['student_name']); ?></td>
                                        <td class="p-5 text-sm text-slate-300 max-w-[200px] truncate" title="<?php echo htmlspecialchars($sub['assignment_title']); ?>"><?php echo htmlspecialchars($sub['assignment_title']); ?></td>
                                        <td class="p-5">
                                            <a href="<?php echo htmlspecialchars($sub['file_path']); ?>" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 text-xs font-bold transition-colors border border-indigo-500/20">
                                                <i class="fas fa-file-download"></i> Download
                                            </a>
                                        </td>
                                        <td class="p-5">
                                            <?php if($is_graded): ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/20">
                                                    <i class="fas fa-check-circle"></i> Graded
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-500/10 text-amber-400 text-xs font-bold border border-amber-500/20 animate-pulse">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td class="p-5 text-right">
                                            <form action="?tab=grading" method="POST" class="flex items-center justify-end gap-2">
                                                <input type="hidden" name="submission_id" value="<?php echo $sub['submission_id']; ?>">
                                                
                                                <div class="relative w-20">
                                                    <input type="number" name="grade" placeholder="0" max="100" min="0" value="<?php echo htmlspecialchars($sub['grade'] ?? ''); ?>" class="w-full bg-[#0f172a]/80 border border-slate-700 rounded-lg py-2 pl-3 pr-6 text-white text-sm font-bold text-center focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder:text-slate-600 placeholder:font-normal" required>
                                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 text-xs font-bold">%</span>
                                                </div>

                                                <input type="text" name="feedback" placeholder="Feedback..." value="<?php echo htmlspecialchars($sub['feedback'] ?? ''); ?>" class="w-48 bg-[#0f172a]/80 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all placeholder:text-slate-600">
                                                
                                                <button type="submit" name="submit_grade" class="px-4 py-2 bg-slate-700 hover:bg-indigo-600 text-white rounded-lg text-sm font-bold transition-colors shadow-md">
                                                    <?php echo $is_graded ? 'Update' : 'Save'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="p-12 text-center text-slate-500"><div class="w-16 h-16 mx-auto bg-slate-800/50 rounded-full flex items-center justify-center mb-3"><i class="fas fa-inbox text-2xl"></i></div><p>No pending submissions found.</p></td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>
