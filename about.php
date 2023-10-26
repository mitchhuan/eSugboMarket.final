<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

// Fetch user details from the database
$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_user->execute([$user_id]);
$user = $select_user->fetch(PDO::FETCH_ASSOC);


if(isset($_POST['send'])){
   // Sanitize and retrieve form data
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);

   // Insert the message into the database
   $insert_message = $conn->prepare("INSERT INTO `message`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
   $insert_message->execute([$user_id, $user['name'], $user['email'], $number, $msg]);

   $message[] = 'Sent message successfully!';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="about">

   <div class="row">

      <div class="box">
         <img src="images/about-img-1.png" alt="">
         <h3>why choose us?</h3>
         <p>Save yourself the trouble of navigating busy markets. You can browse a wide variety of products from the comfort of your home with eSugboMarket. 
            Shop whenever and wherever you want, without having to worry about parking, long lines, or restricted hours.</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

      <div class="box">
         <img src="images/about-img-2.png" alt="">
         <h3>what we provide?</h3>
         <p>Discover a carefully chosen selection of locally sourced goods that have been hand-selected for their quality, authenticity, and charm. 
            Enjoy the seasonal foods that are at their peak right from your neighborhood farmers, bringing the lively flavors of your area to your dining table. 
            Discover a wide variety of handcrafted products that each tell a tale of the talented craftspeople who put their entire being into each creation. 
            Explore your community's culture, heritage, and creativity to find valuable and unique things.</p>
         <a href="shop.php" class="btn">our shop</a>
      </div>

   </div>

</section>

<!-- <section class="reviews"> -->
<section class="about">

<div class="row">

   <div class="box">
      <h3>about us</h3>
      <p>
         At eSugboMarket, we believe in celebrating the rich tapestry of local flavors, craftsmanship, and culture that your community has to offer. 
         Our mission is to connect you with a curated selection of the finest products from nearby vendors, just like you'd find in your beloved public market. 
         Whether you're craving farm-fresh produce, handcrafted goods, or unique finds that reflect the essence of your region, we've got you covered.</p>
   </div>
</div>

</section>




</section>
 
<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>