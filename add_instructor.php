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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Instructors - BOT Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] text-slate-800 selection:bg-indigo-500 selection:text-white flex min-h-screen flex-col md:flex-row overflow-hidden">
    <div class="md:hidden fixed top-0 left-0 right-0 h-16 bg-white/90 backdrop-blur-lg border-b border-slate-200 z-50 flex items-center justify-between px-6 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">B</div>
            <div class="font-extrabold text-xl tracking-tight text-slate-800">BOT <span class="text-indigo-600">Admin</span></div>
        </div>
        <button onclick="document.getElementById('mobile-menu').classList.toggle('-translate-x-full')" class="text-slate-600 hover:text-indigo-600 transition-colors bg-slate-100 p-2 rounded-lg">
            <i class="fas fa-bars text-lg"></i>
        </button>
    </div>
    <aside id="mobile-menu" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out z-50 w-72 bg-[#090e17] border-r border-slate-800/50 flex flex-col h-screen shadow-2xl md:shadow-none">
        <div class="flex items-center justify-between p-6 md:p-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">B</div>
                <div class="text-2xl font-extrabold tracking-tight text-white">
                    BOT <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Admin</span>
                </div>
            </div>
            <button onclick="document.getElementById('mobile-menu').classList.toggle('-translate-x-full')" class="md:hidden text-slate-400 hover:text-white p-2 bg-slate-800/50 rounded-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-5 flex-1 overflow-y-auto mt-2">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-3 px-3">Main Navigation</div>
            <nav class="space-y-2">
                <a href="admin_panel.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-grid-2 text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-semibold">Dashboard</span>
                </a>                
                <a href="admin.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-layer-group text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-semibold">Manage Courses</span>
                </a>
                
                <a href="add_instructor.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-500/25 border-l-[3px] border-indigo-300">
                    <i class="fas fa-chalkboard-user text-lg w-8 text-indigo-100"></i>
                    <span class="font-semibold">Manage Instructors</span>
                </a>
                <a href="index.php" class="flex items-center px-4 py-3.5 rounded-xl transition-all duration-300 relative group text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                    <i class="fas fa-globe text-lg w-8 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="font-semibold">View Site</span>
                    <i class="fas fa-external-link-alt text-[10px] ml-auto opacity-40"></i>
                </a>
            </nav>
        </div>
        <div class="p-5 mt-auto">
            <div class="bg-gradient-to-b from-slate-800/40 to-slate-900/80 rounded-2xl p-4 border border-slate-800/50">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-slate-700 border-2 border-slate-600 flex items-center justify-center text-slate-300 font-bold shrink-0">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-bold text-slate-200 truncate"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></div>
                        <div class="text-xs text-indigo-400 font-medium">Administrator</div>
                    </div>
                </div>
                <a href="logout.php" class="flex items-center justify-center w-full py-2.5 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-all duration-300 font-bold text-sm gap-2 border border-rose-500/20 hover:border-rose-500">
                    <i class="fas fa-power-off"></i> Sign Out
                </a>
            </div>
        </div>
    </aside>
    <main class="flex-1 overflow-y-auto h-screen pt-20 md:pt-0">
        <div class="p-6 md:p-12 max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Instructor Management</h1>
                    <p class="text-slate-500 mt-2">Add, update, and manage your course instructors.</p>
                </div>
            </div>
            <?php
            if(!empty($message)){
               foreach($message as $msg){
                  $is_error = strpos(strtolower($msg), 'failed') !== false || strpos(strtolower($msg), 'could not') !== false || strpos(strtolower($msg), 'already exists') !== false;
                  $bg_color = $is_error ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700';
                  $icon = $is_error ? 'fa-circle-exclamation text-rose-500' : 'fa-circle-check text-emerald-500';
                  echo '
                  <div class="'.$bg_color.' border p-4 mb-8 rounded-2xl flex items-center shadow-sm" role="alert">
                      <i class="fas '.$icon.' text-xl mr-3"></i>
                      <span class="font-medium">'.$msg.'</span>
                  </div>';
               }
            }
            ?>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-10 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 <?php echo $edit_mode ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : 'bg-gradient-to-r from-indigo-500 to-purple-500'; ?>"></div>
                <h2 class="text-xl font-bold mb-8 text-slate-800 flex items-center">
                    <span class="bg-slate-100 p-2 rounded-lg mr-3">
                        <i class="fas <?php echo $edit_mode ? 'fa-user-pen text-emerald-500' : 'fa-user-plus text-indigo-500'; ?>"></i>
                    </span>
                    <?php echo $edit_mode ? 'Update Instructor: ' . htmlspecialchars($edit_data['name'] ?? '') : 'Add New Instructor'; ?>
                </h2>               
                <form action="add_instructor.php" method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="update_id" value="<?php echo htmlspecialchars($edit_data['id'] ?? ''); ?>">
                    <?php endif; ?>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Full Name</label>
                        <input type="text" name="name" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['name'] ?? '') : ''; ?>" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all duration-200" required>
                    </div>                  
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Email Address</label>
                        <input type="email" name="email" value="<?php echo $edit_mode ? htmlspecialchars($edit_data['email'] ?? '') : ''; ?>" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all duration-200" required>
                    </div>                    
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Password <?php echo $edit_mode ? '<span class="text-slate-400 font-normal">(Leave blank to keep)</span>' : ''; ?></label>
                        <div class="relative w-full">
                            <input type="password" name="password" id="instructor_password" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl pr-12 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all duration-200" <?php echo $edit_mode ? '' : 'required'; ?>>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 cursor-pointer text-slate-400 hover:text-indigo-500 transition-colors" onclick="togglePassword('instructor_password', 'eyeIcon1')">
                                <i class="fas fa-eye" id="eyeIcon1"></i>
                            </span>
                        </div>
                    </div>                   
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-600 ml-1">Profile Image</label>
                        <div class="flex items-center gap-4">
                            <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" class="w-full text-sm text-slate-500 file:mr-4 file:py-3.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all cursor-pointer bg-slate-50 border border-slate-200 rounded-xl" <?php echo $edit_mode ? '' : 'required'; ?>>
                            <?php if($edit_mode && !empty($edit_data['image'])): ?>
                                <div class="shrink-0 h-14 w-14 rounded-xl overflow-hidden border-2 border-emerald-100 shadow-sm relative group">
                                    <img src="uploaded_img/<?php echo htmlspecialchars($edit_data['image'] ?? ''); ?>" class="h-full w-full object-cover" alt="">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="md:col-span-2 pt-4 flex gap-4">
                        <?php if($edit_mode): ?>
                            <button type="submit" name="update_instructor" class="flex-1 bg-emerald-600 text-white py-3.5 rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all duration-200 hover:-translate-y-0.5">Update Instructor</button>
                            <a href="add_instructor.php" class="flex-1 bg-slate-200 text-slate-700 py-3.5 rounded-xl font-bold hover:bg-slate-300 transition-all duration-200 text-center hover:-translate-y-0.5 flex items-center justify-center">Cancel Edit</a>
                        <?php else: ?>
                            <button type="submit" name="add_instructor" class="w-full md:w-auto px-10 bg-indigo-600 text-white py-3.5 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-600/30 transition-all duration-200 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i> Add Instructor
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Current Instructors</h2>
                    <span class="bg-indigo-100 text-indigo-700 py-1 px-3 rounded-full text-xs font-bold"><?php echo mysqli_num_rows($instructors); ?> Total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/80 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="p-6">Instructor</th>
                                <th class="p-6">Contact Info</th>
                                <th class="p-6 text-center">ID Tag</th>
                                <th class="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php 
                            if($instructors && mysqli_num_rows($instructors) > 0) {
                                while($row = mysqli_fetch_assoc($instructors)){ 
                                    $is_editing = ($edit_mode && isset($edit_data['id']) && $edit_data['id'] == $row['id']);
                            ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-200 group <?php echo $is_editing ? 'bg-indigo-50/50' : ''; ?>">
                                <td class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-white shadow-md shrink-0">
                                            <img src="uploaded_img/<?php echo htmlspecialchars($row['image'] ?? ''); ?>" class="h-full w-full object-cover" alt="">
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900"><?php echo htmlspecialchars($row['name'] ?? ''); ?></div>
                                            <div class="text-xs text-slate-500 font-medium mt-0.5">Joined recently</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <div class="flex items-center text-slate-600 text-sm font-medium">
                                        <i class="fas fa-envelope text-slate-400 mr-2"></i>
                                        <?php echo htmlspecialchars($row['email'] ?? ''); ?>
                                    </div>
                                </td>
                                <td class="p-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        ID: <?php echo htmlspecialchars($row['id'] ?? ''); ?>
                                    </span>
                                </td>
                                <td class="p-6 text-right space-x-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="add_instructor.php?edit=<?php echo htmlspecialchars($row['id'] ?? ''); ?>" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-200 shadow-sm" title="Edit">
                                        <i class="fas fa-pen text-sm"></i>
                                    </a>
                                    <a href="add_instructor.php?delete=<?php echo htmlspecialchars($row['id'] ?? ''); ?>" class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all duration-200 shadow-sm" title="Delete" onclick="return confirm('Are you sure you want to completely remove this instructor?')">
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="4" class="p-12 text-center text-slate-500 bg-slate-50/50 rounded-b-3xl"><div class="flex flex-col items-center justify-center"><i class="fas fa-users-slash text-4xl mb-3 text-slate-300"></i><p class="font-medium">No instructors found. Start by adding one above.</p></div></td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
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
