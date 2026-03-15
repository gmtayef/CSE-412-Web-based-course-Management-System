<?php
@include 'config.php';
session_start();

// Redirect to login if no session
if (!isset($_SESSION['user_name'])) {
   header('location: login_form.php');
   exit();
}

$user_type = $_SESSION['user_type']; // Should be 'admin' or 'user'
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Courses</title>
   <link rel="stylesheet" href="css/style122.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '<div class="message"><span>' . $msg . '</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`; "></i> </div>';
   }
}
?>

<?php include 'header1.php'; ?>

<div class="container">
   <section class="products">
      <h1 class="heading">Available Courses</h1>

      <div class="box-container">
         <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products`");
         if (mysqli_num_rows($select_products) > 0) {
            while ($fetch_product = mysqli_fetch_assoc($select_products)) {
         ?>
            <form action="add_to_cart.php" method="post">
               <div class="box">
                  <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="">
                  <h3><?php echo $fetch_product['name']; ?></h3>
                  <div class="price">৳<?php echo $fetch_product['price']; ?></div>

                  <!-- Hidden inputs -->
                  <input type="hidden" name="product_id" value="<?php echo $fetch_product['id']; ?>">
                  <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">

                  <?php if ($user_type === 'user'): ?>
                     <input type="submit" class="btn" value="<?php echo ($fetch_product['price'] == 0) ? 'Enroll' : 'Add to Cart'; ?>" name="add_to_cart">
                  <?php else: ?>
                     <p style="margin-top:10px; color: gray;">(Admin View Only)</p>
                  <?php endif; ?>
               </div>
            </form>
         <?php
            }
         } else {
            echo "<p>No courses available.</p>";
         }
         ?>
      </div>
   </section>
</div>

<script src="js/script.js"></script>
</body>
</html>
