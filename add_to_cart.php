<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_name']) || !isset($_SESSION['id'])) {
  header('location: login_form.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
  $user_id = $_SESSION['id'];
  $course_id = intval($_POST['course_id']);

  $check = "SELECT * FROM cart WHERE user_id = '$user_id' AND course_id = '$course_id'";
  $result = mysqli_query($conn, $check);

  if (mysqli_num_rows($result) == 0) {
    $insert = "INSERT INTO cart (user_id, course_id) VALUES ('$user_id', '$course_id')";
    mysqli_query($conn, $insert);
  }

  header("Location: cart.php?added=1");
  exit();
} else {
  header('Location: course-page.php');
  exit();
}
?>

<?php