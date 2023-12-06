<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}

$select_courier = $conn->prepare("SELECT user_type FROM `users` WHERE id = ?");
$select_courier->execute([$cour_id]);
$courier = $select_courier->fetch(PDO::FETCH_ASSOC);

?>


<header class="header">

   <div class="flex">

      <a href="courier_page.php" class="logo" title="CourierPanel">Courier<span>Panel</span></a>

      <nav class="navbar">
         <a href="courier_page.php">home</a>
         <?php if ($courier['user_type'] === 'cour') echo '<a href="courier_orders.php">orders</a>';?>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user" title="User"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$cour_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
         <p><?= $fetch_profile['name']; ?></p>
         <a href="courier_update_profile.php" class="btn">update profile</a>
         <a href="logout.php" class="logout-btn">logout</a>
      </div>

   </div>

</header>