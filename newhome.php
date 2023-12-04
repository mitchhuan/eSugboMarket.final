<?php
ob_start();
@include 'config.php';

session_start();

if(isset($_SESSION['user_id'])){
   header('location:home.php');
   exit;
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>eSugboMarket</title>
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

    <h1 class="title">Shop by Category</h1>

    <div class="box-container">
        <?php
        // Retrieve categories from the database
        $select_categories = $conn->prepare("SELECT * FROM `categories` ORDER BY RAND()");
        $select_categories->execute();

        while ($category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            $imagePath = "images/" . $category['image']; // Assuming the column name is 'image'
            ?>
            <a href="newcategory.php?category=<?= urlencode($category['name']); ?>">
                <div class="box">
                    <img src="<?= $imagePath; ?>" alt="<?= $category['name']; ?>">
                    <h3><?= $category['name']; ?></h3>
                </div>
            </a>
        <?php
        }
        ?>
    </div>

</section>



<section class="products" >

   <h1 class="title">latest products</h1>

   <div class="box-container">

   <?php
    $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC LIMIT 8");
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
    } else {
        echo '<p class="empty">No products added yet!</p>';
    }
?>


   </div>

</section>

<?php include 'newfooter.php'; ?>

<script src="js/script.js"></script>

</body>
</html>