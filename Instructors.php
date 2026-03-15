<?php
// FILE: Instructors.php
@include 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch instructors from database
$select_instructors = mysqli_query($conn, "SELECT * FROM `instructors`") or die('Query failed: ' . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Instructors - BOT Learning</title>

   <script src="https://cdn.tailwindcss.com"></script>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   
   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

   <style>
      body {
         font-family: 'Inter', sans-serif;
         background-color: #0f172a; /* Dark background */
      }
      
      /* Glass Navigation Effect */
      .glass-nav {
         background: rgba(255, 255, 255, 0.1);
         backdrop-filter: blur(10px);
         -webkit-backdrop-filter: blur(10px);
         border: 1px solid rgba(255, 255, 255, 0.1);
         box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
      }

      /* Card Animations */
      @keyframes fadeInUp {
         from { opacity: 0; transform: translateY(40px); }
         to { opacity: 1; transform: translateY(0); }
      }
      .card-entry { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
      .card-entry:nth-child(1) { animation-delay: 0.1s; }
      .card-entry:nth-child(2) { animation-delay: 0.2s; }
      .card-entry:nth-child(3) { animation-delay: 0.3s; }
      .card-entry:nth-child(4) { animation-delay: 0.4s; }
   </style>
</head>

<body class="text-white flex flex-col min-h-screen">

    <header class="relative h-[80vh] overflow-hidden"> 
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop" 
                 alt="Students learning" 
                 class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 h-full flex flex-col">
            
            <nav class="py-4 flex justify-between items-center glass-nav rounded-full mt-6 px-8 mx-2 md:mx-6">
                <a href="index.php" class="text-1xl font-bold tracking-wide">BOT <span class="text-indigo-400">Learning</span></a>
                
                <ul class="hidden md:flex items-center space-x-8 text-md font-medium text-gray-300">
                    <li><a href="index.php" class="hover:text-white transition border-b-2 border-transparent hover:border-indigo-500 pb-1">Home</a></li>
                    <li><a href="course-page.php" class="hover:text-white transition border-b-2 border-transparent hover:border-indigo-500 pb-1">Courses</a></li>
                    <li><a href="instructors.php" class="text-white font-bold border-b-2 border-indigo-500 pb-1">Instructors</a></li>
                    <li><a href="about.php" class="hover:text-white transition border-b-2 border-transparent hover:border-indigo-500 pb-1">About</a></li>
                    <li><a href="contact.php" class="hover:text-white transition border-b-2 border-transparent hover:border-indigo-500 pb-1">Contact</a></li>
                </ul>

                <div class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['admin_name'])): ?>
                        <a href="admin_panel.php" class="hover:text-indigo-300 transition text-sm"><i class="fas fa-user-shield mr-1"></i> Admin</a>
                        <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 rounded-full font-bold text-sm transition">Logout</a>
                    <?php elseif (isset($_SESSION['instructor_name'])): ?>
                        <a href="instructor_panel.php" class="hover:text-indigo-300 transition text-sm"><i class="fas fa-chalkboard-teacher mr-1"></i> Panel</a>
                        <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 rounded-full font-bold text-sm transition">Logout</a>
                    <?php elseif (isset($_SESSION['user_name'])): ?>
                        <a href="student_panel.php" class="hover:text-indigo-300 transition text-sm"><i class="fas fa-user-graduate mr-1"></i> My Courses</a>
                         <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 rounded-full font-bold text-sm transition">Logout</a>
                    <?php else: ?>
                        <a href="login_form.php" class="hover:text-white font-medium transition">Login</a>
                        <a href="register_form.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-full font-bold transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="flex-grow flex flex-col justify-center items-center text-center mt-8">
                <span class="text-indigo-400 font-bold tracking-widest uppercase text-sm mb-4 animate-pulse">
                    World Class Mentors
                </span>
                <h1 class="text-5xl md:text-3xl font-extrabold text-white mb-6 drop-shadow-xl tracking-tight">
                    Meet Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">Expert</span> Instructors
                </h1>
                <p class="text-gray-300 text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed">
                    Industry leaders, passionate educators, and creative visionaries dedicated to accelerating your career journey.
                </p>
            </div>

        </div>
    </header>

    <main class="flex-grow">
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-20">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
             
             <?php
             if(mysqli_num_rows($select_instructors) > 0){
                while($fetch_instructor = mysqli_fetch_assoc($select_instructors)){
                   
                   // ==========================================
                   // DYNAMIC IMAGE FETCHING LOGIC
                   // ==========================================
                   $image_name = $fetch_instructor['image'] ?? '';
                   
                   // NOTE: If you save instructor images in 'instructor_img', change the folder name below!
                   $img_folder = 'uploaded_img/'; 
                   
                   if (!empty($image_name)) {
                       $img_path = $img_folder . $image_name;
                   } else {
                       // Generate placeholder initials if no image is found in database
                       $img_path = "https://placehold.co/400x400/1e293b/FFF?text=" . strtoupper(substr($fetch_instructor['name'], 0, 1));
                   }
             ?>
                <div class="card-entry bg-[#1e293b] rounded-2xl p-8 flex flex-col items-center text-center shadow-2xl border border-gray-700/50 hover:border-indigo-500/50 transition-all duration-300 hover:-translate-y-3 group">
                   
                   <div class="relative w-40 h-40 mb-6">
                       <div class="absolute inset-0 rounded-full bg-indigo-500 blur-md opacity-20 group-hover:opacity-60 transition duration-500"></div>
                       <div class="relative w-full h-full rounded-full p-1 bg-gradient-to-tr from-indigo-500 to-purple-500">
                          <img src="<?php echo htmlspecialchars($img_path); ?>" 
                               alt="<?php echo htmlspecialchars($fetch_instructor['name']); ?>" 
                               class="w-full h-full rounded-full object-cover border-4 border-[#1e293b] bg-[#1e293b]">
                       </div>
                   </div>

                   <h3 class="text-2xl font-bold text-white mb-2 tracking-wide group-hover:text-indigo-400 transition-colors">
                      <?php echo htmlspecialchars($fetch_instructor['name']); ?>
                   </h3>
                   
                   <p class="text-indigo-400 text-sm font-semibold uppercase tracking-wider mb-6">
                      <?php echo htmlspecialchars($fetch_instructor['expertise']); ?>
                   </p>

                   <div class="mt-auto w-full flex gap-3">
                      <a href="mailto:<?php echo htmlspecialchars($fetch_instructor['email']); ?>" class="flex-1 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium transition-colors flex items-center justify-center gap-2 shadow-lg shadow-indigo-900/20">
                         <i class="fas fa-envelope"></i> Contact
                      </a>
                      <button class="w-10 h-10 rounded-lg bg-gray-700 hover:bg-gray-600 text-gray-300 transition-colors">
                         <i class="fab fa-linkedin-in"></i>
                      </button>
                   </div>
                </div>
             <?php
                }
             } else {
                echo '<div class="col-span-full text-center text-gray-400 py-12 bg-[#1e293b] rounded-xl border border-dashed border-gray-700">';
                echo '<i class="fas fa-user-slash text-4xl mb-4 opacity-50"></i>';
                echo '<p>No instructors found yet.</p>';
                echo '</div>';
             }
             ?>

          </div>
        </section>
    </main>

    <footer class="bg-[#1e293b] pt-16 border-t border-slate-800">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">BOT Learning</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">The future of education is here. Join us and unlock your potential.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="about.php" class="hover:text-indigo-400 transition">About Us</a></li>
                        <li><a href="contact.php" class="hover:text-indigo-400 transition">Contact</a></li>
                        <li><a href="course-page.php" class="hover:text-indigo-400 transition">All Courses</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Contact Info</h4>
                    <div class="text-sm text-gray-400 space-y-3">
                        <p><i class="fas fa-map-marker-alt mr-2 text-indigo-400"></i> Aftabnagor, Dhaka</p>
                        <p><i class="fas fa-phone mr-2 text-indigo-400"></i> +8801700000000</p>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Connect With Us</h4>
                    <div class="flex space-x-4 text-xl text-gray-400">
                        <a href="#" class="hover:text-white transition transform hover:scale-110"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-white transition transform hover:scale-110"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-white transition transform hover:scale-110"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-800 py-6 text-center text-sm text-gray-500">
                <p>&copy; <?php echo date("Y"); ?> BOT Learning. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>