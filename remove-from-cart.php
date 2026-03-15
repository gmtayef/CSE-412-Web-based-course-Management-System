<?php
include 'config.php';
session_start();
if (!isset($_SESSION['id'])) {
  header('location: login_form.php');
  exit();
}
$user_id = $_SESSION['id'];
$course_id = $_GET['id'];

mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id' AND course_id = '$course_id'");
header("Location: cart.php?removed=1");
exit();
?>
