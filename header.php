<header class="header">

   <div class="flex">

      <a href="index.php" class="logo">Learn skill</a>

      <nav class="navbar">
         <a href="admin.php">add Course</a>
         <a href="products.php">view Course</a>
      </nav>

      <?php
      
      $select_rows = mysqli_query($conn, "SELECT * FROM `cart`") or die('query failed');
      $row_count = mysqli_num_rows($select_rows);

      ?>


      <div id="menu-btn" class="fas fa-bars"></div>

   </div>

</header>