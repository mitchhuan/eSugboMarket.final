<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
};

if (isset($_POST['update_product'])) {

    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);
    $details = $_POST['details'];
    $details = filter_var($details, FILTER_SANITIZE_STRING);

    $old_image = $_POST['old_image'];
    $new_image = $_FILES['image']['name'];

    // Select current product details
    $select_product = $conn->prepare("SELECT name, category, details, price FROM `products` WHERE id = ?");
    $select_product->execute([$pid]);
    $current_product = $select_product->fetch(PDO::FETCH_ASSOC);

    // Check if changes were made
    if (
        $current_product['name'] !== $name ||
        $current_product['category'] !== $category ||
        $current_product['details'] !== $details ||
        $current_product['price'] !== $price
    ) {
        // Changes were made, update the product details.
        $update_product = $conn->prepare("UPDATE `products` SET name = ?, category = ?, details = ?, price = ? WHERE id = ?");
        $update_product->execute([$name, $category, $details, $price, $pid]);

        // Check if the product details were updated and show messages accordingly.
        $updatedFields = array();

        if ($update_product->rowCount() > 0) {
            $updatedFields[] = 'Product details';
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
                    $update_image = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
                    $update_image->execute([$new_image, $pid]);

                    if ($update_image) {
                     move_uploaded_file($image_tmp_name, $image_folder);
         
                     // Delete the old image if it exists and it's not the default image
                     if ($old_image !== 'default.png' && file_exists($image_folder . $old_image)) {
                         unlink($image_folder . $old_image);
                     }
         
                     $message[] = 'Image updated successfully!';
                 }
                }
            }
        }

        if (!empty($updatedFields)) {
            $message[] = implode(', ', $updatedFields) . ' updated successfully!';
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
   <title>Update Products</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="update-product">

   <h1 class="title">update product</h1>   

   <?php
      $update_id = $_GET['update'];
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?= $fetch_products['image']; ?>">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <input type="text" name="name" placeholder="enter product name" required class="box" value="<?= $fetch_products['name']; ?>">
      <input type="number" name="price" min="1" placeholder="enter product price" required class="box" value="<?= $fetch_products['price']; ?>">
      <select name="category" class="box" required>
         <option selected><?= $fetch_products['category']; ?></option>
               <option value="fruits and vegetables">fruits and vegetables</option>
               <option value="poultry and meat">poultry and meat</option>
               <option value="dry goods and grains">dry goods and grains</option>
               <option value="fresh seafood">fresh seafood</option>
               <option value="spices and condiments">spices and condiments</option>
               <option value="local snacks and street food">local snacks and street food</option>
               <option value="clothing and apparel">clothing and apparel</option>
               <option value="footwear and accessories">footwear and accessories</option>
               <option value="handicrafts and souvenir">handicrafts and souvenir</option>
               <option value="kitchen stuff ">kitchen stuff</option>
      </select>
      <textarea name="details" required placeholder="enter product details" class="box" cols="30" rows="10"><?= $fetch_products['details']; ?></textarea>
      <span>update pic : (max 1 MB)</span>
      <span>file ext. : jpg, jpeg, png</span>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <div class="flex-btn">
         <input type="submit" class="btn" value="update product" name="update_product">
         <a href="admin_products.php" class="option-btn">go back</a>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">no products found!</p>';
      }
   ?>

</section>













<script src="js/script.js"></script>

</body>
</html>