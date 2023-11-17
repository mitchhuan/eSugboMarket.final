<?php
include 'config.php';

$name = '';
$email = '';
$number = '';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = $_POST['cpass'];
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    // Regular expression to match passwords with at least 8 characters and at least one number
    $passwordRegex = '/^(?=.*[a-z])(?=.*\d)[a-zA-Z\d]{8,}$/';

    // Regular expression to validate a phone number with 11 digits
    $phoneNumberRegex = '/^\d{11}$/';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email address.'; 
    }elseif (!preg_match($phoneNumberRegex, $number)) {
        $message[] = 'Phone number must have 11 digits.';
    }elseif (!preg_match($passwordRegex, $pass)) {
        $message[] = 'Password must have at least 8 characters with at least one number';
        // Clear the password fields on error
        $pass = $cpass = '';
    } elseif ($pass != $cpass) {
        $message[] = 'Confirm password not matched.';
        // Clear the password fields on error
        $pass = $cpass = '';
    } else {

        // Set a default image
        $defaultImage = 'default.png';
        $image = $defaultImage; // Assign the default image to the user
        
        $select = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
        $select->execute([$email, $number]);

        if ($select->rowCount() > 0) {
            $message[] = 'User email or phone number already exists.';
            // Clear the password fields on error
            $pass = $cpass = '';
        } else {
            // Hash the password
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
        
            $insert = $conn->prepare("INSERT INTO `users` (name, email, password, number, image) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$name, $email, $hashedPassword, $number, $image]);
        
            // Redirect and handle login
            $user_id = $conn->lastInsertId();
            session_start();
            $_SESSION['user_id'] = $user_id;
            header('Location: home.php');
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>

<?php include 'newheader.php'; ?>
   
<section class="form-container">

   <form action="" enctype="multipart/form-data" method="POST">
      <h3>register now</h3>
      <input type="text" name="name" class="box" placeholder="enter your name" required value="<?= $name ?>">
      <input type="email" name="email" class="box" placeholder="enter your email" required value="<?= $email ?>">
      <input type="number" name="number" class="box" placeholder="enter your number" required value="<?= $number ?>">
      <input type="password" name="pass" class="box" placeholder="enter your password" required>
      <input type="password" name="cpass" class="box" placeholder="confirm your password" required>
      <input type="submit" value="register now" class="btn" name="submit">
      <p>Want to register as <b style="color: red;">courier</b>? <a href="courreg.php">register</a></p>
      <p>Already have an account? <a href="login.php">login now</a></p>
   </form>

</section>

<script src="js/script.js"></script>

</body>
</html>
