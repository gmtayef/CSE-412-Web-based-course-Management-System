<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

// Handle Adding New Instructor
if(isset($_POST['add_instructor'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
   $image = $_FILES['image']['name'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   $select = "SELECT * FROM instructors WHERE email = '$email'";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $message[] = 'Instructor already exists!';
   } else {
      $insert = "INSERT INTO instructors(name, email, password, image) VALUES('$name', '$email', '$pass', '$image')";
      $upload = mysqli_query($conn, $insert);
      if($upload){
         move_uploaded_file($image_tmp_name, $image_folder);
         $message[] = 'New instructor added successfully!';
      } else {
         $message[] = 'Could not add the instructor.';
      }
   }
}

// Handle Deleting Instructor
if(isset($_GET['delete'])){
   $id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM instructors WHERE id = $id");
   header('location:manage_instructors.php');
}

$instructors = mysqli_query($conn, "SELECT * FROM instructors");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Instructors - M & S Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">M & S Admin</div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="admin_panel.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-3"></i>Dashboard</a>
                <a href="admin.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700"><i class="fas fa-book mr-3"></i>Manage Courses</a>
                <a href="manage_instructors.php" class="flex items-center px-4 py-2 rounded-lg bg-gray-700"><i class="fas fa-user-plus mr-3"></i>Manage Instructors</a>
                <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg hover:bg-red-600"><i class="fas fa-sign-out-alt mr-3"></i>Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Instructor Management</h1>

            <?php
            if(isset($message)){
               foreach($message as $msg){
                  echo '<div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">'.$msg.'</div>';
               }
            }
            ?>

            <div class="bg-white p-8 rounded-xl shadow-md mb-10">
                <h2 class="text-xl font-semibold mb-6 text-gray-700">Add New Instructor</h2>
                <form action="" method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="text" name="name" placeholder="Full Name" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none" required>
                    <input type="email" name="email" placeholder="Email Address" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none" required>
                    <input type="password" name="password" placeholder="Password" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none" required>
                    <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="w-full p-2 border rounded-lg bg-gray-50" required>
                    <button type="submit" name="add_instructor" class="md:col-span-2 bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition">Add Instructor</button>
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
                        <?php while($row = mysqli_fetch_assoc($instructors)){ ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 border-b font-medium"><?php echo $row['id']; ?></td>
                            <td class="p-4 border-b">
                                <img src="uploaded_img/<?php echo $row['image']; ?>" class="h-12 w-12 rounded-full object-cover border" alt="">
                            </td>
                            <td class="p-4 border-b"><?php echo $row['name']; ?></td>
                            <td class="p-4 border-b"><?php echo $row['email']; ?></td>
                            <td class="p-4 border-b text-center">
                                <a href="manage_instructors.php?delete=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 p-2" onclick="return confirm('Delete this instructor?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>