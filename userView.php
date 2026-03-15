<?php
include 'config.php'; // Include your database connection

$id = $_GET['id'];
// echo $id;
$sql = "SELECT * FROM `episode` WHERE e_id = '$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$filepath = $row['e_path'];
// echo $filename; 

?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <title>Video Playback</title>
</head>
<body>

    <div class="container mt-5">
        <p class="text fs-3"><?php echo $row['e_name'] ?></p>
        <p class="text fs-5"><?php echo $row['e_description'] ?></p>
        <video controls controlsList="nodownload" class="object-fit-sm-contain border rounded col-sm-8">
            <source src="<?php echo $filepath; ?>" type="video/mp4" >
            Your browser does not support the video tag.
        </video>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>
</html>