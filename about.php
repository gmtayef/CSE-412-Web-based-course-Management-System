<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BOT Learning</title>
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
        
        .text-gradient {
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="text-white relative">

    <div class="absolute top-0 left-0 w-full z-50">
        <div class="container mx-auto px-4">
            <nav class="py-4 flex justify-between items-center glass-nav rounded-full mt-6 px-8 mx-2 md:mx-6 transition-colors duration-300">
                <a href="index.php" class="text-1xl font-bold tracking-wide">BOT <span class="text-indigo-400">Learning</span></a>
                
                <ul class="hidden md:flex items-center space-x-8 text-md font-medium text-gray-300">
                    <li><a href="index.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Home</a></li>
                    <li><a href="course-page.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Courses</a></li>
                    <li><a href="instructors.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Instructors</a></li>
                    <li><a href="about.php" class="text-white font-bold border-b-2 border-indigo-500 pb-1 transition-all duration-300">About</a></li>
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
        </div>
    </div>

    <header class="relative pt-40 pb-20 px-6 text-center">
        <h1 class="text-5xl md:text-3xl font-black mb-6">Who <span class="text-gradient">We Are</span></h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">
            We are dedicated to democratizing education. BOT Learning connects students with industry experts to bridge the gap between theory and practice.
        </p>
    </header>

    <section class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="rounded-2xl overflow-hidden shadow-2xl border border-gray-800 relative group">
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center group-hover:bg-black/30 transition">
                    <i class="fas fa-play-circle text-6xl text-indigo-500 opacity-80"></i>
                </div>
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="About Video" class="w-full h-full object-cover">
            </div>

            <div>
                <h2 class="text-3xl font-bold mb-4">Unlock Your Potential with <br><span class="text-indigo-400">Expert Solutions</span></h2>
                <p class="text-gray-400 mb-8 leading-relaxed">
                    The ultimate planning solution for busy learners who want to reach their personal goals. Effortless, comfortable, and unique details designed for you.
                </p>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-400"><i class="fas fa-video"></i></div>
                        <p class="text-gray-300">High Quality Video Content</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-400"><i class="fas fa-users"></i></div>
                        <p class="text-gray-300">Powerful Community</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-900/50 flex items-center justify-center text-indigo-400"><i class="fas fa-globe"></i></div>
                        <p class="text-gray-300">Premium Content Worldwide</p>
                    </div>
                </div>
                
                <a href="course-page.php" class="inline-block mt-8 bg-white text-black font-bold py-3 px-8 rounded-full hover:bg-gray-200 transition">
                    Explore Courses
                </a>
            </div>
        </div>
    </section>

    <section class="bg-gray-900 py-16 mt-12 border-y border-gray-800">
        <div class="container mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <h3 class="text-4xl font-bold text-white mb-2">50+</h3>
                <p class="text-indigo-400 text-sm uppercase tracking-wider">Instructors</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-white mb-2">120+</h3>
                <p class="text-indigo-400 text-sm uppercase tracking-wider">Total Courses</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-white mb-2">5k+</h3>
                <p class="text-indigo-400 text-sm uppercase tracking-wider">Enrollments</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-white mb-2">99%</h3>
                <p class="text-indigo-400 text-sm uppercase tracking-wider">Satisfaction</p>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <h3 class="text-indigo-500 font-bold mb-2 uppercase text-sm tracking-widest">Our Team</h3>
            <h2 class="text-4xl font-bold">Meet the Professionals</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition duration-300 group">
                <div class="h-64 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Manager" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold mb-1">David Miller</h3>
                    <p class="text-gray-500 text-sm mb-4">Project Manager</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition duration-300 group">
                <div class="h-64 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Developer" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold mb-1">Sarah Connor</h3>
                    <p class="text-gray-500 text-sm mb-4">Lead Developer</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition duration-300 group">
                <div class="h-64 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Designer" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold mb-1">Rakib Hassan</h3>
                    <p class="text-gray-500 text-sm mb-4">UI/UX Designer</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-dribbble"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-b from-black to-gray-900">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-12">Success Stories</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-800 p-8 rounded-lg shadow-lg relative">
                    <i class="fas fa-quote-left text-indigo-500 text-3xl absolute top-4 left-4 opacity-30"></i>
                    <p class="text-gray-300 italic mb-6">"I had an amazing learning experience on this website. The courses are well-structured and the instructors are knowledgeable."</p>
                    <p class="font-bold text-white">- John Doe</p>
                </div>
                <div class="bg-gray-800 p-8 rounded-lg shadow-lg relative">
                    <i class="fas fa-quote-left text-indigo-500 text-3xl absolute top-4 left-4 opacity-30"></i>
                    <p class="text-gray-300 italic mb-6">"The variety of courses available here is impressive. It's been a great platform for enhancing my skills."</p>
                    <p class="font-bold text-white">- Jane Smith</p>
                </div>
                <div class="bg-gray-800 p-8 rounded-lg shadow-lg relative">
                    <i class="fas fa-quote-left text-indigo-500 text-3xl absolute top-4 left-4 opacity-30"></i>
                    <p class="text-gray-300 italic mb-6">"I'm grateful for the interactive lessons and supportive community. This website has truly enriched my learning journey."</p>
                    <p class="font-bold text-white">- Michael Johnson</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 border-t border-gray-800">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">BOT Learning</h3>
                    <p class="text-sm">The future of education is here. Join us and unlock your potential.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="about.php" class="hover:text-indigo-400 transition">About Us</a></li>
                        <li><a href="contact.php" class="hover:text-indigo-400 transition">Contact</a></li>
                        <li><a href="course-page.php" class="hover:text-indigo-400 transition">All Courses</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Contact Info</h4>
                    <p class="text-sm mb-2"><i class="fas fa-map-marker-alt mr-2"></i> Aftabnagor, Dhaka</p>
                    <p class="text-sm"><i class="fas fa-phone mr-2"></i> +8801700000000</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Connect With Us</h4>
                    <div class="flex space-x-4 text-xl">
                        <a href="#" class="hover:text-white transition"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-white transition"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-sm">
                <p>&copy; <?php echo date("Y"); ?> BOT Learning. All Rights Reserved.</p>
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
