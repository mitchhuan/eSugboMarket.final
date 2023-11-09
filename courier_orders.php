<?php
@include 'config.php';

session_start();

$cour_id = $_SESSION['cour_id'];

if (!isset($cour_id)) {
   header('location: login.php');
}

if (isset($_POST['update_order'])) {
   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);
   $update_orders = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE order_id = ?");
   $update_orders->execute([$update_payment, $order_id]);
   $message[] = 'Status has been updated!';
}

if (isset($_POST['accept_order'])) {
   $order_id = $_POST['order_id'];
   $cour_id = $_SESSION['cour_id'];

   // Check if the order has been accepted by the current courier or is still unassigned
   $check_accepted = $conn->prepare("SELECT courier_id FROM `orders` WHERE order_id = ?");
   $check_accepted->execute([$order_id]);
   $result = $check_accepted->fetch(PDO::FETCH_ASSOC);

   if ($result['courier_id'] == $cour_id || $result['courier_id'] === null) {
      // Update the orders table with the courier assignment
      $update_assignment = $conn->prepare("UPDATE `orders` SET courier_id = ? WHERE order_id = ?");
      $update_assignment->execute([$cour_id, $order_id]);
      $message[] = 'You have accepted the order!';

      // Send a message with order details to the user
      $orderDetailsQuery = $conn->prepare("SELECT * FROM `orders` WHERE order_id = ?");
      $orderDetailsQuery->execute([$order_id]);
      $orderDetails = $orderDetailsQuery->fetch(PDO::FETCH_ASSOC);

      $messageContent = "Your order has been accepted by the courier. \n";
      $messageContent .= "Address: {$orderDetails['address']}\n";
      $messageContent .= "Total Products: {$orderDetails['total_products']}\n";
      $messageContent .= "Time of Order: {$orderDetails['time_of_order']}\n";
      // ... (include other details as needed)

      // Insert the new message into the 'messages' table
      $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, order_id, message_content, timestamp, is_read, is_deleted)
         VALUES (?, ?, ?, ?, NOW(), 0, 0)");
      $insertMessageQuery->execute([$cour_id, $orderDetails['user_id'], $order_id, $messageContent]);

   } else {
      $message[] = 'This order has already been accepted by another courier.';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE,initial-scale=1.0">
   <title>Courier Orders</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      /* Add any custom CSS styles here */
      .hidden {
         display: none;
      }
   </style>
</head>
<body>

<?php include 'courier_header.php'; ?>

<section class="placed-orders">
   <h1 class="title">Placed Orders</h1>
   <div class="box-container">
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE courier_id IS NULL OR courier_id = ? AND payment_status != 'completed' ORDER BY transaction_id");
         $select_orders->execute([$cour_id]);  
      if ($select_orders->rowCount() > 0) {
         while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
            $order_id = $fetch_orders['order_id'];
      ?>
            <div class="box">
               <p>Placed on: <span><?= $fetch_orders['placed_on']; ?></span></p>
               <p>Name: <span><?= $fetch_orders['name']; ?></span></p>
               <p>Number: <span><?= $fetch_orders['number']; ?></span></p>
               <p>Address: <span><?= $fetch_orders['address']; ?></span></p>
               <p>Total Products: <span><?= $fetch_orders['total_products']; ?></span></p>
               <p>Total Price: <span>₱<?= $fetch_orders['total_price']; ?></span></p>
               <p>Payment Method: <span><?= $fetch_orders['method']; ?></span></p>
               <p>Time of Order: <span><?= $fetch_orders['time_of_order']; ?></span> </p>
               <form action="" method="POST">
                  <input type="hidden" name="order_id" value="<?= $order_id ?>">
                  <?php
                  if (!$fetch_orders['courier_id'] == $cour_id) {
                  ?>
                  <button type="submit" name="accept_order" class="option-btn" data-order-id="<?= $order_id ?>" onclick="return confirmAccept(<?= $order_id ?>)">Accept</button>
                  <?php
                  } else {
                  ?>
                  <!-- Display accepted order details -->
                  <select name="update_payment" class="drop-down" data-order-id="<?= $order_id ?>">
                     <option value="<?= $fetch_orders['payment_status']; ?>" selected><?= $fetch_orders['payment_status']; ?></option>
                     <option value="preparing order">Preparing Order</option>
                     <option value="order picked up">Order Picked Up</option>
                     <option value="to be delivered">To Be Delivered</option>
                     <option value="completed">Completed</option>
                  </select>
                  <button type="submit" name="update_order" class="option-btn" data-order-id="<?= $order_id ?>">Update</button>
                  <a href="courier_contact.php?user_id=<?= $fetch_orders['user_id'] ?>&order_id=<?= $order_id ?>" class="btn" data-order-id="<?= $order_id ?>">Message</a>
                  <?php
                  }
                  ?>
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

<script>
   function confirmAccept(orderId) {
      return confirm("Accept the order?");
   }
</script>

</body>
</html>





