<?php
@include 'config.php';

session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
}

if (isset($_GET['order_id'])) {
   $order_id = $_GET['order_id'];


   // Fetch the selected order using the $order_id
   $select_order = $conn->prepare("SELECT * FROM `orders` WHERE order_id = ? AND user_id = ?");
   $select_order->execute([$order_id, $user_id]);

   if ($select_order->rowCount() > 0) {
      // The order was found, you can display the order details
      $fetch_order = $select_order->fetch(PDO::FETCH_ASSOC);
   } 
} else {
   // Handle the case where 'order_id' is not set in the URL
   echo '<p class="empty">Order ID not specified.</p>';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Order Details</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">
      <!-- font awesome cdn link  -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

      <!-- custom css file link  -->
      <link rel="stylesheet" href="css/style.css">

       

</head>
<body>

<?php include 'header.php'; ?>

<section class="placed-orders">


<h1 class="title">Order Details</h1>

   <div class="box-containers">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE order_id = ?");
      $select_orders->execute([$order_id]);
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){  
   ?>

<div class="box">
   <p> order id: <span><?= $fetch_orders['transaction_id']; ?></span> </p>
   <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
   <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
   <p> phone number : <span><?= $fetch_orders['number']; ?></span> </p>
   <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
   <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
   <p> your orders : <span><?= $fetch_orders['total_products']; ?></span> </p>
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
      </span> 
   </p>
   <p> time of order: <span><?= $fetch_orders['time_of_order']; ?></span> </p>
<!-- Responsive Progress Bar -->
<div class="progress-bar">
    <div class="step one<?= $fetch_orders['payment_status'] === 'pending' || 
                       $fetch_orders['payment_status'] === 'preparing order' || 
                       $fetch_orders['payment_status'] === 'order picked up' || 
                       $fetch_orders['payment_status'] === 'to be delivered' || 
                       $fetch_orders['payment_status'] === 'completed' ? 'active' : '' ?>">
        <i class="fa-solid fa-cart-shopping" style="color: <?= in_array($fetch_orders['payment_status'], ['pending', 'preparing order', 'order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></i>
        <p  style="color: <?= in_array($fetch_orders['payment_status'], ['pending', 'preparing order', 'order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>">Pending</p>
        <?php if ($fetch_orders['payment_status'] === 'pending') : ?>
         <span><?= $fetch_orders['time_of_order']; ?></span> </p>
        <?php endif; ?>
    </div>
    <div class="bar" style="background-color: <?= in_array($fetch_orders['payment_status'], ['preparing order', 'order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></div>
    <div class="step two<?= $fetch_orders['payment_status'] === 'pending' || 
                       $fetch_orders['payment_status'] === 'preparing order' || 
                       $fetch_orders['payment_status'] === 'order picked up' || 
                       $fetch_orders['payment_status'] === 'to be delivered' || 
                       $fetch_orders['payment_status'] === 'completed' ? 'active' : '' ?>">
           <i class="fa-solid fa-clipboard-list" style="color: <?= in_array($fetch_orders['payment_status'], ['preparing order', 'order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></i>
           <p  style="color: <?= in_array($fetch_orders['payment_status'], ['preparing order', 'order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>">Preparing Order</p>
        <?php if ($fetch_orders['payment_status'] === 'preparing order') : ?>
            <span><?= $fetch_orders['status_updated_at'] ?></span>
        <?php endif; ?>
    </div>
    <div class="bar" style="background-color: <?= in_array($fetch_orders['payment_status'], ['order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></div>
    <div class="step three<?= $fetch_orders['payment_status'] === 'pending' || 
                       $fetch_orders['payment_status'] === 'preparing order' || 
                       $fetch_orders['payment_status'] === 'order picked up' || 
                       $fetch_orders['payment_status'] === 'to be delivered' || 
                       $fetch_orders['payment_status'] === 'completed' ? 'active' : '' ?>">
        <i class="fa-solid fa-hand-holding" style="color: <?= in_array($fetch_orders['payment_status'], ['order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></i>
        <p style="color: <?= in_array($fetch_orders['payment_status'], ['order picked up', 'to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>">Order Picked Up</p>
        <?php if ($fetch_orders['payment_status'] === 'order picked up') : ?>
            <span><?= $fetch_orders['status_updated_at'] ?></span>
        <?php endif; ?>
    </div>
    <div class="bar" style="background-color: <?= in_array($fetch_orders['payment_status'], ['to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></div>
    <div class="step four<?= $fetch_orders['payment_status'] === 'pending' || 
                       $fetch_orders['payment_status'] === 'preparing order' || 
                       $fetch_orders['payment_status'] === 'order picked up' || 
                       $fetch_orders['payment_status'] === 'to be delivered' || 
                       $fetch_orders['payment_status'] === 'completed' ? 'active' : '' ?>">
        <i class="fa-solid fa-truck" style="color: <?= in_array($fetch_orders['payment_status'], ['to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>"></i>
        <p style="color: <?= in_array($fetch_orders['payment_status'], ['to be delivered', 'completed']) ? 'lightgreen' : 'grey'; ?>">To Be Delivered</p>
        <?php if ($fetch_orders['payment_status'] === 'to be delivered') : ?>
            <span><?= $fetch_orders['status_updated_at'] ?></span>
        <?php endif; ?>
    </div>
    <div class="bar" style="background-color: <?= in_array($fetch_orders['payment_status'], ['completed']) ? 'lightgreen' : 'grey'; ?>"></div>  
    <div class="step five<?= $fetch_orders['payment_status'] === 'pending' || 
                       $fetch_orders['payment_status'] === 'preparing order' || 
                       $fetch_orders['payment_status'] === 'order picked up' || 
                       $fetch_orders['payment_status'] === 'to be delivered' || 
                       $fetch_orders['payment_status'] === 'completed' ? 'active' : '' ?>">
        <i class="fa-solid fa-circle-check" style="color: <?= $fetch_orders['payment_status'] === 'completed' ? 'lightgreen'  : 'grey'; ?>"></i>
        <p  style="color: <?= $fetch_orders['payment_status'] === 'completed' ? 'lightgreen' : 'grey'; ?>">Completed</p>
        <?php if ($fetch_orders['payment_status'] === 'completed' ) : ?>
            <span><?= $fetch_orders['status_updated_at'] ?></span>
        <?php endif; ?>
    </div>
    <!-- Repeat this structure for other steps -->
</div>




</div>

<style>
/* Existing CSS styles */

/* Add this CSS for responsive progress bar */
.progress-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 80%; /* Adjust the width as needed */
    margin: 20px auto; /* Adjust the margin as needed */
}

/* Add this CSS for the bar between steps */
.bar {
    width: 125px; /* Adjust the width as needed */
    height: 2px; /* Adjust the height as needed */
    background-color: var(--black); /* Adjust the bar color as needed */
}

.step i {
    font-size: 32px;
}

.step.active {
    color: var(--green); /* Change the color of active steps */
}

/* Add media query for small screens */
@media (max-width: 600px) {
    .progress-bar {
        flex-direction: column; /* Change to a vertical layout */
        align-items: flex-start; /* Align steps to the left */
    }

    .step {
        align-items: flex-start; /* Align step content to the left */
        text-align: left; /* Adjust text alignment */
        margin-bottom: 10px; /* Add some space between steps */
    }

    .bar {
        width: 2px; /* Make the bar thinner for vertical layout */
        height: 100px; /* Adjust the height as needed */
    }
}


.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 16px;
    color: var(--black);
}

/* Define the blinking animation */
@keyframes blink {
  0% {
    opacity: 1; /* Element is visible at the start */
  }
  50% {
    opacity: 0.2; /* Element becomes invisible halfway through */
  }
  100% {
    opacity: 1; /* Element is visible again at the end */
  }
}

/* Apply the animation to the element with the "blinking" class */
.blinking {
  animation: blink 1s infinite; /* 1s duration and infinite iterations */
}



</style>



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
