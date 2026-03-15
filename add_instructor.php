<?php
@include 'config.php';
session_start();


if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = []; 


if(isset($_POST['add_instructor'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

 
   $select_inst = mysqli_query($conn, "SELECT * FROM instructors WHERE email = '$email'");
   $select_user = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'");

   if(mysqli_num_rows($select_inst) > 0 || mysqli_num_rows($select_user) > 0){
      $message[] = 'A user or instructor with this email already exists!';
   } else {

      $insert = "INSERT INTO instructors(name, email, password, image) VALUES('$name', '$email', '$pass', '$image')";
      $upload = mysqli_query($conn, $insert);
      
      if($upload){
        
         mysqli_query($conn, "INSERT INTO user_form(name, email, password, user_type) VALUES('$name', '$email', '$pass', 'instructor')");
         
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'New instructor added successfully!';
      } else {
         $message[] = 'Could not add the instructor.';
      }
   }
}


if(isset($_POST['update_instructor'])){
   $update_id = mysqli_real_escape_string($conn, $_POST['update_id']);
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);

 
   $get_old_email = mysqli_query($conn, "SELECT email FROM instructors WHERE id = '$update_id'");
   $old_email_data = mysqli_fetch_assoc($get_old_email);
   $old_email = $old_email_data['email'];

  
   $duplicate = false;
   if ($email !== $old_email) {
      $check_email = mysqli_query($conn, "SELECT email FROM user_form WHERE email = '$email'");
      if (mysqli_num_rows($check_email) > 0) {
         $duplicate = true;
         $message[] = 'Update failed: That email address is already in use!';
      }
   }

   if (!$duplicate) {
       
       $update_inst_query = "UPDATE instructors SET name = '$name', email = '$email'";
       $update_user_query = "UPDATE user_form SET name = '$name', email = '$email'";

      
       if(!empty($_POST['password'])){
          $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
          $update_inst_query .= ", password = '$pass'";
          $update_user_query .= ", password = '$pass'";
       }

     
       $image = $_FILES['image']['name'];
       $image_tmp_name = $_FILES['image']['tmp_name'];
       $image_folder = 'uploaded_img/'.$image;
       if(!empty($image)){
          $update_inst_query .= ", image = '$image'";
          move_uploaded_file($image_tmp_name, $image_folder);
       }

       
       $update_inst_query .= " WHERE id = '$update_id'";
       $update_user_query .= " WHERE email = '$old_email' AND user_type = 'instructor'";

      
       mysqli_query($conn, $update_inst_query);
       mysqli_query($conn, $update_user_query);

       $message[] = 'Instructor updated successfully!';
   }
}


if(isset($_GET['delete'])){
   $id = mysqli_real_escape_string($conn, $_GET['delete']);
   
   
   $get_email = mysqli_query($conn, "SELECT email FROM instructors WHERE id = '$id'");
   if(mysqli_num_rows($get_email) > 0){
       $email_data = mysqli_fetch_assoc($get_email);
       $inst_email = $email_data['email'];
     
       mysqli_query($conn, "DELETE FROM user_form WHERE email = '$inst_email' AND user_type = 'instructor'");
   }

   
   mysqli_query($conn, "DELETE FROM instructors WHERE id = '$id'");
   header('location:add_instructor.php');
   exit(); 
}


$instructors = mysqli_query($conn, "SELECT * FROM instructors");


$edit_mode = false;
$edit_data = null;
if(isset($_GET['edit'])){
    $edit_mode = true;
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit']);
    $edit_query = mysqli_query($conn, "SELECT * FROM instructors WHERE id = '$edit_id'");
    if(mysqli_num_rows($edit_query) > 0){
        $edit_data = mysqli_fetch_assoc($edit_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Instructors - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                BOT <span class="text-indigo-400">Admin</span>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin_panel.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="admin.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-book mr-3"></i>Manage Courses</a>
                <a href="add_instructor.php" class="flex items-center px-4 py-2 rounded-lg bg-gray-700"><i class="fas fa-user-plus mr-3"></i>Manage Instructors</a>
				<a href="index.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-home mr-3"></i>View Main Site</a>
                <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-red-600"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Instructor Management</h1>

            <?php
            if(!empty($message)){
               foreach($message as $msg){
                  // Display errors in red, success in blue/green
                  $bg_color = strpos($msg, 'failed') !== false ? 'bg-red-100 border-red-500 text-red-700' : 'bg-blue-100 border-blue-500 text-blue-700';
                  echo '<div class="'.$bg_color.' border-l-4 p-4 mb-4" role="alert">'.$msg.'</div>';
               }
            }
            ?>

            <div class="bg-white p-8 rounded-xl shadow-md mb-10 border-t-4 <?php echo $edit_mode ? 'border-green-500' : 'border-indigo-500'; ?>">
                <h2 class="text-xl font-semibold mb-6 text-gray-700">
                    <?php echo $edit_mode ? 'Update Instructor: ' . htmlspecialchars($edit_data['name'] ?? '') : 'Add New Instructor'; ?>
                </h2>
                <form action="add_instructor.php" method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="update_id" value="<?php echo htmlspecialchars($edit_data['id'] ?? ''); ?>">
                    <?php endif; ?>

                    <input type="text" name="name" placeholder="Full Name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['name'] ?? '') : ''; ?>" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none" required>
                    
                    <input type="email" name="email" placeholder="Email Address" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['email'] ?? '') : ''; ?>" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none" required>
                    
                    <div class="relative w-full">
                        <input type="password" name="password" id="instructor_password" placeholder="<?php echo $edit_mode ? 'New Password (leave blank to keep current)' : 'Password'; ?>" class="w-full p-3 border rounded-lg pr-10 focus:ring-2 focus:ring-indigo-400 outline-none" <?php echo $edit_mode ? '' : 'required'; ?>>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-gray-600" onclick="togglePassword('instructor_password', 'eyeIcon1')">
                            <i class="fas fa-eye" id="eyeIcon1"></i>
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="w-full p-2 border rounded-lg bg-gray-50" <?php echo $edit_mode ? '' : 'required'; ?>>
                        <?php if($edit_mode && !empty($edit_data['image'])): ?>
                            <img src="uploaded_img/<?php echo htmlspecialchars($edit_data['image'] ?? ''); ?>" class="h-10 w-10 rounded-full border shadow-sm" alt="Current image">
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2 flex gap-4">
                        <?php if($edit_mode): ?>
                            <button type="submit" name="update_instructor" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition">Update Instructor</button>
                            <a href="add_instructor.php" class="flex-1 bg-gray-400 text-white py-3 rounded-lg font-bold hover:bg-gray-500 transition text-center flex items-center justify-center">Cancel Edit</a>
                        <?php else: ?>
                            <button type="submit" name="add_instructor" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition">Add Instructor</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                        <tr>
                            <th class="p-4 border-b">ID</th>
                            <th class="p-4 border-b">Image</th>
                            <th class="p-4 border-b">Name</th>
                            <th class="p-4 border-b">Email</th>
                            <th class="p-4 border-b text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if($instructors && mysqli_num_rows($instructors) > 0) {
                            while($row = mysqli_fetch_assoc($instructors)){ 
                        ?>
                        <tr class="hover:bg-gray-50 transition <?php echo ($edit_mode && isset($edit_data['id']) && $edit_data['id'] == $row['id']) ? 'bg-indigo-50' : ''; ?>">
                            <td class="p-4 border-b font-medium"><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                            <td class="p-4 border-b">
                                <img src="uploaded_img/<?php echo htmlspecialchars($row['image'] ?? ''); ?>" class="h-12 w-12 rounded-full object-cover border" alt="">
                            </td>
                            <td class="p-4 border-b"><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                            <td class="p-4 border-b"><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                            <td class="p-4 border-b text-center space-x-2">
                                <a href="add_instructor.php?edit=<?php echo htmlspecialchars($row['id'] ?? ''); ?>" class="text-blue-500 hover:text-blue-700 p-2" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="add_instructor.php?delete=<?php echo htmlspecialchars($row['id'] ?? ''); ?>" class="text-red-500 hover:text-red-700 p-2" title="Delete" onclick="return confirm('Are you sure you want to delete this instructor?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo '<tr><td colspan="5" class="p-4 border-b text-center text-gray-500">No instructors found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>