<?php






?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Preview</title>
    <link rel="stylesheet" href="css/course-details-php.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    
    <header>
        <div class="header-container">
            <h1 class="logo">Learn <span class="span-one">Skill</span></h1>
            <nav>
                <ul class="menu">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="course-page.html">Courses</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </nav>

            <div class="menu-next">
                <div class="auth-buttons">
                    <a href="#">Login</a>
                    <!-- <a href="#">Sign Up</a> -->
                    <a href="#">Sign In</a>
                </div>
            </div>

        </div>
    </header>

    <section class="another">
        <div class="another-one">
            <h2><span class="section-heading">Mastering PHP From Zero To<br>Hero</span></h2>
        </div>

        <div class="another-two">
            <a href="#" class="home-link">Home</a> <h4>/  Mastering PHP from zero to hero</h4>
        </div>
    </section>


    <div class="course-container">
        <div class="left-div">
            <div class="video">
                <iframe src="https://www.youtube.com/embed/your-video-id" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="tabs">
                <button class="tab-button" onclick="showContent('overview')">Overview</button>
                <button class="tab-button" onclick="showContent('curriculum')">Curriculum</button>
                <button class="tab-button" onclick="showContent('instructor')">Instructor</button>
            </div>
            <div class="content" id="overview-content">
                <h1>Overview</h1>
                <p>Knowing PHP has allowed me to make enough money to stay home and make courses like this one for students all over the world. Being a PHP developer can allow anyone to make really good money online and offline, developing dynamic applications. Knowing PHP will allow you to build web applications, websites or Content Management systems, like WordPress, Facebook, Twitter or even Google. There is no limit to what you can do with this knowledge. PHP is one of the most important web programming languages to learn, and knowing it, will give you SUPER POWERS in the web</p>
                <h1>What You will Learn?</h1>
                <p> 
                    ✅Clean up face imperfections, improve and repair photos<br>
                    ✅Remove people or objects from photos<br>
                    ✅Master selections, layers, and working with the layers panel<br>
                    ✅Use creative effects to design stunning text styles<br>
                    ✅working with the layers panel<br>
                    ✅Cut away a person from their background
                </p>
            </div>
            <div class="content" id="curriculum-content">
                <h3>Section 1</h3>
                <p>Video, quiz for you <p>
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
            <h2>Course Details</h2>
            <p>Price: ৳199</p>
            <p>Lectures: 12</p>
            <p>Enrolled: 120</p>
            <p>Duration: 8 weeks</p>
            <p>Updated: August 24, 2023</p>
            <!-- <button class="enroll-button">Enroll Course</button> -->
            <button class="enroll-button" onclick="showSuccessMessage()">Enroll Course</button>
    <p id="success-message" class="success-message">You are successfully enrolled in the course!</p>
        </div>
    </div>

    <!-- realated -->

    <section class="related">
        <h1>Related Courses You may like</h1>
      
        <section class="course-section">
            <div class="course">
                <div class="course-image">
                    <img src="image/course.jpg" alt="Course Image 1">
                    <div class="price">৳199</div>
                </div>
                <div class="course-details">
                    <h2>Web Design Course</h2>
                    <p>Master the art of creating stunning websites. Join our comprehensive course to learn HTML, CSS, and JavaScript.</p>
                    <div class="stats">
                        <p><i class="fas fa-users"></i> <span class="count">340</span> students</p>
                        <p><i class="fas fa-book"></i> <span class="count">82</span> lessons</p>
                    </div>
                </div>
            </div>
            
            <div class="course">
                <div class="course-image">
                    <img src="image/course-2.jpg" alt="Course Image 2">
                    <div class="price">৳249</div>
                </div>
                <div class="course-details">
                    <h2>JavaScript Mastery</h2>
                    <p>Unlock the power of JavaScript. Build interactive and dynamic web applications with hands-on projects and exercises.</p>
                    <div class="stats">
                        <p><i class="fas fa-users"></i> <span class="count">450</span> students</p>
                        <p><i class="fas fa-book"></i> <span class="count">64</span> lessons</p>
                    </div>
                </div>
            </div>
            
            <div class="course">
                <div class="course-image">
                    <img src="image/course-3.jpg" alt="Course Image 3">
                    <div class="price">৳299</div>
                </div>
                <div class="course-details">
                    <h2>UI/UX Design Essentials</h2>
                    <p>Create stunning user interfaces and engaging user experiences. Dive into design principles, wireframing, and prototyping.</p>
                    <div class="stats">
                        <p><i class="fas fa-users"></i> <span class="count">280</span> students</p>
                        <p><i class="fas fa-book"></i> <span class="count">72</span> lessons</p>
                    </div>
                </div>
            </div>
        </section>

    </section>

    <!-- footer -->

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
    
    <script src="js/course-details-php.js"></script>
</body>
</html>

