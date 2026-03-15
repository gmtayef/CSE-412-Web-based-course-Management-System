<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$student_activity_query = "
    SELECT u.name as student_name, p.name as course_name, e.enrollment_date 
    FROM enrollments e
    JOIN user_form u ON e.user_id = u.id
    JOIN products p ON e.course_id = p.id
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
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                BOT <span class="text-indigo-400">Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin_panel.php" class="flex items-center px-4 py-2 rounded-lg bg-gray-700"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="admin.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-book mr-3"></i>Manage Courses</a>
                <a href="add_instructor.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-user-plus mr-3"></i>Manage Instructors</a>
                <a href="index.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-home mr-3"></i>View Main Site</a>
				<a href="logout.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
            </nav>
        </aside>
        <main class="flex-1 p-6 md:p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Activity Dashboard</h1>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Student Enrollment Activity</h2>
                    <div class="overflow-y-auto h-96">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3">Student</th>
                                    <th class="px-6 py-3">Course</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($student_activity_result)): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Instructor Assignment Activity</h2>
                    <div class="overflow-y-auto h-96">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3">Instructor</th>
                                    <th class="px-6 py-3">Course</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($instructor_activity_result)): ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['instructor_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('M d, Y', strtotime($row['assigned_date'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
