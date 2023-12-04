<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
};

if (isset($_POST['update_category'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $old_image = $_POST['old_image'];
    $new_image = $_FILES['image']['name'];

    // Select current category details
    $select_category = $conn->prepare("SELECT name FROM `categories` WHERE id = ?");
    $select_category->execute([$id]);
    $current_category = $select_category->fetch(PDO::FETCH_ASSOC);

    // Check if changes were made
    if ($current_category['name'] !== $name) {
        // Changes were made, update the category details.
        $update_category = $conn->prepare("UPDATE `categories` SET name = ? WHERE id = ?");
        $update_category->execute([$name, $id]);

        // Check if the category details were updated and show messages accordingly.
        if ($update_category->rowCount() > 0) {
            $message[] = 'Category details updated successfully!';
        }
    }

    if (!empty($new_image)) {
        // New image uploaded
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'uploaded_img/' . $new_image;

        // Check if the image size is within limits
        $max_image_size = 1000000; // 1 MB
        if ($image_size > $max_image_size) {
            $message[] = 'Image size exceeds the maximum limit of 1 MB.';
        } else {
            // Validate image file type
            $allowed_image_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
            $detected_image_type = exif_imagetype($image_tmp_name);

            if (!in_array($detected_image_type, $allowed_image_types)) {
                $message[] = 'Invalid image file type. Please upload a JPEG, PNG, or GIF image.';
            } else {
                // Update the image in the database
                $update_image = $conn->prepare("UPDATE `categories` SET image = ? WHERE id = ?");
                $update_image->execute([$new_image, $id]);

                if ($update_image) {
                    // Move the new image to the folder
                    move_uploaded_file($image_tmp_name, $image_folder);
                    // Delete the old image
                    unlink('uploaded_img/' . $old_image);
                    $message[] = 'Image updated successfully!';
                }
            }
        }
    }
}
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Category</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="update-product">

   <h1 class="title">Update Category</h1>   

   <?php
      $update_id = $_GET['update'];
      $select_categories = $conn->prepare("SELECT * FROM `categories` WHERE id = ?");
      $select_categories->execute([$update_id]);
      if($select_categories->rowCount() > 0){
         while($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_category['image']; ?>">
      <input type="hidden" name="id" value="<?= $fetch_category['id']; ?>">
      <img src="uploaded_img/<?= $fetch_category['image']; ?>" alt="">
      <input type="text" name="name" placeholder="Enter category name" required class="box" value="<?= $fetch_category['name']; ?>">
      <span>Update image : (max 1 MB)</span>
      <span>File ext. : jpg, jpeg, png</span>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <div class="flex-btn">
         <input type="submit" class="btn" value="Update Category" name="update_category">
         <a href="admin_category.php" class="option-btn">Go Back</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">No categories found!</p>';
      }
   ?>

</section>


<script src="js/script.js"></script>

</body>
</html>
