<?php
@include 'config.php';

session_start();

$cour_id = $_SESSION['cour_id'];

if (!isset($cour_id)) {
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

    // Check if the email is a valid email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format.';
    }

    $email_check_query = $conn->prepare("SELECT id FROM `users` WHERE email = ? AND id != ?");
    $email_check_query->execute([$email, $cour_id]);
    if ($email_check_query->rowCount() > 0) {
        $message[] = 'Email is already in use.';
    }

    // Check if the phone number is 11 digits
    if (strlen($number) !== 11) {
        $message[] = 'Phone number must have exactly 11 digits.';
    }

    if (empty($message)) {
        // Email and phone number are valid, so proceed with the update.
        $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ?, number = ? WHERE id = ?");
        $update_profile->execute([$name, $email, $number, $cour_id]);

        // Check if the username, email, or phone number was updated and show messages accordingly.
        $updatedFields = array();

        if ($update_profile->rowCount() > 0) {
            $updatedFields[] = 'Profile';
        }

        if (!empty($updatedFields)) {
            $message[] = implode(', ', $updatedFields) . ' updated successfully!';
        }
    }
}


if (isset($_POST['update_pass'])) {
    $old_pass = $_POST['old_pass'];
    $update_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if (empty($old_pass) || empty($update_pass) || empty($confirm_pass)){
    } elseif ($update_pass !== $confirm_pass) {
        $message[] = 'New and confirm passwords do not match.';
    } elseif (strlen($update_pass) < 8 || !preg_match('/\d/', $update_pass)) {
        $message[] = 'New password must be at least 8 characters long and contain at least one number.';
    } else {
        // Check if the old password matches the user's current hashed password
        $check_password_query = $conn->prepare("SELECT password FROM `users` WHERE id = ?");
        $check_password_query->execute([$cour_id]);
        $row = $check_password_query->fetch(PDO::FETCH_ASSOC);

        if (password_verify($old_pass, $row['password'])) {
            // Hash the new password before updating
            $hashed_new_pass = password_hash($update_pass, PASSWORD_DEFAULT);

            // Update the password
            $update_pass_query = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass_query->execute([$hashed_new_pass, $cour_id]);
            $message[] = 'Password updated successfully!';
        } else {
            $message[] = 'Old password not matched!';
        }
    }
}


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
        $update_image->execute([$image, $cour_id]);
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

if (isset($_POST['delete_user'])) {
    // ...

    $message[] = 'Your account has been deleted.';
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
   <title>Update Courier Profile</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/components.css">
</head>
<body>
<?php include 'courier_header.php'; ?>

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
            <input type="hidden" name="update_pass" value="<?= $fetch_profile['password']; ?>">
            <span>old password :</span>
            <input type="password" name="old_pass" placeholder="enter previous password" class="box">
            <span>new password :</span>
            <input type="password" name="new_pass" placeholder="enter new password" class="box">
            <span>confirm password :</span>
            <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
            </div>
         </div>
         <div class="flex-btn">
            <input type="submit" class="btn" value="update profile" name="update_profile">
            <button class="delete-btn" name="delete_user" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">Delete User</button>
            <a href="courier_page.php" class="option-btn">go back</a>  
         </div>
      </form>
   </section>
   
<script src="js/script.js"></script>
</body>
</html>