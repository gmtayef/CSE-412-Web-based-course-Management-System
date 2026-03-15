<?php
// Turn on error reporting to stop the "blank white screen"
error_reporting(E_ALL);
ini_set('display_errors', 1);

@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$message = '';

$cart_count_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM cart WHERE user_id = '$user_id'");
$cart_count_data = mysqli_fetch_assoc($cart_count_query);
$cart_count = $cart_count_data['count'] ?? 0;


if (isset($_POST['submit_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    $file_name = $_FILES['assignment_file']['name'];
    $file_tmp = $_FILES['assignment_file']['tmp_name'];
    
    
    if (!is_dir('uploaded_assignments')) {
        mkdir('uploaded_assignments', 0777, true);
    }
    
    $file_folder = 'uploaded_assignments/' . time() . '_' . $file_name;

    if (!empty($file_name)) {
        if (move_uploaded_file($file_tmp, $file_folder)) {
            $message = "Assignment submitted successfully!";
        } else {
            $message = "Failed to upload file. Check folder permissions.";
        }
    } else {
        $message = "Please select a file to upload.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - BOT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#0f172a] text-white flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#1e293b] flex flex-col border-r border-slate-800 shadow-xl z-10 shrink-0">
        <div class="p-6 border-b border-slate-800">
            <h2 class="text-2xl font-bold tracking-tight">BOT <span class="text-[#6366f1]">Student</span></h2>
        </div>

        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="student_panel.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                My Courses
            </a>
            <a href="explore_courses.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Explore Courses
            </a>
            <a href="assignments.php" class="flex items-center gap-3 px-4 py-3 bg-[#6366f1] text-white rounded-lg font-medium shadow-sm transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Assignments
            </a>
			 
            <a href="messages.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                Messages
            </a>
			 <a href="cart.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    My Cart
                </div>
				</a>
                <?php if($cart_count > 0): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg font-medium transition-colors mt-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                View Site
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-slate-800 hover:text-red-300 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto bg-[#0f172a] p-10">
        <header class="mb-8">
            <h1 class="text-3xl font-bold mb-2">My Assignments</h1>
            <p class="text-slate-400 text-sm">Review your pending tasks and upload your completed files below.</p>
        </header>

        <?php if ($message != ''): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo strpos($message, 'success') !== false ? 'bg-green-500/10 border-green-500/50 text-green-400' : 'bg-red-500/10 border-red-500/50 text-red-400'; ?> border">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            
            <div class="bg-[#1e293b] rounded-xl shadow-lg border border-slate-700 p-6 flex flex-col md:flex-row gap-6 items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs font-semibold px-2.5 py-1 bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 rounded-full">Pending</span>
                        <span class="text-sm font-medium text-slate-400">Web Development Bootcamp</span>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Build a Personal Portfolio</h3>
                    <p class="text-slate-400 text-sm mb-4">Create a responsive personal portfolio using HTML, CSS, and basic JavaScript. Make sure to include an "About Me", "Projects", and "Contact" section. Zip your files before uploading.</p>
                </div>

                <div class="w-full md:w-72 bg-[#0f172a] p-5 rounded-lg border border-slate-800 shrink-0">
                    <h4 class="text-sm font-semibold text-white mb-3">Submit your work</h4>
                    <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                        <input type="hidden" name="assignment_id" value="1">
                        
                        <label class="block">
                            <span class="sr-only">Choose file</span>
                            <input type="file" name="assignment_file" required class="block w-full text-sm text-slate-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-[#6366f1] file:text-white
                                hover:file:bg-indigo-500 file:cursor-pointer file:transition-colors
                                border border-slate-700 rounded-lg bg-[#1e293b]">
                        </label>
                        
                        <button type="submit" name="submit_assignment" class="w-full bg-[#6366f1] text-white py-2 rounded-lg text-sm font-medium hover:bg-indigo-500 transition-colors">
                            Upload Assignment
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>
</body>
</html>