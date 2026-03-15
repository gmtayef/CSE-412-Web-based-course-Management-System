<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:login_form.php');
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;
$message = [];

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = 1;

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'");

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'Course is already in your cart';
    } else {
        $insert_product = mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')");
        if ($insert_product) {
            $message[] = 'Course added to cart successfully!';
        } else {
            $message[] = 'Could not add the course to your cart.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>M & S Learning - Your Gateway to Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .back-video {
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: -1;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .header-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <?php
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<div id="toast-message" class="fixed top-5 right-5 bg-indigo-600 text-white py-3 px-5 rounded-lg shadow-lg z-50 flex items-center justify-between">';
            echo '<span>' . htmlspecialchars($msg) . '</span>';
            echo '<button onclick="document.getElementById(\'toast-message\').style.display=\'none\'" class="ml-4 text-xl font-bold">&times;</button>';
            echo '</div>';
        }
    }
    ?>

    <header class="relative text-white h-screen">
        <video autoplay loop muted class="back-video">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-video-of-a-man-with-a-vr-headset-4290-large.mp4" type="video/mp4" />
        </video>
        <div class="header-overlay"></div>
        <div class="relative z-10 container mx-auto px-4 h-full flex flex-col">
            <nav class="py-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold">M & S <span class="text-indigo-400">Learning</span></h1>
                <ul class="hidden md:flex items-center space-x-8 text-lg">
                    <li><a href="home.php" class="hover:text-indigo-300 transition">Home</a></li>
                    <li><a href="course-page.php" class="hover:text-indigo-300 transition">Courses</a></li>
                    <li><a href="about.php" class="hover:text-indigo-300 transition">About</a></li>
                    <li><a href="contact.php" class="hover:text-indigo-300 transition">Contact</a></li>
                </ul>
                <div class="flex items-center space-x-4">
                    <span class="hidden sm:block">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="logout.php" class="bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg transition">Logout</a>
                </div>
            </nav>

            <div class="flex-grow flex flex-col justify-center items-center text-center">
                <h2 class="text-5xl md:text-7xl font-black tracking-tight leading-tight mb-4">Elevate Your Skills.</h2>
                <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mb-8">Join thousands of learners and take your career to the next level with our industry-leading courses.</p>
                <a href="course-page.php" class="bg-white text-gray-900 font-bold py-4 px-8 rounded-lg text-lg hover:bg-gray-200 transition-transform transform hover:scale-105">Browse All Courses</a>
            </div>
        </div>
    </header>

    <main>
        <section id="why-us" class="py-20 bg-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-bold mb-4">Why M & S Learning?</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-12">We provide a platform for you to learn, grow, and succeed.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="feature-card">
                        <div class="bg-indigo-100 text-indigo-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Expert Instructors</h3>
                        <p class="text-gray-600">Learn from the best. Our instructors are industry experts with years of practical experience.</p>
                    </div>
                    <div class="feature-card">
                        <div class="bg-indigo-100 text-indigo-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-laptop-code text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Flexible Learning</h3>
                        <p class="text-gray-600">Learn at your own pace, on your own schedule. Access courses anytime, anywhere.</p>
                    </div>
                    <div class="feature-card">
                        <div class="bg-indigo-100 text-indigo-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-medal text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Career Growth</h3>
                        <p class="text-gray-600">Gain in-demand skills that are recognized by top companies around the world.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="featured-courses" class="py-20">
            <div class="container mx-auto px-4">
                <h2 class="text-4xl font-bold text-center mb-12">Featured Courses</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 3");
                    if (mysqli_num_rows($select_products) > 0) {
                        while ($fetch_product = mysqli_fetch_assoc($select_products)) {
                    ?>
                    <form action="" method="post">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                            <img class="h-56 w-full object-cover" src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="<?php echo htmlspecialchars($fetch_product['name']); ?>">
                            <div class="p-6">
                                <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($fetch_product['name']); ?></h3>
                                <p class="text-gray-600 mb-4 h-24 overflow-hidden"><?php echo htmlspecialchars($fetch_product['description']); ?></p>
                                <div class="flex justify-between items-center">
                                    <span class="text-3xl font-bold text-indigo-600">$<?php echo $fetch_product['price']; ?></span>
                                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($fetch_product['name']); ?>">
                                    <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                                    <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
                                    <button type="submit" name="add_to_cart" class="bg-indigo-500 text-white py-2 px-4 rounded-lg hover:bg-indigo-600 transition">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                        }
                    } else {
                        echo "<p class='col-span-3 text-center text-gray-500'>No featured courses available at the moment.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold mb-4">M & S <span class="text-indigo-400">Learning</span></h3>
                    <p class="text-gray-400">Empowering individuals through accessible, high-quality online education.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="about.php" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-400 hover:text-white">Contact</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Connect With Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white text-2xl"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-2xl"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white text-2xl"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-gray-500">
                <p>&copy; <?php echo date("Y"); ?> M & S Learning. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
