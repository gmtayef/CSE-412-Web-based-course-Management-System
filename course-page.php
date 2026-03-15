<?php
@include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 0;
$message = [];

if (isset($_POST['add_to_cart'])) {
    if($user_id == 0){
        header('location:login_form.php');
        exit();
    }
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

$search_key = $_GET['search'] ?? '';
$select_products_query = "SELECT * FROM `products`";
if (!empty($search_key)) {
    $select_products_query .= " WHERE name LIKE '%" . mysqli_real_escape_string($conn, $search_key) . "%'";
}
$select_products = mysqli_query($conn, $select_products_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Courses - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #0a0a0a; } 
        
        /* Glass Navigation Effect */
        .glass-nav {
           background: rgba(255, 255, 255, 0.1);
           backdrop-filter: blur(10px);
           -webkit-backdrop-filter: blur(10px);
           border: 1px solid rgba(255, 255, 255, 0.1);
           box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        /* Course Cards */
        .course-card {
            background: linear-gradient(145deg, #111111, #1a1a1a);
            border: 1px solid #2a2a2a;
        }
        
        .course-card:hover {
            border-color: #4f46e5;
            box-shadow: 0 10px 30px -10px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body class="text-white">

    <div class="sticky top-4 z-50 container mx-auto px-4">
        <nav class=" py-4 flex justify-between items-center glass-nav rounded-full px-8">
            <a href="index.php" class="text-1xl font-bold tracking-wide">BOT <span class="text-indigo-400">Learning</span></a>
            
            <ul class="hidden md:flex items-center space-x-8 text-md font-medium text-gray-300">
                <li><a href="index.php" class="hover:text-white pb-1 border-b-2 border-transparent hover:border-indigo-500 transition-all duration-300">Home</a></li>
                
                <li><a href="course-page.php" class="text-white font-bold border-b-2 border-indigo-500 pb-1 transition-all duration-300">Courses</a></li>
                
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
    </div>

    <header class="relative bg-cover bg-center h-[50vh] mt-[-88px]" style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80');">
        <div class="absolute inset-0 bg-black/70"></div>
        <div class="relative z-10 container mx-auto px-4 h-full flex flex-col justify-center items-center text-center text-white pt-20">
            <h1 class="text-5xl md:text-2xl font-black mb-4">Explore Our <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Courses</span></h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">Find the perfect course to boost your skills and accelerate your career.</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-16">
        
        <?php if (!empty($message)): ?>
            <?php foreach($message as $msg): ?>
                <div class="mb-8 max-w-2xl mx-auto p-4 rounded-lg bg-indigo-500/10 border border-indigo-500/50 text-indigo-400 text-center font-medium shadow-lg">
                    <?php echo $msg; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php
            if (mysqli_num_rows($select_products) > 0) {
                while ($fetch_product = mysqli_fetch_assoc($select_products)) {
            ?>
            <div class="course-card rounded-2xl overflow-hidden flex flex-col transition-all duration-300 group">
                <div class="relative overflow-hidden h-56">
                    <img class="h-full w-full object-cover transform group-hover:scale-110 transition-transform duration-500" src="uploaded_img/<?php echo htmlspecialchars($fetch_product['image']); ?>" alt="<?php echo htmlspecialchars($fetch_product['name']); ?>">
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold mb-3 text-white group-hover:text-indigo-400 transition-colors line-clamp-1"><?php echo htmlspecialchars($fetch_product['name']); ?></h3>
                    
                    <div class="text-sm text-gray-400 mb-4 space-y-2">
                        <p class="flex items-center"><i class="fas fa-calendar-alt w-5 text-indigo-500"></i> <strong>Starts:</strong> &nbsp;<?php echo date('M d, Y', strtotime($fetch_product['start_time'])); ?></p>
                        <p class="flex items-center"><i class="fas fa-flag-checkered w-5 text-purple-500"></i> <strong>Ends:</strong> &nbsp;<?php echo date('M d, Y', strtotime($fetch_product['end_time'])); ?></p>
                    </div>
                    
                    <p class="text-gray-400 text-sm mb-6 flex-grow line-clamp-3"><?php echo htmlspecialchars($fetch_product['description']); ?></p>
                    
                    <div class="flex justify-between items-center mt-auto border-t border-white/10 pt-5">
                        <span class="text-2xl font-black text-white">৳<?php echo htmlspecialchars($fetch_product['price']); ?></span>
                        <form action="" method="post" class="m-0">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($fetch_product['name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($fetch_product['price']); ?>">
                            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($fetch_product['image']); ?>">
                            <button type="submit" name="add_to_cart" class="bg-indigo-600 text-white font-bold py-2 px-5 rounded-lg hover:bg-indigo-700 transition transform hover:-translate-y-1 shadow-[0_0_15px_rgba(79,70,229,0.3)]">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<div class='col-span-full py-20 text-center flex flex-col items-center justify-center'>";
                echo "<i class='fas fa-book-open text-6xl text-gray-700 mb-4'></i>";
                echo "<p class='text-gray-400 text-xl font-medium'>No courses found matching your criteria.</p>";
                echo "</div>";
            }
            ?>
        </div>
    </main>

    <footer class="bg-black pt-16 pb-8 border-t border-white/5 mt-12">
        <div class="container mx-auto px-6 text-center text-sm text-gray-500">
            <p>&copy; <?php echo date("Y"); ?> BOT Learning. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>