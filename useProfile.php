<?php
    include 'config.php';
    session_start();
    $id = $_SESSION['id'] ?? 'NULL';
    
    $query = "SELECT * FROM `products` WHERE id IN (SELECT `course_id` FROM `order` WHERE user_id = '$id')"; //subquery
    $result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <title>User Profile</title>
    <style>
       .navbarcustom {
	   background-color: transparent !important;
	   /* height: 20%; */
	   /* position: fixed; */
	   /*width: 90%; */
   

	   }
	   .text{
		 color: white;
	   }
	 .btn:hover{
		 background-color: #ed0a0ae0; 
		 color: white;
	 }
     .dropdown-menu {
        --bs-dropdown-link-active-bg: #ED0A0A;
     }

   </style>
</head>
<body">
    
        
        <?php
            include 'config.php';
            $userSql = "SELECT `id`, `name`, `email`, `password` FROM `user_form` WHERE `id`= $id; ";
            $userResult = mysqli_query($conn, $userSql);
            while ($row = mysqli_fetch_assoc($userResult)) {

        ?>


        
            <div class="container emp-profile text-black">
                <form method="post">
                    
                        <div class="col-md-6">
                            <div class="profile-head">
                                        <h5 class="text-uppercase">
                                            <?php echo $row['name'] ?>
                                        </h5>                                 
                                
                            </div>
                        </div>
                        
                    
                    
                        <div class="container-fluid col-md-8 text-black">
                            <div class="tab-content profile-tab" id="myTabContent">
                                <h3>About</h3>
                                <hr class="my-3">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>User Id</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><?php echo $row['id'] ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Name</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><?php echo $row['name'] ?></p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Email</label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><?php echo $row['email'] ?></p>
                                                </div>
                                            </div>
                                            
        <?php } ?>
                                </div>
                                <hr class="my-3">
                            </div>
                        </div>
                                
                </form>           
            </div>

            <div class="container">
                <p class="text-black col-sm-12 d-inline-flex gap-1 fs-2">My Courses  <button class="btn btn-transparent fs-2 border-dark rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample1" aria-expanded="false" aria-controls="collapseExample">
                >
                </button>
            </p>
                

                    <div class="row collapse" id="collapseExample1">
                    <section class="course-section">
                        <div class="row">
                                    <?php
                                            
                                            while ($row = mysqli_fetch_assoc($result)) {                    

                                    ?>
                            <div class="course">
                                <div class="course-image">
                                    <img src="uploaded_img/<?php echo $row ['image'] ?>" alt="Course Image 1">
                                    
                                </div>
                                <div class="course-details">
                                <a style="text-decoration: none;" href="userEpisodeList.php?id=<?php echo $row['id'] ?>"><h2 class="text-black"><?php echo $row ['name'] ?></h2></a>
                                 
                                    <!-- <div class="stats">
                                        <p><i class="fas fa-users"></i> <span class="count">340</span> students</p>
                                        <p><i class="fas fa-book"></i> <span class="count">82</span> lessons</p>
                                    </div> -->
                                </div>
                            </div>
        </div>
        <?php }?>
        
        
    </section>
            
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js" integrity="sha384-Rx+T1VzGupg4BHQYs2gCW9It+akI2MM/mndMCy36UVfodzcJcF0GGLxZIzObiEfa" crossorigin="anonymous"></script>
</body>
</html>
