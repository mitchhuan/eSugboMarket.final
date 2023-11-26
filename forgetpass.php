<?php
@include 'config.php';

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    $sql = "SELECT id FROM `users` WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        // Generate a unique token for password reset
        $resetToken = bin2hex(random_bytes(32));

        // Store the token and its expiration time in the database
        $expireTime = date('Y-m-d H:i:s', strtotime('+1 hour')); // Change the expiration time as needed
        $updateToken = $conn->prepare("UPDATE `users` SET reset_token = ?, reset_token_expire = ? WHERE email = ?");
        $updateToken->execute([$resetToken, $expireTime, $email]);

        // Send an email to the user with the reset link
        $resetLink = "http://yourwebsite.com/reset_password.php?token=$resetToken"; // Change the URL as needed
        $subject = "Password Reset";
        $message = "Click the following link to reset your password: $resetLink";
        mail($email, $subject, $message);

        $successMessage = 'Password reset link sent to your email. Please check your inbox.';
    } else {
        $errorMessage = 'Email address not found.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Forget Password</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">

</head>
<body>

<?php
if (isset($successMessage)) {
    echo '<div class="success-message"><span>' . $successMessage . '</span></div>';
} elseif (isset($errorMessage)) {
    echo '<div class="error-message"><span>' . $errorMessage . '</span></div>';
}
?>

<?php include 'newheader.php'; ?>
   
<section class="form-container">

   <form action="" method="POST">
      <h3>Forgot Password</h3>
      <input type="email" name="email" class="box" placeholder="Enter your email" required value="<?= isset($email) ? $email : '' ?>">
      <input type="submit" value="Submit" class="btn" name="submit">
      <p>Remember your password? <a href="login.php">Login now</a></p>
   </form>

</section>

<script src="js/script.js"></script>

</body>
</html>
