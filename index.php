<?php

@include 'config.php';

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'newheader.php'; ?>

<div class="home-bg">

   <section class="home">

      <div class="content">
         <span>choose what you want</span>
         <h3>Reach For A Healthier You at Carbon Market</h3>
         <p>Welcome to eSugboMarket, your one-stop destination for all your local and artisanal needs! 
            We are a passionate team dedicated to bringing the charm of your favorite public market right to your doorstep.</p>
         <a href="#category" class="btn">Get Started</a>
      </div>

   </section>

</div>

<section class="home-category" id="category">

   <h1 class="title">shop by category</h1>

   <div class="box-container">
      <a href="newcategory.php?category=fruits and vegetables">
      <div class="box">
         <img src="images/cat-1.png" alt="">
         <h3>Fresh fruits and vegetables</h3>

      </div>
      </a>
      

      <a href="newcategory.php?category=poultry and meat">
      <div class="box">
         <img src="images/cat-2.png" alt="">
         <h3>Poultry and meat products</h3>

      </div>
      </a>

      <a href="newcategory.php?category=Drygoods and grains">
      <div class="box">
         <img src="images/cat-3.png" alt="">
         <h3>Dry goods and grains</h3>
      </div>
      </a>

      <a href="newcategory.php?category=fresh seafood">
      <div class="box">
         <img src="images/cat-4.png" alt="">
         <h3>Fresh seafood</h3>
      </div>
      </a>

      <a href="newcategory.php?category=spices and condiments">
      <div class="box">
         <img src="images/spices.png" alt="">
         <h3>Spices and condiments</h3>
      </div>
      </a>

      <a href="newcategory.php?category=local snacks and street food">
      <div class="box">
         <img src="images/street food.png" alt="">
         <h3>Local snacks and street food</h3>
      </div>
      </a>

      <a href="newcategory.php?category=clothing and apparel">
      <div class="box">
         <img src="images/clothing.png" alt="">
         <h3>Clothing and apparel</h3>
      </div>
      </a>

      <a href="newcategory.php?category=footwear and accessories">
      <div class="box">
         <img src="images/footwear.png" alt="">
         <h3>Footwear and accessories</h3>
      </div>
      </a>

      <a href="newcategory.php?category=handicrafts and souvenirs">
      <div class="box">
         <img src="images/souvenir.png" alt="">
         <h3>Handicrafts and souvenirs</h3>
      </div>
      </a>

      <a href="newcategory.php?category=kitchen Stuff">
      <div class="box">
         <img src="images/utensils.png" alt="">
         <h3>Kitchen Stuff</h3>
      </div>
      </a>

   </div>

</section>


<section class="products" >

   <h1 class="title">latest products</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 8");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" class="box" method="POST">
      <a href="newview_page.php?pid=<?= $fetch_products['id']; ?>">
      <div class="price">₱<span><?= $fetch_products['price']; ?></span></div>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
      </a>
      <input type="number" min="1" value="1" name="p_qty" class="qty">
      <a href="login.php" class="option-btn">Add to Wishlist</a>
      <a href="login.php" class="btn">Add to Cart</a>
    
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>