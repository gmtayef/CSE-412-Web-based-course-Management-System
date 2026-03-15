<?php
    include 'config.php';

    $key = $_GET['key'];
    session_start();
// $name = $_SESSION['user_name'];
// $sql = "SELECT `id` FROM `user_form` WHERE `name` = '$name'";
// $result = mysqli_query($conn, $sql);
// $row = mysqli_fetch_assoc($result);

if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}

    $query = "SELECT * FROM `products` WHERE `name` like '%$key%'";
    $result = mysqli_query($conn, $query);
    




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day One</title>
    
    <!-- Gooogle Font -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;600&family=Poppins:wght@300;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/b67581ec1b.js" crossorigin="anonymous"></script>

    <!-- Css -->
    <link rel="stylesheet" href="css/course-page.css">

</head>

<body>

    <header>
        <div class="header-container">
            <h1 class="logo">Learn <span class="span-one">Skill</span></h1>
            <nav>
                <ul class="menu">
                    <li><a href="index1.php">Home</a></li>
                    <li><a href="course-page.php">Courses</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </nav>

            <div class="menu-next">
                <div class="auth-buttons">
                <li><a href="useProfile.php?id=<?php echo $_SESSION['id'] ?>"><?php echo $_SESSION['user_name'] ?></a></li>
          <li><a href="logout.php" >logout</a><li>
                </div>
            </div>

        </div>
    </header>

    <!-- menu end -->

    <!-- body start -->


    <section class="another">
        <div class="another-one">
            <h2><span class="section-heading">Find best Courses</span></h2>
        </div>

        <div class="another-two">
            <a href="#" class="home-link">Home</a> <h4>/ Courses</h4>
        </div>
    </section>

    <!-- pagination search bar start -->

    <div class="pagination-search-bar">
        <p class="pagination">Showing 1-6 of 8 results</p>
        
    </div>

    <!-- pagination search bar end -->

    <!-- course section start -->

    <section class="course-section">
        <div class="row">
                    <?php
                            
                            while ($row = mysqli_fetch_assoc($result)) {                    

                    ?>
            <div class="course">
                <div class="course-image">
                    <img src="uploaded_img/<?php echo $row ['image'] ?>" alt="Course Image 1">
                    <div class="price">৳<?php echo $row ['price'] ?></div>
                </div>
                <div class="course-details">
                <a style="text-decoration: none;" href="course-details.php?id=<?php echo $row['id'] ?>"><h2><?php echo $row ['name'] ?></h2></a>
                    <p><?php echo $row ['description'] ?></p>
                    <!-- <div class="stats">
                        <p><i class="fas fa-users"></i> <span class="count">340</span> students</p>
                        <p><i class="fas fa-book"></i> <span class="count">82</span> lessons</p>
                    </div> -->
                </div>
            </div>
        </div>
        <?php }?>
        
        
    </section>

    

    <!-- course section end -->

    <!-- page number and arrow section start -->

 

    <!-- end -->

    <!-- footer start -->

    <section class="footer-menu">
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col" id="company">
                        <!-- <img src="image/Lofo-one.jpg" alt="" class="logo"> -->
                        <h1 class="logo-new">. Learn <span class="span-one">Skill</span></h1>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos magni expedita laboriosam deleniti eaque corrupti hic itaque illum ab architecto?</p>
    
                        <div class="social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                            <a href="#"><i class="fa fa-youtube"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                            <a href="#"><i class="fa fa-github"></i></a>
                        </div>
                    </div>
                    <div class="col" id="Explore">
                        <h3>Explore</h3>
                        <div class="links">
                            <a href="about.html">About us</a>
                            <a href="contact.html">Conatct us</a>
                            <a href="FAQ.html">FAQ</a>
                            <a href="privacy-policy.html">Privacy policy</a>
                        </div>
                    </div>
                    <div class="col" id="Courses">
                        <h3>Courses</h3>
                        <div class="links">
                            <a href="#">Python</a>
                            <a href="#">Java</a>
                            <a href="#">C++</a>
                            <a href="#">C</a>
                        </div>
                    </div>
                    <div class="col" id="address">
                        <h3>Address</h3>
                            <div class="address-details">
                                <i class="fa fa-location"></i>
                                <p>Aftabnagor, <br> Dhaka, <br> Bangladesh</p>
                            </div>
                            <div class="address-details">
                                <i class="fa fa-phone"></i>
                                <p>+88017**********<p>
                            </div>
                        </div>
                </div>

                <div class="row">
                    <div class="form">
                        <form action="">
                            <input type="text" placeholder="Email here...">
                            <button><i class="fa fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bottom-details">
                <p>All Right reserved by &copy;Learn Skill 2023</p>
            </div>
    
        </footer>
    </section>


    <script src="js/app.js"></script>
</body>
</html> 
    

    

