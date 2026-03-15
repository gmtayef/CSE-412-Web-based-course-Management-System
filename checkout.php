<?php
include 'config.php';
session_start();


$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? 0;

if (!isset($_SESSION['user_name']) || empty($user_id)) {
  header('location: login_form.php');
  exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment']);
    
    
    $trx_id = isset($_POST['trxid']) ? mysqli_real_escape_string($conn, $_POST['trxid']) : '';
    $card_num = isset($_POST['card_num']) ? mysqli_real_escape_string($conn, $_POST['card_num']) : '';
    
    
    $payment_details = $payment_method;
    if ($payment_method == 'bKash') {
        $payment_details .= " (TrxID: $trx_id)";
    } elseif ($payment_method == 'Card') {
        $payment_details .= " (Card No: $card_num)";
    }

    $total_query = "SELECT SUM(products.price) as total 
                    FROM cart 
                    JOIN products ON cart.course_id = products.id 
                    WHERE cart.user_id = '$user_id'";
    $total_result = mysqli_query($conn, $total_query);
    $total_data = mysqli_fetch_assoc($total_result);
    $total_amount = $total_data['total'];

   
    $insert_payment = "INSERT INTO payments 
        (user_id, name, email, phone, address, payment_method, total_amount) 
        VALUES 
        ('$user_id', '$name', '$email', '$phone', '$address', '$payment_details', '$total_amount')";
    
    
    $payment_success = mysqli_query($conn, $insert_payment);

    if ($payment_success) {
       
        $result = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
        while ($row = mysqli_fetch_assoc($result)) {
            $course_id = $row['course_id'];
            
            $check = mysqli_query($conn, "SELECT * FROM enrollments WHERE user_id = '$user_id' AND course_id = '$course_id'");
            if(mysqli_num_rows($check) == 0) {
                mysqli_query($conn, "INSERT INTO enrollments (user_id, course_id) VALUES ('$user_id', '$course_id')");
            }
        }

        
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

        
        header("Location: student_panel.php?checkout=success");
        exit();
    } else {
        die("Payment failed to process: " . mysqli_error($conn));
    }
}


$cart_query = "SELECT cart.*, products.name, products.image, products.price 
               FROM cart 
               JOIN products ON cart.course_id = products.id 
               WHERE cart.user_id = '$user_id'";
$cart_result = mysqli_query($conn, $cart_query);

$total = 0;
$has_items = mysqli_num_rows($cart_result) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - BOT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        /* Custom styles to hide default radio circles for custom styling */
        .payment-radio:checked + div {
            border-color: #6366f1;
            background-color: rgba(99, 102, 241, 0.1);
        }
        .bkash-radio:checked + div {
            border-color: #e2136e;
            background-color: rgba(226, 19, 110, 0.1);
        }
    </style>
    <script>
        function updatePaymentUI() {
           
            document.getElementById('bkash-details').classList.add('hidden');
            document.getElementById('card-details').classList.add('hidden');

            
            const selectedMethod = document.querySelector('input[name="payment"]:checked').value;

           
            if (selectedMethod === 'bKash') {
                document.getElementById('bkash-details').classList.remove('hidden');
            } else if (selectedMethod === 'Card') {
                document.getElementById('card-details').classList.remove('hidden');
            }
        }

       
        function toggleCVC() {
            const cvcInput = document.getElementById('cvc_input');
            const eyeIcon = document.getElementById('eye_icon');
            
            if (cvcInput.type === 'password') {
                cvcInput.type = 'text';
                
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                cvcInput.type = 'password';
            
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</head>
<body class="bg-[#0f172a] text-slate-300 min-h-screen flex flex-col">

    <nav class="bg-[#1e293b] border-b border-slate-800 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold tracking-tight text-white">BOT <span class="text-[#6366f1]">Checkout</span></h2>
                </div>
                <div>
                    <a href="cart.php" class="text-sm font-medium text-slate-400 hover:text-white transition-colors border border-slate-700 px-4 py-2 rounded-lg hover:bg-slate-800">
                        &larr; Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <?php if ($has_items): ?>
        <form action="checkout.php" method="post" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-8">
                
                <div class="bg-[#1e293b] rounded-2xl shadow-xl border border-slate-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#6366f1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Billing Information
                        </h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-400">Full Name</label>
                            <input type="text" name="name" required class="w-full bg-[#0f172a] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1] transition-colors" placeholder="John Doe">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-400">Email Address</label>
                            <input type="email" name="email" required class="w-full bg-[#0f172a] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1] transition-colors" placeholder="john@example.com">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-400">Phone Number</label>
                            <input type="text" name="phone" required class="w-full bg-[#0f172a] border border-slate-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1] transition-colors" placeholder="+880 1XXX-XXXXXX">
                        </div>
                        
                    </div>
                </div>

                <div class="bg-[#1e293b] rounded-2xl shadow-xl border border-slate-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#6366f1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            Payment Method
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment" value="bKash" class="bkash-radio sr-only" onchange="updatePaymentUI()">
                                <div class="border-2 border-slate-700 rounded-xl p-4 text-center hover:border-[#e2136e] transition-colors">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-[#e2136e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <span class="block font-semibold text-white">bKash Mobile</span>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment" value="Card" class="payment-radio sr-only" onchange="updatePaymentUI()">
                                <div class="border-2 border-slate-700 rounded-xl p-4 text-center hover:border-[#6366f1] transition-colors">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-[#6366f1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <span class="block font-semibold text-white">Credit/Debit Card</span>
                                </div>
                            </label>
                        </div>

                        <div id="bkash-details" class="hidden bg-gradient-to-r from-[#e2136e]/20 to-[#e2136e]/5 border border-[#e2136e]/30 rounded-xl p-5 mb-4 transition-all">
                            <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                                <span class="bg-[#e2136e] p-1 rounded"><svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></span>
                                Send Money to Merchant
                            </h4>
                            <p class="text-sm text-slate-300 mb-4">Please "Send Money" or "Make Payment" to our Merchant Number: <strong class="text-white text-lg ml-2 tracking-wider">017XX-XXXXXX</strong></p>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-pink-200">bKash Transaction ID (TrxID)</label>
                                <input type="text" name="trxid" placeholder="e.g. 8N4GD56X" class="w-full bg-[#0f172a]/50 border border-[#e2136e]/40 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#e2136e] focus:ring-1 focus:ring-[#e2136e]">
                            </div>
                        </div>

                        <div id="card-details" class="hidden bg-[#0f172a] border border-slate-700 rounded-xl p-5 mb-4 transition-all">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-white">Card Information</h4>
                                <div class="flex gap-2">
                                    <div class="w-8 h-5 bg-slate-700 rounded-sm"></div>
                                    <div class="w-8 h-5 bg-slate-700 rounded-sm"></div>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-slate-400">Card Number</label>
                                    <input type="text" name="card_num" placeholder="0000 0000 0000 0000" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg px-4 py-3 text-white mt-1 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-slate-400">Expiry Date</label>
                                        <input type="text" name="card_exp" placeholder="MM/YY" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg px-4 py-3 text-white mt-1 focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]">
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-slate-400">CVC</label>
                                        <div class="relative mt-1">
                                            <input type="password" id="cvc_input" name="card_cvc" placeholder="123" maxlength="3" class="w-full bg-[#1e293b] border border-slate-700 rounded-lg px-4 py-3 pr-10 text-white focus:outline-none focus:border-[#6366f1] focus:ring-1 focus:ring-[#6366f1]">
                                            <button type="button" onclick="toggleCVC()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white transition-colors">
                                                <svg id="eye_icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-[#1e293b] rounded-2xl shadow-xl border border-slate-800 p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-white mb-6 border-b border-slate-700 pb-4">Order Summary</h2>
                    
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 mb-6 custom-scrollbar">
                        <?php while ($row = mysqli_fetch_assoc($cart_result)): ?>
                        <div class="flex items-center gap-4 bg-[#0f172a] p-3 rounded-lg border border-slate-800">
                            <img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" class="w-16 h-12 object-cover rounded-md" alt="Course">
                            <div class="flex-grow">
                                <h4 class="text-sm font-bold text-white line-clamp-1"><?php echo htmlspecialchars($row['name']); ?></h4>
                                <span class="text-xs text-slate-400">Lifetime access</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-[#818cf8]">৳<?php echo number_format($row['price'], 2); ?></span>
                            </div>
                        </div>
                        <?php $total += $row['price']; ?>
                        <?php endwhile; ?>
                    </div>

                    <div class="border-t border-slate-700 pt-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-slate-300">Subtotal</span>
                            <span class="text-white">৳<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-slate-300">Platform Fee</span>
                            <span class="text-green-400 text-sm">Free</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-slate-700">
                            <span class="text-lg font-bold text-white">Total</span>
                            <span class="text-3xl font-black text-[#818cf8]">৳<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>

                    <button type="submit" name="pay_now" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] text-white font-bold py-4 px-4 rounded-xl transition-all shadow-[0_0_15px_rgba(99,102,241,0.3)] hover:shadow-[0_0_25px_rgba(99,102,241,0.5)] flex justify-center items-center gap-2 transform hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Confirm Payment & Enroll
                    </button>
                    
                    <p class="text-center text-xs text-slate-500 mt-4 flex justify-center items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Secure 256-bit SSL Encryption
                    </p>
                </div>
            </div>

        </form>
        <?php else: ?>
            <div class="bg-[#1e293b] rounded-2xl p-16 flex flex-col items-center justify-center text-center shadow-lg border border-slate-800 max-w-2xl mx-auto mt-10">
                <svg class="w-16 h-16 text-slate-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h2 class="text-2xl font-bold text-white mb-2">Checkout Unavailable</h2>
                <p class="text-slate-400 mb-8">Your cart is empty. Please add some courses to your cart before proceeding to checkout.</p>
                <a href="explore_courses.php" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                    Browse Courses
                </a>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>