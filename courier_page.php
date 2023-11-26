<?php
ob_start();
@include 'config.php';

session_start();

$cour_id = $_SESSION['cour_id'];

if (!isset($cour_id)) {
    header('Location: login.php');
    exit;
}

   $select_courier = $conn->prepare("SELECT user_type FROM `users` WHERE id = ?");
   $select_courier->execute([$cour_id]);
   $courier = $select_courier->fetch(PDO::FETCH_ASSOC);

   if ($courier['user_type'] !== 'cour') {
      // If the user is not a courier, display an error message
      $message [] = "You do not have access to this page yet. Only approved couriers can use courier privileges.
       Your courier account has a pending approval. 
       You will have access to courier privileges once approved by the admin.
       Please wait for at least 24 to 72 hours. Thank you!";
   }

   
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Courier Page</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
   
<?php include 'courier_header.php'; ?>

<section class="dashboard">

   <h1 class="title">dashboard</h1>

   <div class="box-container">
  
      <div class="box">
      <?php
         if ($courier['user_type'] === 'cour') {
            $total_completed = 0;
            $select_completed = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ? AND courier_id = ?");
            $select_completed->execute(['completed', $cour_id]);
            while($fetch_completed = $select_completed->fetch(PDO::FETCH_ASSOC)){
               $total_completed += $fetch_completed['total_price'];
            }
            echo '<h3>₱' . $total_completed . '</h3>';
         }
      ?>
      <p>completed orders</p>
      <a href="courier_completedorders.php" class="btn <?php if ($courier['user_type'] !== 'cour') echo 'disabled'; ?>">see orders</a>
      </div>

      <div class="box">
      <?php
         if ($courier['user_type'] === 'cour') {
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE payment_status != 'completed' AND courier_id IS NULL");
            $select_orders->execute();
            $number_of_orders = $select_orders->rowCount();
            echo '<h3>' . $number_of_orders . '</h3>';
         }
      ?>
      <p>orders available</p>
      <a href="courier_orders.php" class="btn <?php if ($courier['user_type'] !== 'cour') echo 'disabled'; ?>">see orders</a>
      </div>
   </div>
</section>

<script src="js/script.js"></script>

</body>
</html>


