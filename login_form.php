<?php
@include 'config.php';
session_start();

$error = []; // Initialize error array

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = $_POST['password']; 

   // 1. Search for the user by email only
   $select = "SELECT * FROM user_form WHERE email = '$email'";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_array($result);

      // 2. Use password_verify to check the hash against the submitted password
      if(password_verify($pass, $row['password'])){

         // Password is correct, set sessions based on user type
         if($row['user_type'] == 'admin'){
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_id'] = $row['id'];
            header('location:admin_panel.php');
         }elseif($row['user_type'] == 'instructor'){
            $_SESSION['instructor_name'] = $row['name'];
            $_SESSION['instructor_email'] = $row['email'];
            $_SESSION['instructor_id'] = $row['id'];
            header('location:instructor_panel.php');
         }elseif($row['user_type'] == 'user'){
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_id'] = $row['id'];
            header('location:index.php');
         }
         exit();
      } else {
         $error[] = 'Incorrect email or password!';
      }
   } else {
      $error[] = 'Incorrect email or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login - BOT Learning</title>
   <script src="https://cdn.tailwindcss.com"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
      body { font-family: 'Inter', sans-serif; }
   </style>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen p-4">

   <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-md">
      
      <form action="" method="post" class="p-10">
         <h3 class="text-2xl font-bold text-center text-gray-800 mb-2 uppercase">Login Now</h3>
         <p class="text-center text-gray-500 mb-8">Enter your credentials to access your account.</p>

         <?php
         if(!empty($error)){
            foreach($error as $err){
               echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 text-center text-sm">'.$err.'</div>';
            }
         }
         ?>

         <div class="mb-5">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
            <input type="email" name="email" required placeholder="you@example.com" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-gray-50">
         </div>

         <div class="mb-8">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
            <input type="password" name="password" required placeholder="••••••••" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-gray-50">
         </div>

         <button type="submit" name="submit" 
                 class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 transform active:scale-[0.98] transition-all duration-200 shadow-lg">
            Sign In
         </button>

         <p class="mt-8 text-center text-gray-600">
            Don't have an account? <a href="register_form.php" class="text-indigo-600 hover:text-indigo-800 font-bold underline underline-offset-4">Register now</a>
         </p>
      </form>

   </div>

</body>
</html>