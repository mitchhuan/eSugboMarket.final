<?php
ob_start();
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

    $phone_check_query = $conn->prepare("SELECT id FROM `users` WHERE number = ? AND id != ?");
    $phone_check_query->execute([$number, $cour_id]);
    if ($phone_check_query->rowCount() > 0) {
        $message[] = 'Phone is already in use.';
    }

    if (empty($message)) {
        // Check if any changes have been made before updating
        $check_profile_query = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $check_profile_query->execute([$cour_id]);
        $current_profile = $check_profile_query->fetch(PDO::FETCH_ASSOC);

        if ($current_profile['name'] != $name || $current_profile['email'] != $email || $current_profile['number'] != $number) {
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
        }else {
            // No changes were made.
            $message[] = 'No changes.';
        }
    }
}

if (isset($_POST['update_pass'])) {
    $old_pass = $_POST['old_pass'];
    $update_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if (empty($old_pass) || empty($update_pass) || empty($confirm_pass)) {
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
    $user_id = $_SESSION['cour_id']; // Assuming you store user_id in the session

    // Perform the deletion from your database, for example:
    $delete_user = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_user->execute([$user_id]);

    // Optionally, you might want to delete associated data in other tables, if any.

    // Display a message or log the action
    $message[] = 'Your account has been deleted.';

    // Destroy the session and redirect to a new page
    session_destroy();
    header('Location: newhome.php');
    exit;
}

// Function to delete a document
if (isset($_GET['delete_document'])) {
    $document_id = $_GET['delete_document'];

    // Ensure the document belongs to the current courier
    $check_document = $conn->prepare("SELECT courier_id FROM documents WHERE id = ?");
    $check_document->execute([$document_id]);
    $document_courier_id = $check_document->fetchColumn();

    if ($document_courier_id == $cour_id) {
        // Get document information
        $get_document_info = $conn->prepare("SELECT document_name FROM documents WHERE id = ?");
        $get_document_info->execute([$document_id]);
        $document_info = $get_document_info->fetch(PDO::FETCH_ASSOC);
        $document_name = $document_info['document_name'];

        // Delete the document from the database
        $delete_document_query = $conn->prepare("DELETE FROM documents WHERE id = ?");
        $delete_document_query->execute([$document_id]);

        if ($delete_document_query) {
            // Delete the document file from the server
            unlink('document_uploads/' . $document_name);

            // Display a success message
            $message[] = 'Document deleted successfully!';
        } else {
            // Display an error message
            $message[] = 'Error deleting document. Please try again.';
        }
    }
}

if (isset($_POST['submit_document'])) {
    $documents = $_FILES['documents'];
    $num_files = count($documents['name']);

    // Check if any files are submitted
    $filesSubmitted = false;
    foreach ($documents['name'] as $documentName) {
        if (!empty($documentName)) {
            $filesSubmitted = true;
            break;
        }
    }

    if ($filesSubmitted) {
        for ($i = 0; $i < $num_files; $i++) {
            $document_name = $documents['name'][$i];

            // Check if the document_name is not empty, indicating a file is submitted
            if (!empty($document_name)) {
                $document_name = filter_var($document_name, FILTER_SANITIZE_STRING);
                $document_size = $documents['size'][$i];
                $document_tmp_name = $documents['tmp_name'][$i];

                // Check if the document size is within limits
                if ($document_size > 1000000) {
                    $message[] = 'Document size is too large!';
                } else {
                    // Generate a unique filename
                    $info = pathinfo($document_name);
                    $basename = $info['filename'];
                    $extension = $info['extension'];
                    $counter = 1;

                    while (file_exists('document_uploads/' . $document_name)) {
                        $document_name = $basename . '_' . $counter . '.' . $extension;
                        $counter++;
                    }

                    // Move the uploaded document to the server
                    move_uploaded_file($document_tmp_name, 'document_uploads/' . $document_name);

                    // Add the document to the database
                    $insert_document_query = $conn->prepare("INSERT INTO documents (courier_id, document_name, document_path) VALUES (?, ?, ?)");
                    $insert_document_query->execute([$cour_id, $document_name, 'document_uploads/' . $document_name]);

                    if ($insert_document_query) {
                        $message[] = 'Document added successfully!';
                    } else {
                        $message[] = 'Error adding document. Please try again.';
                    }
                }
            }
        }
    } else {
        $message[] = 'No files submitted for upload.';
    }
}

// Retrieve documents related to the user
$select_documents = $conn->prepare("SELECT * FROM documents WHERE courier_id = ?");
$select_documents->execute([$cour_id]);
$documents = $select_documents->fetchAll(PDO::FETCH_ASSOC);
ob_end_flush();
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
               <input type="phone number" name="number" value="<?= $fetch_profile['number']; ?>" placeholder="update number" required class="box">
               
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
            <div class="inputBox">
            <span>files:</span>
                     <?php foreach ($documents as $document): ?>
                    <div class="box">
                        <a href="<?= $document['document_path']; ?>" target="_blank" style="word-wrap: break-word;"><?= $document['document_name']; ?></a>
                        <a href="?delete_document=<?= $document['id']; ?>" onclick="return confirm('Are you sure you want to delete this document?')" style="color: red;">(Delete)</a>
                    </div>
                    <?php endforeach; ?>
                    <input type="file" name="documents[]" accept="application/pdf" class="box" multiple>
                    <input type="submit" class="btn" value="add files" name="submit_document">
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
