<?php
@include 'config.php';
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

$message_sent = false;


if(isset($_POST['send'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = $_POST['number'];
    $msg = mysqli_real_escape_string($conn, $_POST['message']);

   
    if(isset($conn)){
        $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'contact'");
        if(mysqli_num_rows($check_table) > 0) {
            $insert_message = mysqli_query($conn, "INSERT INTO `contact`(name, email, number, message) VALUES('$name', '$email', '$number', '$msg')");
            if($insert_message){
                $message_sent = true;
            }
        } else {
             
             $error_msg = "Table 'contact' missing in database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - BOT Learning</title>
    
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

     
        .input-field {
            background-color: #1f2937;
            border: 1px solid #374151;
            color: white;
            transition: all 0.3s;
        }
        .input-field:focus {
            border-color: #6366f1; /* Indigo-500 */
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
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
                    <li><a href="about.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">About</a></li>
                    <li><a href="contact.php" class="text-white font-bold border-b-2 border-indigo-500 pb-1 transition-all duration-300">Contact</a></li>
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

    <header class="pt-40 pb-12 px-6 text-center relative z-10">
        <h1 class="text-4xl md:text-3xl font-black mb-4">Get in <span class="text-indigo-500">Touch</span></h1>
        <p class="text-gray-400 text-lg max-w-xl mx-auto">
            Have questions about our courses or need support? We are here to help you unlock your potential.
        </p>
    </header>

    <section class="container mx-auto px-6 pb-20 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-indigo-900/50 rounded-full flex items-center justify-center text-indigo-400 text-xl">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-200">Address</h4>
                            <p class="text-gray-400 text-sm">Aftabnagor, Dhaka</p>
                        </div>
                    </div>
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 flex items-center space-x-4">
                        <div class="w-12 h-12 bg-indigo-900/50 rounded-full flex items-center justify-center text-indigo-400 text-xl">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-200">Phone</h4>
                            <p class="text-gray-400 text-sm">+880 1700 000000</p>
                        </div>
                    </div>
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 flex items-center space-x-4 sm:col-span-2">
                        <div class="w-12 h-12 bg-indigo-900/50 rounded-full flex items-center justify-center text-indigo-400 text-xl">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-200">Email</h4>
                            <p class="text-gray-400 text-sm">support@botlearning.com</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl overflow-hidden shadow-2xl border border-gray-800 h-80">
                    <iframe 
                        class="w-full h-full"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.8466155695024!2d90.41968951543153!3d23.753331894599976!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8783ab9882f%3A0x5e4b3c7b3e2b2b2b!2sAftabnagar%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1625567890123!5m2!1sen!2sbd" 
                        allowfullscreen="" 
                        loading="lazy" 
                        style="filter: grayscale(100%) invert(92%) contrast(83%);">
                    </iframe>
                </div>
            </div>

            <div class="bg-gray-900 p-8 md:p-10 rounded-2xl border border-gray-800 shadow-xl">
                <h3 class="text-2xl font-bold mb-6">Send us a Message</h3>
                
                <?php if($message_sent): ?>
                    <div class="bg-green-900/30 border border-green-500 text-green-300 px-4 py-3 rounded mb-6 text-center">
                        <i class="fas fa-check-circle mr-2"></i> Message sent successfully!
                    </div>
                <?php elseif(isset($error_msg)): ?>
                     <div class="bg-yellow-900/30 border border-yellow-500 text-yellow-300 px-4 py-3 rounded mb-6 text-center text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Note: Database not ready, but your form is working visually.
                    </div>
                <?php endif; ?>

                <form action="" method="post" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Your Name</label>
                        <input type="text" name="name" required placeholder="John Doe" class="w-full px-4 py-3 rounded-lg input-field">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
                            <input type="email" name="email" required placeholder="john@example.com" class="w-full px-4 py-3 rounded-lg input-field">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Phone Number</label>
                            <input type="number" name="number" placeholder="017..." class="w-full px-4 py-3 rounded-lg input-field">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Message</label>
                        <textarea name="message" required placeholder="How can we help you?" rows="5" class="w-full px-4 py-3 rounded-lg input-field resize-none"></textarea>
                    </div>
                    <button type="submit" name="send" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-lg transition duration-300 shadow-lg hover:shadow-indigo-500/30">
                        Send Message <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </form>
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
                <p>© <?php echo date("Y"); ?> BOT Learning. All Rights Reserved.</p>
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
