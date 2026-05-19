<?php
@include 'config.php';
session_start();
if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

if (isset($_GET['approve_user']) && isset($_GET['approve_course'])) {
    $a_user = mysqli_real_escape_string($conn, $_GET['approve_user']);
    $a_course = mysqli_real_escape_string($conn, $_GET['approve_course']);
    mysqli_query($conn, "UPDATE enrollments SET status = 'approved' WHERE user_id = '$a_user' AND course_id = '$a_course'");
    header('location:admin_panel.php');
    exit();
}
$pending_query = "
    SELECT e.user_id, e.course_id, u.name as student_name, p.name as course_name, e.enrollment_date, 
           (SELECT payment_method FROM payments WHERE user_id = e.user_id ORDER BY id DESC LIMIT 1) as payment_details
    FROM enrollments e
    JOIN user_form u ON e.user_id = u.id
    JOIN products p ON e.course_id = p.id
    WHERE e.status = 'pending'
    ORDER BY e.enrollment_date DESC";
$pending_result = mysqli_query($conn, $pending_query);
$student_activity_query = "
    SELECT u.name as student_name, p.name as course_name, e.enrollment_date 
    FROM enrollments e
    JOIN user_form u ON e.user_id = u.id
    JOIN products p ON e.course_id = p.id
    WHERE e.status = 'approved' OR e.status IS NULL
    ORDER BY e.enrollment_date DESC";
$student_activity_result = mysqli_query($conn, $student_activity_query);
$instructor_activity_query = "
    SELECT i.name as instructor_name, p.name as course_name, ca.assigned_date
    FROM course_activity ca
    JOIN instructors i ON ca.instructor_id = i.id
    JOIN products p ON ca.course_id = p.id
    ORDER BY ca.assigned_date DESC";
$instructor_activity_result = mysqli_query($conn, $instructor_activity_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
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
                <a href="admin_panel.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25 border-l-[3px] border-indigo-300">
                    <i class="fas fa-grid-2 text-lg w-8 text-indigo-100"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>             
                <a href="admin.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-layer-group text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
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
    <main class="flex-1 overflow-y-auto h-screen pt-20 md:pt-0 relative">
        <div class="absolute top-0 left-0 w-full h-72 bg-indigo-600 -z-10 rounded-bl-[4rem]"></div>       
        <div class="p-6 md:p-12 max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-10 text-white">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Overview Dashboard</h1>
                    <p class="text-indigo-100 mt-2 font-medium">Here's what is happening with your platform today.</p>
                </div>
            </div>           
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 p-8 mb-10 border border-slate-100">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center">
                        <span class="bg-amber-100 p-2 rounded-lg mr-3 text-amber-500 shadow-inner"><i class="fas fa-bell"></i></span>
                        Action Required: Pending Approvals
                    </h2>
                    <?php if(mysqli_num_rows($pending_result) > 0): ?>
                        <span class="bg-amber-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md shadow-amber-500/30 animate-pulse">
                            <?php echo mysqli_num_rows($pending_result); ?> Pending
                        </span>
                    <?php endif; ?>
                </div>               
                <div class="overflow-y-auto max-h-[350px] pr-2">
                    <table class="w-full text-left border-collapse">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 font-semibold tracking-wider sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-4 rounded-tl-xl">Student Details</th>
                                <th class="px-6 py-4">Requested Course</th>
                                <th class="px-6 py-4">Payment Info</th>
                                <th class="px-6 py-4">Applied On</th>
                                <th class="px-6 py-4 text-right rounded-tr-xl">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(mysqli_num_rows($pending_result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($pending_result)): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-5 font-bold text-slate-900 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">
                                            <?php echo substr(htmlspecialchars($row['student_name']), 0, 1); ?>
                                        </div>
                                        <?php echo htmlspecialchars($row['student_name']); ?>
                                    </td>
                                    <td class="px-6 py-5 font-medium text-slate-700"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td class="px-6 py-5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <i class="fas fa-money-check-dollar mr-1.5 text-slate-400"></i>
                                            <?php echo htmlspecialchars($row['payment_details']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-sm text-slate-500 font-medium">
                                        <?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <a href="admin_panel.php?approve_user=<?php echo $row['user_id']; ?>&approve_course=<?php echo $row['course_id']; ?>" class="inline-flex items-center bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5">
                                            <i class="fas fa-check mr-2"></i> Approve
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500 bg-white">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-check-double text-2xl text-emerald-400"></i>
                                            </div>
                                            <p class="font-medium text-slate-600">All caught up! No pending approvals.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 flex flex-col h-[500px]">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <span class="bg-indigo-50 p-2 rounded-lg mr-3 text-indigo-500"><i class="fas fa-user-graduate"></i></span>
                        Recent Enrollments
                    </h2>
                    <div class="overflow-y-auto flex-1 pr-2">
                        <ul class="space-y-4">
                            <?php 
                            if(mysqli_num_rows($student_activity_result) > 0):
                                while($row = mysqli_fetch_assoc($student_activity_result)): 
                            ?>
                            <li class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition-colors flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white shadow-sm border border-slate-100 flex items-center justify-center shrink-0 text-slate-400">
                                    <i class="fas fa-arrow-right-to-bracket"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-800">
                                        <span class="font-bold text-indigo-600"><?php echo htmlspecialchars($row['student_name']); ?></span> enrolled in
                                    </p>
                                    <p class="font-bold text-slate-900 mt-0.5"><?php echo htmlspecialchars($row['course_name']); ?></p>
                                </div>
                                <div class="text-xs font-bold text-slate-400 bg-white px-2 py-1 rounded-md border border-slate-100 whitespace-nowrap">
                                    <?php echo date('M d', strtotime($row['enrollment_date'])); ?>
                                </div>
                            </li>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <div class="h-full flex flex-col items-center justify-center text-slate-400">
                                    <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                                    <p>No recent student activity.</p>
                                </div>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>               
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 flex flex-col h-[500px]">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <span class="bg-emerald-50 p-2 rounded-lg mr-3 text-emerald-500"><i class="fas fa-chalkboard-user"></i></span>
                        Instructor Assignments
                    </h2>
                    <div class="overflow-y-auto flex-1 pr-2">
                        <ul class="space-y-4">
                            <?php 
                            if(mysqli_num_rows($instructor_activity_result) > 0):
                                while($row = mysqli_fetch_assoc($instructor_activity_result)): 
                            ?>
                            <li class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:border-emerald-200 transition-colors flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-white shadow-sm border border-slate-100 flex items-center justify-center shrink-0 text-slate-400">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-slate-800">
                                        <span class="font-bold text-emerald-600"><?php echo htmlspecialchars($row['instructor_name']); ?></span> assigned to teach
                                    </p>
                                    <p class="font-bold text-slate-900 mt-0.5"><?php echo htmlspecialchars($row['course_name']); ?></p>
                                </div>
                                <div class="text-xs font-bold text-slate-400 bg-white px-2 py-1 rounded-md border border-slate-100 whitespace-nowrap">
                                    <?php echo date('M d', strtotime($row['assigned_date'])); ?>
                                </div>
                            </li>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <div class="h-full flex flex-col items-center justify-center text-slate-400">
                                    <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                                    <p>No recent assignments.</p>
                                </div>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
