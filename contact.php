<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

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
   <title>contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>


<section class="contact">


<section class="about">

<div class="row">

   <div class="box">
      <h3>Contact Us!</h3>
      <p>If you have any suggestions or complaints, please don't hesitate to contact our website. 
         Your feedback is important to us.</p>
   </div>
</div>

</section>

   <form action="" method="POST" >
      <!-- Display user's name and email as read-only fields -->
      <input type="text" name="name" class="box" value="<?= $user['name'] ?>" readonly>
      <input type="email" name="email" class="box" value="<?= $user['email'] ?>" readonly>
      <input type="text" name="name" class="box" required placeholder="enter your name">
      <input type="email" name="email" class="box" required placeholder="enter your email">
      <input type="number" name="number" min="0" class="box" required placeholder="enter your number">
      <input type="number" name="number" min="0" class="box" value="<?= $user['number'] ?>" readonly>
      <textarea name="msg" class="box" required placeholder="enter your message" cols="30" rows="10"></textarea>
      <input type="submit" value="send message" class="btn" name="send">
   </form>


</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
