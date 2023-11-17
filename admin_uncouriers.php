<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Accounts</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">Pending approvals</h1>

   <div class="box-container">

      <?php
         $select_users = $conn->prepare("SELECT * FROM users WHERE user_type='ucour'");
         $select_users->execute();

         if ($select_users->rowCount() > 0) {
            while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <img src="uploaded_img/<?= $fetch_users['image']; ?>" alt="">
         <p> User ID: <span><?= $fetch_users['id']; ?></span></p>
         <p> Username: <span><?= $fetch_users['name']; ?></span></p>
         <p> User Type: <span style="color:<?php if($fetch_users['user_type'] == 'ucour'){ echo 'orange'; }; ?>"><?= $fetch_users['user_type']; ?></span></p>
         <a href="admin_user_details.php?id=<?= $fetch_users['id']; ?>" class="btn">View Details</a>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No user accounts found.</p>';
         }
      ?>
   </div>

</section>

</section>

<script src="js/script.js"></script>

</body>
</html>
