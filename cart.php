<?php
include 'config.php';
session_start();


$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? 0;

if (!isset($_SESSION['user_name']) || empty($user_id)) {
  header('location: login_form.php');
  exit();
}

$query = "SELECT cart.*, products.name, products.image, products.price 
          FROM cart 
          JOIN products ON cart.course_id = products.id 
          WHERE cart.user_id = '$user_id'";

$result = mysqli_query($conn, $query);
$total = 0;
$item_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BOT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#0f172a] text-slate-300 min-h-screen flex flex-col">

    <nav class="bg-[#1e293b] border-b border-slate-800 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold tracking-tight text-white">BOT <span class="text-[#6366f1]">Learning</span></h2>
                </div>
                <div>
                    <a href="student_panel.php" class="text-sm font-medium text-slate-400 hover:text-white transition-colors border border-slate-700 px-4 py-2 rounded-lg hover:bg-slate-800">
                        &larr; Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-white">Shopping Cart</h1>
            <p class="text-slate-400 mt-1">You have <?php echo $item_count; ?> course(s) in your cart.</p>
        </header>

        <?php if ($item_count > 0): ?>
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3 space-y-4">
                    <?php while ($row = mysqli_fetch_assoc($result)): $total += $row['price']; ?>
                    <div class="bg-[#1e293b] rounded-xl p-4 flex flex-col sm:flex-row items-center gap-6 border border-slate-800 shadow-sm hover:border-slate-700 transition-colors">
                        <div class="h-24 w-full sm:w-32 flex-shrink-0 rounded-lg overflow-hidden bg-slate-800">
                            <img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" alt="Course Image" class="h-full w-full object-cover">
                        </div>
                        <div class="flex-grow text-center sm:text-left">
                            <h3 class="text-lg font-semibold text-white"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="text-sm text-slate-400 mt-1">Full lifetime access included.</p>
                        </div>
                        <div class="flex flex-col items-center sm:items-end gap-3 min-w-[100px]">
                            <span class="text-xl font-bold text-[#818cf8]">৳<?php echo number_format($row['price'], 2); ?></span>
                            <a href="remove-from-cart.php?id=<?php echo $row['course_id']; ?>" class="text-sm flex items-center gap-1 text-red-400 hover:text-red-300 transition-colors">
                                Remove
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-[#1e293b] rounded-xl p-6 border border-slate-800 shadow-lg sticky top-24">
                        <h2 class="text-xl font-bold text-white mb-6 border-b border-slate-700 pb-4">Order Summary</h2>
                        <div class="border-t border-slate-700 pt-4 mb-6">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-base font-semibold text-white">Total</span>
                                <span class="text-2xl font-bold text-white">৳<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                        <a href="checkout.php" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-bold py-3 px-4 rounded-lg transition-colors shadow-md flex justify-center items-center gap-2">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-[#1e293b] rounded-2xl p-16 flex flex-col items-center justify-center text-center shadow-lg border border-slate-800 max-w-3xl mx-auto mt-8">
                <h2 class="text-2xl font-bold text-white mb-2">Your cart is feeling lonely.</h2>
                <a href="explore_courses.php" class="mt-4 bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 px-8 rounded-lg transition-colors shadow-md">
                    Explore Courses
                </a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>