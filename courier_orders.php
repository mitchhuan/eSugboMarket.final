<?php

@include 'config.php';

session_start();

$courier_id = $_SESSION['courier_id'];

if(!isset($courier_id)){
   header('location:login.php');
};

if(isset($_POST['update_order'])){

   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE order_id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'order has been updated!';

};
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
   <link rel="stylesheet" href="css/admin_style.css">

   <!-- <script>
   function toggleButtons() {
    var acceptButton = document.getElementById("accept");
    var updateButton = document.getElementById("update");
    
        acceptButton.style.visibility = "hidden";
        updateButton.style.visibility = "visible";
   }
   </script> -->

</head>
<body>
   
<?php include 'courier_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">placed orders</h1>

   <div class="box-container">

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status != 'completed'");
         $select_orders->execute();
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
         <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
         <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
         <p> total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> total price : <span>₱<?= $fetch_orders['total_price']; ?></span> </p>
         <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['order_id']; ?>">
            <select name="update_payment" class="drop-down">
               <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
               <option value="preparing order">preparing order</option>
               <option value="order picked up">order picked up</option>
               <option value="to be delivered">to be delivered</option>
               <option value="completed">completed</option>
            </select>
            <div class="flex-btn">
               <!-- <input onclick="toggleButtons()" id="accept" type="submit" name="accept_order" class="option-btn" value="accept"> -->
               <input type="submit" name="update_order"<?= $fetch_orders['order_id']; ?> class="option-btn" value="update">
            </div> 
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>