<?php
include 'config.php';

session_start();

if (!isset($conn)) {
    die("Database connection failed. Please check your config.php file.");
}

if (!isset($_SESSION['admin_name'])) {
    header('location:login_form.php');
    exit();
}

$message = '';
$message_type = '';

if (isset($_POST['add_instructor'])) {
    $i_name = mysqli_real_escape_string($conn, $_POST['i_name']);
    $i_email = mysqli_real_escape_string($conn, $_POST['i_email']);
    $i_expertise = mysqli_real_escape_string($conn, $_POST['i_expertise']);
    
   
    $i_image = $_FILES['i_image']['name'];
    $i_image_tmp_name = $_FILES['i_image']['tmp_name'];
    $i_image_size = $_FILES['i_image']['size'];

   
    $image_extension = pathinfo($i_image, PATHINFO_EXTENSION);
    $unique_image_name = time() . '_' . rand(100, 999) . '.' . $image_extension;

    $instructor_image_folder = 'instructor_img/';
    
 
    if (!is_dir($instructor_image_folder)) {
        mkdir($instructor_image_folder, 0777, true);
    }
    
    $i_image_path = $instructor_image_folder . $unique_image_name;

    if (empty($i_name) || empty($i_email) || empty($i_expertise) || empty($i_image)) {
        $message = 'Please fill out all fields.';
        $message_type = 'error';
    } elseif ($i_image_size > 2000000) {
        $message = 'Image size is too large. Please upload an image smaller than 2MB.';
        $message_type = 'error';
    }
    else {
      
        $insert_query = mysqli_query($conn, "INSERT INTO `instructors`(name, email, expertise, image) VALUES('$i_name', '$i_email', '$i_expertise', '$unique_image_name')") or die('Query failed: ' . mysqli_error($conn));

        if ($insert_query) {
            move_uploaded_file($i_image_tmp_name, $i_image_path);
            $message = 'New instructor added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Could not add the instructor. Please try again.';
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Instructor</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .form-container {
            transition: transform 0.3s ease-in-out;
        }
        .form-container:hover {
            transform: translateY(-5px);
        }
        input[type="file"]::file-selector-button {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        input[type="file"]::file-selector-button:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body class="antialiased">

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl mx-auto">

        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 rounded-lg text-white text-center <?php echo ($message_type === 'success') ? 'bg-green-500' : 'bg-red-500'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden form-container">
            <div class="grid grid-cols-1 md:grid-cols-2">
                
                <div class="p-8 md:p-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Add New Instructor</h2>
                    <p class="text-gray-500 mb-8">Fill in the details to add a new instructor to the platform.</p>
                    
                    <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                        
                        <div>
                            <label for="i_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="i_name" name="i_name" placeholder="e.g., John Doe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                        </div>
                        
                        <div>
                            <label for="i_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="i_email" name="i_email" placeholder="e.g., john.doe@example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                        </div>

                        <div>
                            <label for="i_expertise" class="block text-sm font-medium text-gray-700 mb-1">Area of Expertise</label>
                            <input type="text" id="i_expertise" name="i_expertise" placeholder="e.g., Web Development, Data Science" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" required>
                        </div>

                        <div>
                            <label for="i_image" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                            <input type="file" id="i_image" name="i_image" accept="image/png, image/jpg, image/jpeg" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required onchange="previewImage(event)">
                        </div>

                        <div>
                            <button type="submit" name="add_instructor" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
                                Add Instructor
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-gray-50 p-8 md:p-12 flex flex-col items-center justify-center">
                    <div class="w-48 h-48 rounded-full bg-gray-200 mb-6 flex items-center justify-center overflow-hidden shadow-inner">
                        <img id="imagePreview" src="https://placehold.co/192x192/EFEFEF/AFAFAF?text=Photo" alt="Image Preview" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700">Photo Preview</h3>
                    <p class="text-gray-500 text-center mt-2 text-sm">The selected instructor's profile photo will appear here. Recommended size: 400x400px.</p>
                </div>

            </div>
        </div>
        <div class="text-center mt-6">
            <a href="admin.php" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">← Back to Admin Dashboard</a>
        </div>
    </div>
</div>

<script>
    const previewImage = event => {
        const imagePreview = document.getElementById('imagePreview');
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        } else {
            imagePreview.src = 'https://placehold.co/192x192/EFEFEF/AFAFAF?text=Photo';
        }
    };
</script>

</body>
</html>