<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

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

// Check if delete parameter is set in the URL
if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];

    // Select the category information before deleting
    $select_category = $conn->prepare("SELECT * FROM `categories` WHERE id = ?");
    $select_category->execute([$delete_id]);
    $fetch_category = $select_category->fetch(PDO::FETCH_ASSOC);

    // Delete the category
    $delete_category = $conn->prepare("DELETE FROM `categories` WHERE id = ?");
    $delete_category->execute([$delete_id]);

    // Remove the category image file
    unlink('images/'.$fetch_category['image']);

    header('location:admin_category.php');
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Categories</title>
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


   <h1 class="title">Add New Category</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="flex">
                <div class="inputBox">
                    <input type="text" name="category_name" class="box" required placeholder="Enter category name">
                </div>
                <div class="inputBox">
                    <input type="file" name="category_image" required class="box" accept="image/jpg, image/jpeg, image/png">
                </div>
            </div>
            <input type="submit" class="btn" value="Add Category" name="add_category">
        </form>

</section>

<section class="show-products">

<h1 class="title">Categories Added</h1>

<section class="search-form">
    <form action="" method="POST">
        <input type="text" class="box" name="search_box" id="searchInput" placeholder="search categories..." onclick="focusSearch()">
    </form>
</section>

<div id="noCategoryMessage" style="display: none; color: red; font-size: large;">No category found!</div>


<div class="box-container">
    <?php
    $select_categories = $conn->prepare("SELECT * FROM `categories`");
    $select_categories->execute();
    if ($select_categories->rowCount() > 0) {
        while ($fetch_categories = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box">
                <img src="images/<?= $fetch_categories['image']; ?>" alt="">
                <div class="name"><?= $fetch_categories['name']; ?></div>
                <div class="flex-btn">
                    <a href="admin_update_category.php?update=<?= $fetch_categories['id']; ?>" class="option-btn">update</a>
                    <a href="admin_category.php?delete=<?= $fetch_categories['id']; ?>" class="delete-btn" onclick="return confirm('delete this category?');">delete</a>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="empty">No categories added yet!</p>';
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

            // Show or hide the category box based on whether the search term matches the category name
            var matchFound = categoryName.includes(searchTerm);
            box.style.display = matchFound ? '' : 'none';

            if (matchFound) {
                anyMatchFound = true;
            }
        });

        // Display or hide the "No category found" message
        noCategoryMessage.style.display = anyMatchFound ? 'none' : 'block';
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