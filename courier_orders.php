<?php
@include 'config.php';

session_start();

$cour_id = $_SESSION['cour_id'];

if(!isset($cour_id)){
   header('location:login.php');
};

if(isset($_POST['update_order'])){

   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE order_id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'Payment status has been updated!';

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv=X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">

   <script>
   function confirmAccept(orderId) {
      if (confirm("Are you sure you want to accept this order?")) {
         document.getElementById("accept_" + orderId).style.display = "none";
         document.getElementById("update_" + orderId).style.visibility = "visible";
         document.getElementById("message_" + orderId).style.display = "block";
      }
   }
   </script>
</head>
<body>
   
<?php include 'courier_header.php'; ?>

<section class="placed-orders">
   <h1 class="title">Placed Orders</h1>
   <div class="box-container">
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status != 'completed'");
         $select_orders->execute();
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p>Placed on: <span><?= $fetch_orders['placed_on']; ?></span></p>
         <p>Name: <span><?= $fetch_orders['name']; ?></span></p>
         <p>Number: <span><?= $fetch_orders['number']; ?></span></p>
         <p>Address: <span><?= $fetch_orders['address']; ?></span></p>
         <p>Total Products: <span><?= $fetch_orders['total_products']; ?></span></p>
         <p>Total Price: <span>₱<?= $fetch_orders['total_price']; ?></span></p>
         <p>Payment Method: <span><?= $fetch_orders['method']; ?></span></p>
         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['order_id']; ?>">
            <select name="update_payment" class="drop-down">
               <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
               <option value="intransit">In Transit</option>
               <option value="orderpicked">Order Picked</option>
               <option value="abouttodeliver">About to Deliver</option>
               <option value="completed">Completed</option>
            </select>
            <div class="flex-btn">
               <input id="accept_<?= $fetch_orders['order_id']; ?>" type="button" name="accept_order" class="option-btn" value="Accept" onclick="confirmAccept(<?= $fetch_orders['order_id']; ?>)">
               <input id="update_<?= $fetch_orders['order_id']; ?>" style="visibility:hidden" type="submit" name="update_order" class="btn" value="Update">
               <a id="message_<?= $fetch_orders['order_id']; ?>" style="display:none" href="message.php?order_id=<?= $fetch_orders['order_id']; ?>" class="btn">Message</a>
            </div>
         </form>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">No orders placed yet!</p>';
      }
      ?>
   </div>
</section>

<script src="js/script.js"></script>

</body>
</html>
