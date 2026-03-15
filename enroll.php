<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('Location: login_form.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $user_id = $_SESSION['id'];
    $course_id = intval($_POST['course_id']);

    // Check if already enrolled
    $check = "SELECT * FROM enrollments WHERE user_id = '$user_id' AND course_id = '$course_id'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) == 0) {
        $insert = "INSERT INTO enrollments (user_id, course_id) VALUES ('$user_id', '$course_id')";
        mysqli_query($conn, $insert);
    }

    // Redirect to show success
    header('Location: my-courses.php?enrolled=1');
    exit();
} else {
    // If accessed directly or without POST
    header('Location: course-page.php');
    exit();
}
