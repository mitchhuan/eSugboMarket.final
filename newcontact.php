<?php
ob_start();
@include 'config.php';

session_start();

if(isset($_SESSION['user_id'])){
   header('location:about.php#contact');
   exit;
}

if(isset($_POST['send'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $msg = $_POST['msg'];
   $msg = filter_var($msg, FILTER_SANITIZE_STRING);
   $subject = $_POST['subj'];
   $subject= filter_var($subject, FILTER_SANITIZE_STRING);

   // You can remove the code that checks for duplicate messages since this is a public page

   // Insert the message directly into the database
   $insert_message = $conn->prepare("INSERT INTO `message`(name, email, number, message) VALUES(?,?,?,?,?)");
   $insert_message->execute([$name, $email, $number, $subject, $msg]);

   $message[] = 'Sent message successfully!';

}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Public Contact</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'newheader.php'; ?>


<section class="contact">


<section class="about">

<div class="row">

   <div class="box">
      <h3>Contact Us!</h3>
      <p>If you have any suggestions or complaints or questions, please don't hesitate to contact us. 
         Your feedback is important to us.</p>
   </div>
</div>

</section>

   <form action="" method="POST" >
      <input type="text" name="name" class="box" required placeholder="Enter your name">
      <input type="email" name="email" class="box" required placeholder="Enter your email">
      <input type="number" name="number" min="0" class="box" required placeholder="Enter your number">
      <input type="subject" name="subject" class="box" placeholder="Subject of message">
      <textarea name="msg" class="box" required placeholder="Enter your message" cols="30" rows="10"></textarea>
      <input type="submit" value="Send Message" class="btn" name="send">
   </form>

</section>








<?php include 'newfooter.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
