<?php
include 'config.php';
$courseId = $_GET['id'];
$query = "SELECT * FROM `products` WHERE id = '$courseId'";
$result1 = mysqli_query($conn, $query);
$row1 = mysqli_fetch_assoc($result1);

$sql = "SELECT * FROM episode WHERE course_id = '$courseId' ORDER BY e_id ASC";
$result = mysqli_query($conn, $sql);

if (isset($_POST['delete'])) {
    include 'config.php';
    $del_id = $_POST['idToDelete'];
    $sql2 = "DELETE FROM episode WHERE e_id = $del_id";
    if (mysqli_query($conn, $sql2)) {
      header('Location: adminHome.php');
    } else {
      echo "Error!" . mysqli_error($conn);
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Episode List</title>
    <link rel="stylesheet" href="css/course-details-C.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  
</head>

<body>
<header>
        <div class="header-container">
            <h1 class="logo">Learn <span class="span-one">Skill</span></h1>
            <nav>
                <ul class="menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="course-page.php">Courses</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>

            <div class="menu-next">
                <div class="auth-buttons">
              
                </div>
            </div>

        </div>
    </header>


<section class="another">
      <div class="another-one">
        <h2>
          <span class="section-heading"
            >Mastering C From Zero To<br />Hero</span
          >
        </h2>
      </div>

      <div class="another-two">
        <a href="quiz-C.php" class="home-link">Quiz</a>
        <h4>/ Mastering C from zero to hero</h4>
      </div>
    </section>

    <div class="course-container">
      <div class="left-div">
        <div class="image">
         <img src="uploaded_img/<?php echo $row1 ['image'] ?>" style="width: 31rem;" alt="Course Image 1">
        </div>
        <div class="tabs">
          <button class="tab-button" onclick="showContent('overview')">
            Overview
          </button>
          <button class="tab-button" onclick="showContent('curriculum')">
            Curriculum
          </button>
          <button class="tab-button" onclick="showContent('instructor')">
            Instructor
          </button>
        </div>
        <div class="content" id="overview-content">
          <h1>Overview</h1>
          <p>
          <?php echo $row1 ['description'] ?>
          </p>
          <h1>What You will Learn?</h1>
          <p>
            ✅Clean up face imperfections, improve and repair photos<br />
            ✅Remove people or objects from photos<br />
            ✅Master selections, layers, and working with the layers panel<br />
            ✅Use creative effects to design stunning text styles<br />
            ✅working with the layers panel<br />
            ✅Cut away a person from their background
          </p>
        </div>
        <div class="content" id="curriculum-content">
          <h3>Section 1</h3>
          <p>Video, quiz for you</p>
          <p></p>
          <div class="curriculum-lesson">
            <span>Lesson 1</span>
            <span>10 min</span>
          </div>
          <div class="curriculum-lesson">
            <span>Lesson 2</span>
            <span>30 min</span>
          </div>
          <div class="curriculum-lesson">
            <span>Quiz 1</span>
            <span>14 questions</span>
            <span>10 min</span>
          </div>
        </div>
        <div class="content" id="instructor-content">
          <p>Rakib</p>
          <p>Web Developer</p>
          <p>I am here for you, Best of luck.</p>
        </div>
      </div>
      <div class="main-content">
        <h2><?php echo $row1 ['name'] ?></h2>
        <div class="container mt-5">
        <p class="text fs-2">Course Materials of <?php echo $row1 ['name'] ?></p>
        <table class="table table table-striped border rounded">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Episode Name</th>
                
                
                </tr>
            </thead>
            <?php
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                // echo "<li><a href='view.php?id={$row['m_id']}'>{$row['m_name']}</a></li>";
                $i = $i+1;
            ?>
            
            
            <tbody>
                <tr>
                <th scope="row"><?php echo $i ?></th>
                <td><a class="text-decoration-none text- btn btn-sm border-info rounded" href="userView.php?id=<?php echo $row['e_id'] ?>"><?php echo $row['e_name'] ?></a></td>
                
                
                
                </tr>
            </tbody>
            <?php } ?>
        </table>
        
     </div>
      </div>
    </div>

    

     <script src="js/course-details-C.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>
</html>