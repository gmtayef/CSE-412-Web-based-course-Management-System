<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BOT Learning - Unlock Your Potential</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; }
        
        
        .glass-nav {
           background: rgba(255, 255, 255, 0.1);
           backdrop-filter: blur(10px);
           -webkit-backdrop-filter: blur(10px);
           border: 1px solid rgba(255, 255, 255, 0.1);
           box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

       
        .course-card {
            background: linear-gradient(145deg, #111111, #1a1a1a);
            border: 1px solid #2a2a2a;
        }
        
        .course-card:hover {
            border-color: #4f46e5;
            box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.3);
        }

        /* Feature Boxes */
        .feature-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="text-white">

    <header class="relative h-[80vh] overflow-hidden"> 
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop" 
                 alt="Students learning" 
                 class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-black/70"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 h-full flex flex-col">
            
            <nav class="py-4 flex justify-between items-center glass-nav rounded-full mt-6 px-8 mx-2 md:mx-6 transition-colors duration-300">
                <a href="index.php" class="text-1xl font-bold tracking-wide">BOT <span class="text-indigo-400">Learning</span></a>
                
                <ul class="hidden md:flex items-center space-x-8 text-md font-medium text-gray-300">
                    <li><a href="index.php" class="text-white font-bold border-b-2 border-indigo-500 pb-1 transition-all duration-300">Home</a></li>
                    
                    <li><a href="course-page.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Courses</a></li>
                    <li><a href="instructors.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Instructors</a></li>
                    <li><a href="about.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">About</a></li>
                    <li><a href="contact.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Contact</a></li>
                </ul>

                <div class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['admin_name'])): ?>
                        <a href="admin_panel.php" class="hover:text-indigo-300 transition text-sm font-medium"><i class="fas fa-user-shield mr-1"></i> Admin</a>
                        <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 rounded-full font-bold text-sm transition">Logout</a>
                    
                    <?php elseif (isset($_SESSION['instructor_name'])): ?>
                        <a href="instructor_panel.php" class="hover:text-indigo-300 transition text-sm font-medium"><i class="fas fa-chalkboard-teacher mr-1"></i> Panel</a>
                        <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 rounded-full font-bold text-sm transition">Logout</a>
                    
                    <?php elseif (isset($_SESSION['user_name'])): ?>
                        <a href="student_panel.php" class="hover:text-indigo-300 transition text-sm font-medium"><i class="fas fa-user-graduate mr-1"></i> My Courses</a>
                        <a href="logout.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 rounded-full font-bold text-sm transition">Logout</a>
                    
                    <?php else: ?>
                        <a href="login_form.php" class="hover:text-white font-medium transition">Login</a>
                        <a href="register_form.php" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-full font-bold transition shadow-[0_0_15px_rgba(79,70,229,0.3)]">Sign Up</a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="flex-1 flex flex-col justify-center items-center text-center mt-[-40px]">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-5xl md:text-6xl lg:text-3xl font-black mb-6 leading-tight tracking-tight">
                        Unlock Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Potential</span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-10 text-center max-w-2xl mx-auto">
                        Learn from industry experts, build real-world projects, and accelerate your career with our premium online courses.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="course-page.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-full transition-all duration-300 transform hover:-translate-y-1 shadow-[0_0_20px_rgba(79,70,229,0.4)]">
                            Explore Courses
                        </a>
                        <a href="about.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 text-white font-bold py-4 px-8 rounded-full transition-all duration-300">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <section id="features" class="py-20 relative z-10 -mt-10">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="feature-box p-8 rounded-2xl transition-transform duration-300 hover:-translate-y-2">
                    <div class="w-14 h-14 rounded-full bg-indigo-900/50 flex items-center justify-center mb-6 text-indigo-400 text-2xl">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Expert-Led Courses</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Learn from industry professionals with years of real-world experience in top tech companies.</p>
                </div>
                
                <div class="feature-box p-8 rounded-2xl transition-transform duration-300 hover:-translate-y-2">
                    <div class="w-14 h-14 rounded-full bg-purple-900/50 flex items-center justify-center mb-6 text-purple-400 text-2xl">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Earn Certificates</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Get recognized for your skills with verified certificates upon course completion.</p>
                </div>

                <div class="feature-box p-8 rounded-2xl transition-transform duration-300 hover:-translate-y-2">
                    <div class="w-14 h-14 rounded-full bg-blue-900/50 flex items-center justify-center mb-6 text-blue-400 text-2xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Community Support</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Join a thriving community of learners. Share ideas, ask questions, and grow together.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="courses" class="py-20 bg-[#0f0f12]">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl md:text-2xl font-bold text-white mb-4">Trending <span class="text-indigo-400">Courses</span></h2>
                    <p class="text-gray-400">Explore our most popular programs designed to help you succeed.</p>
                </div>
                <a href="course-page.php" class="hidden md:flex items-center text-indigo-400 hover:text-indigo-300 font-medium transition">
                    View All <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
             
                $select_products = mysqli_query($conn, "SELECT p.*, i.name as instructor_name FROM `products` p LEFT JOIN `course_activity` ca ON p.id = ca.course_id LEFT JOIN `instructors` i ON ca.instructor_id = i.id LIMIT 3");
                
                if($select_products && mysqli_num_rows($select_products) > 0){
                    while($row = mysqli_fetch_assoc($select_products)){
                ?>
                <div class="course-card rounded-2xl overflow-hidden group flex flex-col transition-all duration-300">
                    <div class="relative overflow-hidden h-52">
                        <img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" alt="Course" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full border border-white/10">
                            Featured
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-xs font-medium text-indigo-400 bg-indigo-900/30 px-2 py-1 rounded">Course</span>
                            <div class="flex items-center text-yellow-400 text-sm">
                                <i class="fas fa-star mr-1"></i> 4.9
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2 line-clamp-1 group-hover:text-indigo-400 transition-colors"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="text-gray-400 text-sm mb-6 flex-1 line-clamp-2"><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-6 space-x-4">
                            <span class="flex items-center"><i class="fas fa-user mr-2"></i> <?php echo !empty($row['instructor_name']) ? htmlspecialchars($row['instructor_name']) : 'Expert'; ?></span>
                        </div>

                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-white/10">
                            <span class="text-2xl font-black text-white">৳<?php echo htmlspecialchars($row['price']); ?></span>
                            <?php if (isset($_SESSION['user_name'])): ?>
                                <a href="course-page.php?id=<?php echo $row['id']; ?>" class="text-sm font-bold text-indigo-400 hover:text-indigo-300">Enroll Now &rarr;</a>
                            <?php else: ?>
                                <a href="login_form.php" class="text-sm font-bold text-indigo-400 hover:text-indigo-300">Login to Enroll &rarr;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p class='text-gray-500 col-span-full text-center'>No courses available at the moment. Check back soon!</p>";
                }
                ?>
            </div>
            
            <div class="mt-10 text-center md:hidden">
                 <a href="course-page.php" class="inline-block border border-indigo-600 text-indigo-400 hover:bg-indigo-600 hover:text-white font-medium py-3 px-8 rounded-full transition">
                    View All Courses
                </a>
            </div>
        </div>
    </section>

    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-indigo-900 z-0"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 z-0"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-4xl mx-auto text-center bg-black/40 backdrop-blur-md border border-white/10 p-10 md:p-16 rounded-3xl shadow-2xl">
                <h2 class="text-3xl md:text-2xl font-bold text-white mb-6">Ready to start learning?</h2>
                <p class="text-lg text-indigo-200 mb-10 max-w-2xl mx-auto">Join thousands of students from around the world who are advancing their careers with our platform.</p>
                
                <?php if (!isset($_SESSION['user_name']) && !isset($_SESSION['admin_name']) && !isset($_SESSION['instructor_name'])): ?>
                    <a href="register_form.php" class="inline-block bg-white text-indigo-900 font-black text-lg py-4 px-10 rounded-full hover:bg-indigo-50 transition transform hover:-translate-y-1 shadow-lg">
                        Get Started Now
                    </a>
                <?php else: ?>
                    <a href="course-page.php" class="inline-block bg-white text-indigo-900 font-black text-lg py-4 px-10 rounded-full hover:bg-indigo-50 transition transform hover:-translate-y-1 shadow-lg">
                        Browse Courses
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer class="bg-black pt-20 pb-10 border-t border-white/5">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <a href="index.php" class="text-2xl font-bold tracking-wide block mb-6">BOT <span class="text-indigo-600">Learning</span></a>
                    <p class="text-gray-400 mb-6 max-w-sm">Empowering learners worldwide with accessible, high-quality tech education designed for the modern workplace.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6 text-lg">Quick Links</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="about.php" class="hover:text-indigo-400 transition">About Us</a></li>
                        <li><a href="course-page.php" class="hover:text-indigo-400 transition">Courses</a></li>
                        <li><a href="instructors.php" class="hover:text-indigo-400 transition">Instructors</a></li>
                        <li><a href="contact.php" class="hover:text-indigo-400 transition">Contact Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6 text-lg">Legal</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="#" class="hover:text-indigo-400 transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            
             <div class="border-t border-slate-800 py-6 text-center text-sm text-gray-500">
                <p>&copy; <?php echo date("Y");?> BOT Learning. All Rights Reserved.</p>
            
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition"><i class="fab fa-facebook text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-youtube text-xl"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-black/80');
                nav.classList.remove('glass-nav');
            } else {
                nav.classList.add('glass-nav');
                nav.classList.remove('bg-black/80');
            }
        });
    </script>
</body>
</html>
