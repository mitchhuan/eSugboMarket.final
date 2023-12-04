<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if (isset($_POST['add_product'])) {
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);
   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_STRING);
   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
       $message[] = 'Product name already exists!';
   } else {
       // Validate image file type
       $allowed_image_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
       $detected_image_type = exif_imagetype($image_tmp_name);

       if (!in_array($detected_image_type, $allowed_image_types)) {
           $message[] = 'Invalid image file type. Please upload a JPEG, PNG, or GIF image.';
       } else {
           $insert_products = $conn->prepare("INSERT INTO `products`(name, category, details, price, image) VALUES(?,?,?,?,?)");
           $insert_products->execute([$name, $category, $details, $price, $image]);

           if ($insert_products) {
               if ($image_size > 1000000) {
                   $message[] = 'Image size is too large!';
               } else {
                   move_uploaded_file($image_tmp_name, $image_folder);
                   $message[] = 'New product added!';
               }
           }
       }
   }
}

if (isset($_POST['add_category'])) {
   $category_name = $_POST['category_name'];
   $category_name = filter_var($category_name, FILTER_SANITIZE_STRING);

   $category_image = $_FILES['category_image']['name'];
   $category_image = filter_var($category_image, FILTER_SANITIZE_STRING);
   $category_image_size = $_FILES['category_image']['size'];
   $category_image_tmp_name = $_FILES['category_image']['tmp_name'];
   $category_image_folder = 'images/' . $category_image;

   $select_categories = $conn->prepare("SELECT * FROM `categories` WHERE name = ?");
   $select_categories->execute([$category_name]);

   if ($select_categories->rowCount() > 0) {
       $message[] = 'Category name already exists!';
   } else {
       // Validate image file type
       $allowed_image_types = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
       $detected_image_type = exif_imagetype($category_image_tmp_name);

       if (!in_array($detected_image_type, $allowed_image_types)) {
           $message[] = 'Invalid image file type. Please upload a JPEG, PNG, or GIF image.';
       } else {
           $insert_category = $conn->prepare("INSERT INTO `categories` (name, image) VALUES (?, ?)");
           $insert_category->execute([$category_name, $category_image]);

           if ($insert_category) {
               if ($category_image_size > 5000000) {
                   $message[] = 'Image size is too large!';
               } else {
                   move_uploaded_file($category_image_tmp_name, $category_image_folder);
                   $message[] = 'New category added!';
               }
           }
       }
   }
}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $select_delete_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
   $select_delete_image->execute([$delete_id]);
   $fetch_delete_image = $select_delete_image->fetch(PDO::FETCH_ASSOC);
   unlink('uploaded_img/'.$fetch_delete_image['image']);
   $delete_products = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);
   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);
   header('location:admin_products.php');


}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>
   <link rel="icon" type="image/x-icon" href="images/title.ico">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">add new product</h1>

   <form action="" method="POST" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
         <input type="text" name="name" class="box" required placeholder="enter product name">
         <select name="category" class="box" required>
                <option value="" selected disabled>select category</option>
                <?php
                $select_category_names = $conn->prepare("SELECT name FROM `categories`");
                $select_category_names->execute();
                
                while ($category_name = $select_category_names->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . htmlspecialchars($category_name['name']) . '">' . htmlspecialchars($category_name['name']) . '</option>';
                }
                ?>
        </select>
         </div>
         <div class="inputBox">
         <input type="number" min="1" name="price" class="box" required placeholder="enter product price">
         <input type="file" name="image" required class="box" accept="image/jpg, image/jpeg, image/png">
         </div>
      </div>
      <textarea name="details" class="box" required placeholder="enter product details" cols="30" rows="10"></textarea>
      <input type="submit" class="btn" value="add product" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="title">products added</h1>

    <section class="search-form">
    <form action="" method="POST">
        <input type="text" class="box" name="search_box" id="searchInput" placeholder="search products..." onclick="focusSearch()">
    </form>
    </section>

   <div id="noProductMessage" style="display: none; color: red; font-size: large;">No product found!</div>

   <div class="box-container">


   <?php
    $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
    $select_products->execute();
    if($select_products->rowCount() > 0){
        while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <div class="price">₱<?= $fetch_products['price']; ?></div>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="cat"><?= $fetch_products['category']; ?></div>
      <div class="details"><?= $fetch_products['details']; ?></div>
      <div class="flex-btn">
         <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
         <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">now products added yet!</p>';
   }
   ?>

   </div>

</section>


<script>
function focusSearch() {
    var searchInput = document.getElementById('searchInput');
    searchInput.focus();
}

document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('searchInput');
    var categoryBoxes = document.querySelectorAll('.box-container .box');
    var noCategoryMessage = document.getElementById('noCategoryMessage');

    searchInput.addEventListener('input', function () {
        var searchTerm = searchInput.value.toLowerCase();
        var anyMatchFound = false;

        categoryBoxes.forEach(function (box) {
            var categoryName = box.querySelector('.name').innerText.toLowerCase();

            // Show or hide the product box based on whether the search term matches the product name
            var matchFound = categoryName.includes(searchTerm);
            box.style.display = matchFound ? '' : 'none';

            if (matchFound) {
                anyMatchFound = true;
            }
        });

        // Display or hide the "No product found" message
        noProductMessage.style.display = anyMatchFound ? 'none' : 'block';
    });

    // Set focus on the search input when the page loads
    focusSearch();

    // Smooth scroll to the search input when it is clicked
    searchInput.addEventListener('click', function () {
        focusSearch();
        searchInput.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>




<script src="js/script.js"></script>

</body>
</html>