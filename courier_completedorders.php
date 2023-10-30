<?php

@include 'config.php';

session_start();

$courier_id = $_SESSION['courier_id'];

if(!isset($courier_id)){
   header('location:login.php');
};

// if(isset($_POST['update_order'])){

//    $order_id = $_POST['order_id'];
//    $update_payment = $_POST['update_payment'];
//    $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
//    $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
//    $update_orders->execute([$update_payment, $order_id]);
//    $message[] = 'payment has been updated!';

// };

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_orders->execute([$delete_id]);
   header('location:admin_orders.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>completed orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'courier_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">completed orders</h1>

   <div class="box-container">

      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = 'completed'");
         $select_orders->execute();
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p> user id : <span><?= $fetch_orders['user_id']; ?></span> </p>
         <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
         <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
         <p> email : <span><?= $fetch_orders['email']; ?></span> </p>
         <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
         <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
         <p> total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
         <p> total price : <span>₱<?= $fetch_orders['total_price']; ?></span> </p>
         <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['order_id']; ?>">
               <p style="border-style: dashed; border-color: blue; text-align: center; color:#6495ED; font-size: 30px;">COMPLETED</p>
            <div class="flex-btn">
               <a href="courier_orders.php?delete=<?= $fetch_orders['order_id']; ?>" class="delete-btn" onclick="return confirm('delete this order?');">delete</a>
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