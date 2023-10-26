<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">placed orders</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
      $select_orders->execute([$user_id]);
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){ 

      $order_id = uniqid();       
   ?>
   <div class="box">
   <a href="order_details.php?order_id=<?= $fetch_orders['order_id']; ?>">
   <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
   <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
   <p> your orders : <span><?= $fetch_orders['total_products']; ?> </span> </p>
   <p> total price : <span>₱<?= $fetch_orders['total_price']; ?></span> </p>
   <p> status : <span style="color:<?php
    $status = $fetch_orders['payment_status'];
    if ($status == 'pending') {
        echo 'red';
    } elseif ($status == 'preparing order' || $status == 'order picked up' || $status == 'to be delivered') {
        echo 'orange';
    } elseif ($status == 'completed') {
        echo 'green';
    } else {
        echo 'black'; // Set a default color for other statuses if needed
    }
?>"><?= $status; ?></span>

   <span><?= $fetch_orders['status_updated_at']; ?></span> </p>
   </a>
   </div>

   <?php
      }
   }else{
      echo '<p class="empty">no orders placed yet!</p>';
   }
   ?>

   </div>

</section>



<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>