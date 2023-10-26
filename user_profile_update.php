<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

$message = array();

if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $number = $_POST['number'];
    $number = filter_var($number, FILTER_SANITIZE_STRING);


    // Check if the updated email is already in use
    $email_check_query = $conn->prepare("SELECT id FROM `users` WHERE email = ? AND id != ?");
    $email_check_query->execute([$email, $user_id]);
    if ($email_check_query->rowCount() > 0) {
        $message[] = 'Email is already in use.';
    }

    // Check if the updated phone number is already in use
    $phone_check_query = $conn->prepare("SELECT id FROM `users` WHERE number = ? AND id != ?");
    $phone_check_query->execute([$number, $user_id]);
    if ($phone_check_query->rowCount() > 0) {
        $message[] = 'Phone number is already in use.';
    } elseif (strlen($number) < 11) {
        $message[] = 'Phone number must have at least 11 digits.';
    }

    if (empty($message)) {
        // Email and phone number are unique, and phone number is at least 11 digits, so proceed with the update.
        $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ?, number = ? WHERE id = ?");
        $update_profile->execute([$name, $email, $number, $user_id]);
    
      // ...

// Handle image update
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/';
    $old_image = $_POST['old_image'];

    if ($image_size > 1000000) {
        $message[] = 'Image size is too large!';
    } else {
        // Generate a unique filename
        $info = pathinfo($image);
        $basename = $info['filename'];
        $extension = $info['extension'];
        $counter = 1;

        while (file_exists($image_folder . $image)) {
            $image = $basename . '_' . $counter . '.' . $extension;
            $counter++;
        }

        $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
        $update_image->execute([$image, $user_id]);
        if ($update_image) {
            move_uploaded_file($image_tmp_name, $image_folder . $image);

            // Delete the old image if it's not the default image
            if ($old_image !== 'default.png') {
                unlink($image_folder . $old_image);
            }

            $message[] = 'Image updated successfully!';
        }
    }
}

// ...

     

// Handle password update
$old_pass = $_POST['old_pass'];
if (!empty($_POST['update_pass']) && !empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])) {
    $update_pass = md5($_POST['update_pass']);
    $update_pass = filter_var($update_pass, FILTER_SANITIZE_STRING);
    $new_pass = md5($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $confirm_pass = md5($_POST['confirm_pass']);
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

    if ($update_pass != $old_pass) {
        $message[] = 'Old password not matched!';
    } elseif ($new_pass != $confirm_pass) {
        $message[] = 'Confirm password not matched!';
    } else {
        $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
        $update_pass_query->execute([$confirm_pass, $user_id]);
        $message[] = 'Password updated successfully!';
    }
}
    }
}

if (isset($_POST['delete_user'])) {
    // You may want to ask for confirmation before deleting the user.
    $delete_user_query = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_user_query->execute([$user_id]);

    // Perform any other cleanup, like deleting related data, if needed.

    // Redirect to a page or take appropriate action after user deletion.
    session_destroy();
    header('Location: newhome.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update user profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/components.css">
</head>
<body>
   <?php include 'header.php'; ?>

   <section class="update-profile">
      <h1 class="title">update profile</h1>

      <form action="" method="POST" enctype="multipart/form-data">
         <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
         <div class="flex">
            <div class="inputBox">
               <span>username :</span>
               <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" placeholder="update username" required class="box">
               <span>email :</span>
               <input type="email" name="email" value="<?= $fetch_profile['email']; ?>" placeholder="update email" required class="box">
               <span>update pic :</span>
               <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
               <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
               <span>phone number:</span>
               <input type="number" name="number" value="<?= $fetch_profile['number']; ?>" placeholder="update number" required class="box">
            </div>
            <div class="inputBox">
               <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
               <span>old password :</span>
               <input type="password" name="update_pass" placeholder="enter previous password" class="box">
               <span>new password :</span>
               <input type="password" name="new_pass" placeholder="enter new password" class="box">
               <span>confirm password :</span>
               <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
            </div>
         </div>
         <div class="flex-btn">
            <input type="submit" class="btn" value="update profile" name="update_profile">
            <button class="delete-btn" name="delete_user" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">Delete User</button>
            <a href="home.php" class="option-btn">go back</a>  
         </div>
      </form>
   </section>

   <?php include 'footer.php'; ?>

   <script src="js/script.js"></script>
</body>
</html>
