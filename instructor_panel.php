<?php
@include 'config.php';
session_start();


if (!isset($_SESSION['instructor_name'])) {
    header('location:login_form.php');
    exit();
}

$instructor_name = $_SESSION['instructor_name'];
$instructor_email = $_SESSION['instructor_email']; 


$get_real_id = mysqli_query($conn, "SELECT id FROM instructors WHERE email = '$instructor_email'");
if ($get_real_id && mysqli_num_rows($get_real_id) > 0) {
    $inst_data = mysqli_fetch_assoc($get_real_id);
    $instructor_id = $inst_data['id']; 
} else {
    $instructor_id = 0; 
}

$message = [];

$tab = $_GET['tab'] ?? 'dashboard';


$courses_query = mysqli_query($conn, "SELECT id, name FROM products WHERE instructor_id = '$instructor_id'");
$my_courses = [];
if ($courses_query && mysqli_num_rows($courses_query) > 0) {
    while ($row = mysqli_fetch_assoc($courses_query)) {
        $my_courses[] = $row;
    }
}


if (isset($_POST['add_content'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $lesson_title = mysqli_real_escape_string($conn, $_POST['lesson_title']);
    $content_type = mysqli_real_escape_string($conn, $_POST['content_type']);
    $youtube_url = mysqli_real_escape_string($conn, $_POST['youtube_url']);
    
   
    $verify_course = mysqli_query($conn, "SELECT id FROM products WHERE id = '$course_id' AND instructor_id = '$instructor_id'");
    
    if (mysqli_num_rows($verify_course) > 0) {
        $video_file = $_FILES['video_file']['name'];
        $video_tmp_name = $_FILES['video_file']['tmp_name'];
        $video_folder = 'uploaded_videos/' . time() . '_' . $video_file;

        if ($content_type == 'video' && !empty($video_file)) {
            if (!is_dir('uploaded_videos')) mkdir('uploaded_videos', 0777, true);
            move_uploaded_file($video_tmp_name, $video_folder);
            $content_link = $video_folder;
        } else {
            $content_link = $youtube_url;
        }

          $message[] = "Lesson added successfully to your course!";
    } else {
        $message[] = "Error: You do not have permission to add content to this course.";
    }
}


if (isset($_POST['save_assignment'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    
    $verify_course = mysqli_query($conn, "SELECT id FROM products WHERE id = '$course_id' AND instructor_id = '$instructor_id'");

    if (mysqli_num_rows($verify_course) > 0) {
       
        $message[] = "Assignment created successfully for your course!";
    } else {
        $message[] = "Error: You do not have permission to add assignments to this course.";
    }
}


if (isset($_POST['submit_grade'])) {
    $submission_id = mysqli_real_escape_string($conn, $_POST['submission_id']);
    $grade = mysqli_real_escape_string($conn, $_POST['grade']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

   
    $update_query = "UPDATE submissions SET grade='$grade', feedback='$feedback', status='Graded' WHERE id='$submission_id'";
    $run_update = mysqli_query($conn, $update_query);

    if($run_update){
        $message[] = "Grade submitted successfully!";
    } else {
        $message[] = "Error: Could not submit grade.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Panel - BOT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
    </style>
</head>
<body class="bg-[#0f172a] text-white flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#1e293b] flex flex-col border-r border-slate-800 shadow-xl z-10 shrink-0">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-2xl font-bold tracking-tight">BOT <span class="text-[#6366f1]">Instructor</span></h2>
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="?tab=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors <?php echo $tab == 'dashboard' ? 'bg-[#6366f1] text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fas fa-home w-5 text-center"></i> Dashboard
            </a>
            
            <a href="?tab=courses" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors <?php echo $tab == 'courses' ? 'bg-[#6366f1] text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fas fa-video w-5 text-center"></i> Manage Content
            </a>

            <a href="?tab=assignments" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors <?php echo $tab == 'assignments' ? 'bg-[#6366f1] text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fas fa-clipboard-list w-5 text-center"></i> Assignments
            </a>

            <a href="?tab=grading" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-colors <?php echo $tab == 'grading' ? 'bg-[#6366f1] text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fas fa-check-circle w-5 text-center"></i> Grade Students
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-slate-800 hover:text-red-300 rounded-lg font-medium transition-colors">
                <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-10">
        
        <header class="mb-8 flex justify-between items-end border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-3xl font-bold mb-1">Welcome, <?php echo htmlspecialchars($instructor_name); ?>!</h1>
                <p class="text-slate-400 text-sm">Manage your courses, assignments, and students from this dashboard.</p>
            </div>
        </header>

        <?php if (!empty($message)): ?>
            <?php foreach($message as $msg): ?>
                <div class="mb-6 p-4 rounded-lg <?php echo strpos($msg, 'Error') !== false ? 'bg-red-500/10 border-red-500/50 text-red-400' : 'bg-green-500/10 border-green-500/50 text-green-400'; ?> border">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($tab == 'dashboard'): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#1e293b] p-6 rounded-xl border border-slate-700 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-slate-400 font-medium">My Courses</h3>
                        <i class="fas fa-book text-indigo-500 text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php echo count($my_courses); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'courses'): ?>
            <div class="bg-[#1e293b] p-8 rounded-xl border border-slate-700 shadow-lg max-w-3xl">
                <h2 class="text-xl font-bold mb-6 border-b border-slate-700 pb-2">Upload Lesson Video or YouTube URL</h2>
                
                <form action="?tab=courses" method="POST" enctype="multipart/form-data" class="space-y-5">
                    
                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Select Course</label>
                        <select name="course_id" class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500" required>
                            <?php if(empty($my_courses)): ?>
                                <option value="" disabled selected>No courses assigned to you yet.</option>
                            <?php else: ?>
                                <option value="" disabled selected>-- Choose your course --</option>
                                <?php foreach($my_courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Lesson Title</label>
                        <input type="text" name="lesson_title" placeholder="e.g. Introduction to HTML" class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Content Type</label>
                            <select name="content_type" id="content_type" class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500" onchange="toggleContentInput()">
                                <option value="youtube">YouTube URL</option>
                                <option value="video">Upload MP4 Video</option>
                            </select>
                        </div>
                    </div>

                    <div id="youtube_input_group">
                        <label class="block text-sm text-slate-400 mb-2">YouTube URL</label>
                        <input type="url" name="youtube_url" placeholder="https://youtube.com/watch?v=..." class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500">
                    </div>

                    <div id="video_input_group" class="hidden">
                        <label class="block text-sm text-slate-400 mb-2">Upload Video File</label>
                        <input type="file" name="video_file" accept="video/mp4,video/mkv" class="w-full p-2 rounded-lg bg-[#0f172a] border border-slate-700 text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                    </div>

                    <button type="submit" name="add_content" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-bold transition-colors shadow-lg" <?php echo empty($my_courses) ? 'disabled' : ''; ?>>Save Lesson Content</button>
                </form>
            </div>
            
            <script>
                function toggleContentInput() {
                    const type = document.getElementById('content_type').value;
                    document.getElementById('youtube_input_group').style.display = (type === 'youtube') ? 'block' : 'none';
                    document.getElementById('video_input_group').style.display = (type === 'video') ? 'block' : 'none';
                }
            </script>
        <?php endif; ?>

        <?php if ($tab == 'assignments'): ?>
             <div class="bg-[#1e293b] p-8 rounded-xl border border-slate-700 shadow-lg max-w-4xl mb-10">
                <h2 class="text-xl font-bold mb-6 border-b border-slate-700 pb-2">Create Assignment</h2>
                
                <form action="?tab=assignments" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Target Course</label>
                            <select name="course_id" class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500" required>
                                <?php if(empty($my_courses)): ?>
                                    <option value="" disabled selected>No courses assigned to you yet.</option>
                                <?php else: ?>
                                    <option value="" disabled selected>-- Choose your course --</option>
                                    <?php foreach($my_courses as $course): ?>
                                        <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Due Date</label>
                            <input type="date" name="due_date" class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-slate-300 outline-none focus:border-indigo-500" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Assignment Title</label>
                        <input type="text" name="title" placeholder="e.g. Build a Portfolio Website" class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500" required>
                    </div>

                    <div>
                        <label class="block text-sm text-slate-400 mb-2">Instructions / Description</label>
                        <textarea name="description" rows="4" placeholder="Describe what the students need to do..." class="w-full p-3 rounded-lg bg-[#0f172a] border border-slate-700 text-white outline-none focus:border-indigo-500" required></textarea>
                    </div>

                    <button type="submit" name="save_assignment" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-bold transition-colors shadow-lg" <?php echo empty($my_courses) ? 'disabled' : ''; ?>>Save Assignment</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($tab == 'grading'): ?>
            <div class="bg-[#1e293b] rounded-xl border border-slate-700 shadow-lg overflow-hidden">
                <div class="p-6 border-b border-slate-700 flex justify-between items-center">
                    <h2 class="text-xl font-bold">Student Submissions</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-[#0f172a] text-slate-400 uppercase text-xs font-semibold">
                            <tr>
                                <th class="p-4">Student Name</th>
                                <th class="p-4">Assignment</th>
                                <th class="p-4">Submitted File</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">Grade & Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php
                           
                            $fetch_submissions = mysqli_query($conn, "
                                SELECT 
                                    s.id AS submission_id, 
                                    s.file_path, 
                                    s.grade, 
                                    s.feedback, 
                                    s.status,
                                    a.title AS assignment_title, 
                                    u.name AS student_name
                                FROM submissions s
                                JOIN assignments a ON s.assignment_id = a.id
                                JOIN products p ON a.course_id = p.id
                                JOIN user_form u ON s.user_id = u.id
                                WHERE p.instructor_id = '$instructor_id'
                                ORDER BY s.status ASC, s.id DESC
                            ");

                            if ($fetch_submissions && mysqli_num_rows($fetch_submissions) > 0) {
                                while ($sub = mysqli_fetch_assoc($fetch_submissions)) {
                            ?>
                                <tr class="hover:bg-slate-800/50 transition-colors">
                                    <td class="p-4 font-medium"><?php echo htmlspecialchars($sub['student_name']); ?></td>
                                    <td class="p-4 text-sm text-slate-300"><?php echo htmlspecialchars($sub['assignment_title']); ?></td>
                                    <td class="p-4">
                                        <a href="<?php echo htmlspecialchars($sub['file_path']); ?>" download class="text-indigo-400 hover:underline flex items-center gap-2 text-sm">
                                            <i class="fas fa-download"></i> Download Work
                                        </a>
                                    </td>
                                    <td class="p-4">
                                        <?php if($sub['status'] == 'Graded'): ?>
                                            <span class="px-2 py-1 bg-green-500/10 text-green-400 border border-green-500/20 rounded text-xs font-bold">Graded</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded text-xs font-bold">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <form action="?tab=grading" method="POST">
                                        <input type="hidden" name="submission_id" value="<?php echo $sub['submission_id']; ?>">
                                        <td class="p-4 flex gap-2 justify-center items-center">
                                            <input type="number" name="grade" placeholder="Score (0-100)" max="100" min="0" value="<?php echo htmlspecialchars($sub['grade'] ?? ''); ?>" class="w-24 p-2 rounded bg-[#0f172a] border border-slate-700 text-white text-sm outline-none focus:border-indigo-500" required>
                                            <input type="text" name="feedback" placeholder="Feedback..." value="<?php echo htmlspecialchars($sub['feedback'] ?? ''); ?>" class="w-40 p-2 rounded bg-[#0f172a] border border-slate-700 text-white text-sm outline-none focus:border-indigo-500">
                                            <button type="submit" name="submit_grade" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded text-sm font-bold transition-colors">
                                                <?php echo ($sub['status'] == 'Graded') ? 'Update' : 'Grade'; ?>
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="5" class="p-4 text-center text-slate-500">No student submissions found for your courses yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>