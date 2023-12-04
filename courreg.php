<?php
@include 'config.php';

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

    // Check if any documents were uploaded
    $documents = [];

    if (isset($_FILES['documents'])) {
        $documentDirectory = 'document_uploads/';

        foreach ($_FILES['documents']['name'] as $key => $documentName) {
            if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                $documentName = filter_var($documentName, FILTER_SANITIZE_STRING);
                $document_tmp_name = $_FILES['documents']['tmp_name'][$key];
                $uniqueDocumentName = time() . '_' . $documentName;
                $documentPath = $documentDirectory . $uniqueDocumentName;

                // Move the uploaded document to the server
                move_uploaded_file($document_tmp_name, $documentPath);

                // Store document information
                $documents[] = [
                    'name' => $uniqueDocumentName,
                    'path' => $documentPath,
                ];
            }
        }
    }

    // Regular expression to match passwords with at least 8 characters and at least one number
    $passwordRegex = '/^(?=.*[a-z])(?=.*\d)[a-zA-Z\d]{8,}$/';

    // Regular expression to validate a phone number with 11 digits
    $phoneNumberRegex = '/^\d{11}$/';
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email address.';
    } elseif (!preg_match($phoneNumberRegex, $number)) {
        $message[] = 'Phone number must have 11 digits.';
    } elseif (!preg_match($passwordRegex, $pass)) {
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
        
            // Insert user information into the database
            $insert = $conn->prepare("INSERT INTO `users` (name, email, password, number, image, user_type) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$name, $email, $hashedPassword, $number, $image, 'ucour']); // Set user_type to 'ucour'

            // Retrieve the courier's user ID
            $courier_id = $conn->lastInsertId();

            // Insert document information into the database with the associated courier ID
            foreach ($documents as $document) {
                $insertDocument = $conn->prepare("INSERT INTO `documents` (courier_id, document_name, document_path) VALUES (?, ?, ?)");
                $insertDocument->execute([$courier_id, $document['name'], $document['path']]);
            }

            // Redirect to the courier page with user_id parameter
            session_start();
            $_SESSION['cour_id'] = $courier_id;
            header('Location: courier_page.php');
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
   <title>Courier Registration</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

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

<form action="" enctype="multipart/form-data" method="POST">
      <h3>Courier Registration</h3>
      <input type="text" name="name" class="box" placeholder="Enter your name" required value="<?= $name ?>">
      <input type="email" name="email" class="box" placeholder="Enter your email" required value="<?= $email ?>">
      <input type="number" name="number" class="box" placeholder="Enter your number" required value="<?= $number ?>">
      <input type="password" name="pass" class="box" placeholder="Enter your password" required>
      <input type="password" name="cpass" class="box" placeholder="Confirm your password" required>
      <label for="documents">Upload Necessary Documents for Verification:</label>
      <input type="file" name="documents[]" accept="application/pdf" class="box" multiple required>
      <input type="submit" value="Register Now" class="btn" name="submit">
      <p>already have an account? <a href="login.php">Login now</a></p>
      <p>go back to user registration <a href="register.php">Back</a></p>
   </form>

</section>

<script src="js/script.js"></script>

</body>
</html>
