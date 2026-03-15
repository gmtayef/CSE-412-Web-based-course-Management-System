<?php
@include 'config.php';

$error = [];

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
    $user_type = $_POST['user_type'];

    $select = " SELECT * FROM user_form WHERE email = '$email' ";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        if ($pass != $cpass) {
            $error[] = 'Passwords do not match!';
        } else {
            // Securely hash the password before saving
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            
            $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$hashed_password','$user_type')";
            mysqli_query($conn, $insert);
            header('location:login_form.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Create an Account</h1>
            <p class="text-gray-500 mt-2">Join BOT Learning today </p>
        </div>

        <?php
        if (!empty($error)) {
            foreach ($error as $err) {
                echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">' . $err . '</div>';
            }
        }
        ?>

        <form action="" method="post" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" required placeholder="john@example.com" class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full px-4 py-2 mt-2 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 mt-2 cursor-pointer text-gray-400 hover:text-gray-600" onclick="togglePassword('password', 'eyeIcon1')">
                        <i class="fas fa-eye" id="eyeIcon1"></i>
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="relative">
                    <input type="password" name="cpassword" id="cpassword" required placeholder="••••••••" class="w-full px-4 py-2 mt-2 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 mt-2 cursor-pointer text-gray-400 hover:text-gray-600" onclick="togglePassword('cpassword', 'eyeIcon2')">
                        <i class="fas fa-eye" id="eyeIcon2"></i>
                    </span>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Account Type</label>
                <select name="user_type" class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="user">Student</option>
                </select>
            </div>
            
            <div>
                <button type="submit" name="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transition duration-200">Sign Up</button>
            </div>
        </form>
        
        <p class="text-sm text-center text-gray-500 mt-6">
            Already have an account? <a href="login_form.php" class="font-medium text-indigo-600 hover:underline">Log in</a>
        </p>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); // Changes icon to an eye with a slash
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye"); // Changes icon back to normal eye
            }
        }
    </script>
</body>
</html>