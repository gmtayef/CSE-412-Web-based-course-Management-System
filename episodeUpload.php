<?php
include 'config.php';

$showAlert = false;
$showError = false;

$courseID = null;
$row = null;

if (isset($_GET['id'])) {
    $courseID = $_GET['id'];
    $query = "SELECT * FROM `products` WHERE id = '$courseID'";
    $result1 = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result1);
} else {
    $showError = "No course ID provided!";
}

if (isset($_POST['upload']) && $courseID !== null) {
    $targetDirection = "episodes/";
    $title = $_POST["title"];
    $description = $_POST["description"];
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDirection . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    $allowedTypes = array("mp4", "avi", "mov", "mkv");

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $sql = "INSERT INTO `episode`(`e_name`, `e_description`, `e_path`, `e_file`, `course_id`) 
                    VALUES ('$title','$description', '$targetFilePath', '$fileName', '$courseID')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $showAlert = true;
            } else {
                $showError = 'Database error';
            }
        } else {
            $showError = 'Failed to move uploaded file';
        }
    } else {
        $showError = 'Invalid file format';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Episode Upload</title>
  <link rel="stylesheet" href="css/episodeupload.css" />
</head>
<body>
  <div class="container">

    <a href="admin.php" class="back-btn">← Admin Home</a>

    <?php if ($showAlert): ?>
      <div class="alert success">✅ Video uploaded successfully!</div>
    <?php endif; ?>

    <?php if ($showError): ?>
      <div class="alert error">❌ <?= $showError ?></div>
    <?php endif; ?>

    <?php if ($courseID && $row): ?>
      <h2 class="heading">Upload Episode for: <span><?= $row['name'] ?></span></h2>

      <form action="episodeupload.php?id=<?= $courseID ?>" method="post" enctype="multipart/form-data">
        <label>Episode Title</label>
        <input type="text" name="title" required placeholder="Enter episode title" />

        <label>Upload Video File</label>
        <input type="file" name="file" accept="video/*" required />

        <label>Description</label>
        <textarea name="description" rows="3" required placeholder="Write description..."></textarea>

        <input type="submit" name="upload" value="Upload Episode" class="submit-btn" />
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
