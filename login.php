<?php
@include 'config.php';

session_start();

$email = '';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $sql = "SELECT id, password, user_type FROM `users` WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $rowCount = $stmt->rowCount();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowCount > 0) {
        $hashedPassword = $row['password'];
        if (password_verify($pass, $hashedPassword)) {
            // Password is correct, perform login based on user_type
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_id'] = $row['id'];
                header('location:admin_page.php');
            } elseif ($row['user_type'] == 'cour') {
                $_SESSION['cour_id'] = $row['id'];
                header('location:courier_page.php');
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_id'] = $row['id'];
                header('location:home.php');
            } elseif ($row['user_type'] == 'ucour') {
                $_SESSION['cour_id'] = $row['id'];
                header('location:courier_page.php');
            } else {
                $message[] = 'No user found!';
            }
        } else {
            $message[] = 'Incorrect password!';
        }
    } else {
        $message[] = 'No user found!';
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>

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

?>

<?php include 'newheader.php'; ?>
   
<section class="form-container">

   <form action="" method="POST">
      <h3>login now</h3>
      <input type="email" name="email" class="box" placeholder="enter your email" required value="<?= $email ?>">
      <input type="password" name="pass" class="box" placeholder="enter your password" required>
      <input type="submit" value="login now" class="btn" name="submit">
      <p>Don't have an account? <a href="register.php">register now</a></p>
   </form>

</section>

<script src="js/script.js"></script>

</body>
</html>