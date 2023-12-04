<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

$name = '';
$email = '';
$number = '';

// Messages
$message = [];


// Create new user
if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
    $cpass = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);
    $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_STRING);

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

            $insert = $conn->prepare("INSERT INTO `users` (name, email, password, number, image, user_type) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$name, $email, $hashedPassword, $number, $image, $user_type]);

            $message[] = 'User created successfully!';
        }
    }
}

if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_STRING);

    // Regular expression to validate a phone number with 11 digits
    $phoneNumberRegex = '/^\d{11}$/';
    
    // Validate phone number
    if (!preg_match($phoneNumberRegex, $number)) {
        $message [] = 'Phone number must have 11 digits.';
    } else {
        // Check if the phone number is unique
        $selectPhoneNumber = $conn->prepare("SELECT id FROM `users` WHERE number = ? AND id != ?");
        $selectPhoneNumber->execute([$number, $user_id]);

        if ($selectPhoneNumber->rowCount() > 0) {
            $message [] = 'Phone number is already in use.';
        } else {
            // Validate and process image update
            if (!empty($_FILES['image']['name'])) {
                $image_name = $_FILES['image']['name'];
                $image_size = $_FILES['image']['size'];
                $image_tmp = $_FILES['image']['tmp_name'];
                $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                $allowed_extensions = array('jpg', 'jpeg', 'png');

                // Check if the file extension is valid
                if (in_array($image_ext, $allowed_extensions)) {
                    // Check if the file size is within the limit (1 MB)
                    if ($image_size <= 1000000) {
                        // Generate a unique name for the image
                        $new_image_name = uniqid('user_image_', true) . '.' . $image_ext;
                        // Move the uploaded image to the desired directory
                        move_uploaded_file($image_tmp, 'uploaded_img/' . $new_image_name);

                        // Update the user with the new image
                        $update_user = $conn->prepare("UPDATE `users` SET name = ?, number = ?, user_type = ?, image = ? WHERE id = ?");
                        $update_user->execute([$name, $number, $user_type, $new_image_name, $user_id]);

                        $message [] = 'User details updated successfully!';
                    } else {
                        $message [] = 'Image size should be max 1 MB.';
                    }
                } else {
                    $message [] = 'Invalid file type. Allowed extensions: jpg, jpeg, png.';
                }
            } else {
                // Update the user without changing the image
                $update_user = $conn->prepare("UPDATE `users` SET name = ?, number = ?, user_type = ? WHERE id = ?");
                $update_user->execute([$name, $number, $user_type, $user_id]);

                $message [] = 'User details updated successfully!';
            } 
        }
    }
}// Check if the 'delete_user_id' parameter is set in the POST data
elseif (isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['delete_user'];

    // Delete user by user ID
    $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_user->execute([$user_id_to_delete]);

    $message[] = 'User deleted successfully!';
} 


ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Accounts</title>
    <link rel="icon" type="image/x-icon" href="images/title.ico">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include 'admin_header.php'; ?>

    <section class="user-accounts">

        <h1 class="title">User Accounts</h1>

        <section class="search-form">
            <form action="" method="POST">
                <input type="text" class="box" name="search_box" id="searchInput" placeholder="search users or user types...">
            </form>
        </section>

        <!-- Button to trigger modal -->
        <button id="openModalBtn" class="btn">Add New User</button>

        <!-- The Modal -->
         <div id="userModal" class="modal">
            <div class="modal-content">
               <span class="close" id="closeModalBtn">&times;</span>
               <!-- Form for creating new user -->
               <form action="" enctype="multipart/form-data" method="POST">
                     <h3>Create User</h3>
                     <input type="text" name="name" class="box" placeholder="enter name" required value="<?= $name ?>">
                     <input type="email" name="email" class="box" placeholder="enter email" required value="<?= $email ?>">
                     <input type="number" name="number" class="box" placeholder="enter number" required value="<?= $number ?>">
                     <input type="password" name="pass" class="box" placeholder="enter password" required>
                     <input type="password" name="cpass" class="box" placeholder="confirm password" required>
                     <!-- Dropdown select for user type -->
                     <label for="user_type">Choose User Type:</label>
                     <select name="user_type" id="user_type" class="box">
                        <option value="admin">Admin</option>
                        <option value="cour">Courier</option>
                        <option value="ucour">Unverified Courier</option>
                        <option value="user">User</option>
                     </select> 
                     <input type="submit" value="Create" class="btn" name="submit">
               </form>
            </div>
         </div>

         <div class="table-container">
    <table class="user-table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>User</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Action</th> 
            </tr>
        </thead>
        <tbody>
            <?php
            $select_users = $conn->prepare("SELECT * FROM `users` ORDER BY id DESC");
            $select_users->execute();
            while ($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <tr>
                    <td><?= $fetch_users['id']; ?></td>
                    <td>
                        <?php if (!empty($fetch_users['image'])) : ?>
                            <img src="uploaded_img/<?= $fetch_users['image']; ?>" alt="User Image" class="user-image">
                        <?php endif; ?>
                        <?= $fetch_users['name']; ?>
                    </td>
                    <td><?= $fetch_users['email']; ?></td>
                    <td style="color: <?php if ($fetch_users['user_type'] == 'admin') {
                                            echo 'blue';
                                        } elseif ($fetch_users['user_type'] == 'cour') {
                                            echo 'red';
                                        } elseif ($fetch_users['user_type'] == 'ucour') {
                                            echo 'orange';
                                        } elseif ($fetch_users['user_type'] == 'user') {
                                          echo 'green';
                                      }?>"><?= $fetch_users['user_type']; ?></td>
                    <td>
                        <button class="option-btn update-btn" title="Update" data-user-id="<?= $fetch_users['id']; ?>">
                            <i class="fa-solid fa-list-ul" style="color: #ffffff;"></i>
                        </button>
                        <!-- The Update Modal -->
                        <div id="updateUserModal<?= $fetch_users['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close" id="closeUpdateUserModalBtn<?= $fetch_users['id']; ?>">&times;</span>
                                <!-- Form for updating user -->
                                <form action="" id="deleteUserForm" enctype="multipart/form-data" method="POST">
                                    <h3>Update User</h3>
                                    <!-- Add fields here for updating user details, e.g., name, email, number, etc. -->
                                    <input type="hidden" name="user_id" id="updateUserId<?= $fetch_users['id']; ?>" value="<?= $fetch_users['id']; ?>">
                                    <img src="uploaded_img/<?= $fetch_users['image']; ?>" alt="User Image" class="user-image">
                                    <input type="text" name="name" class="box" placeholder="Enter updated name" value="<?= $fetch_users['name']; ?>" required>
                                    <input type="phone number" name="number" value="<?= $fetch_users['number']; ?>" placeholder="Update number" required class="box">
                                    <label for="updateUserType<?= $fetch_users['id']; ?>">Choose User Type:</label>
                                    <select name="user_type" id="updateUserType<?= $fetch_users['id']; ?>" class="box">
                                        <option value="admin" <?php echo ($fetch_users['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="cour" <?php echo ($fetch_users['user_type'] == 'cour') ? 'selected' : ''; ?>>Courier</option>
                                        <option value="ucour" <?php echo ($fetch_users['user_type'] == 'ucour') ? 'selected' : ''; ?>>Unverified Courier</option>
                                        <option value="user" <?php echo ($fetch_users['user_type'] == 'user') ? 'selected' : ''; ?>>User</option>
                                    </select>
                                    <!-- No input field for email -->
                                    <span>Update Pic: (max 1 MB)</span>
                                    <span>File ext.: jpg, jpeg, png</span>
                                    <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
                                    <!-- ... Add more fields as needed ... -->
                                    <!-- Inside the form for updating user -->
                                    <input type="submit" value="Update" class="btn" name="update_user">
                                    <input type="hidden" name="delete_user" id="deleteUserId">
                                    <button class="delete-btn" onclick="confirmDelete(<?= $fetch_users['id']; ?>)">Delete</button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <p id="noUserFoundMessage" style="display: none; color: red; font-size: large;">No user found.</p>
</div>

</section>

<script>
var modal = document.getElementById('userModal');
var openModalBtn = document.getElementById('openModalBtn');
var closeModalBtn = document.getElementById('closeModalBtn');

// Open the modal
openModalBtn.onclick = function() {
    modal.style.display = 'block';
};

// Close the modal
closeModalBtn.onclick = function() {
    modal.style.display = 'none';
};

// Close the modal if clicked outside the modal content
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};

document.addEventListener('DOMContentLoaded', function () {
   var searchInput = document.getElementById('searchInput');
   var tableRows = document.querySelectorAll('.user-table tbody tr');
   var noUserFoundMessage = document.getElementById('noUserFoundMessage');

   searchInput.addEventListener('input', function () {
       var searchTerm = searchInput.value.toLowerCase();

       var userFound = false;

       tableRows.forEach(function (row) {
           var userName = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
           var userType = row.querySelector('td:nth-child(4)').innerText.toLowerCase();

           // Show or hide the row based on whether the search term matches the user name or user type
           var matchFound = userName.includes(searchTerm) || userType.includes(searchTerm);
           row.style.display = matchFound ? '' : 'none';

           // Update the flag if at least one match is found
           if (matchFound) {
               userFound = true;
           }
       });

       // Show or hide the "No user found" message based on the flag
       noUserFoundMessage.style.display = userFound ? 'none' : 'block';
   });
});

// Update Modal
document.addEventListener('DOMContentLoaded', function () {
    // Get all update buttons
    var updateBtns = document.querySelectorAll('.update-btn');

    // Add click event listener to each update button
    updateBtns.forEach(function (updateBtn) {
        updateBtn.addEventListener('click', function () {
            // Get the user ID associated with the clicked button
            var userId = this.getAttribute('data-user-id');

            // Show the corresponding modal
            var modal = document.getElementById('updateUserModal' + userId);
            modal.style.display = 'block';

            // Get the close button for this modal
            var closeModalBtn = document.getElementById('closeUpdateUserModalBtn' + userId);

            // Add click event listener to the close button
            closeModalBtn.addEventListener('click', function () {
                // Hide the modal when the close button is clicked
                modal.style.display = 'none';
            });
        });
    });

    // Add click event listener to delete buttons
    var deleteBtns = document.querySelectorAll('.delete-btn');

    deleteBtns.forEach(function (deleteBtn) {
        deleteBtn.addEventListener('click', function () {
            var confirmDelete = confirm('Are you sure you want to delete this account? This action cannot be undone.');

            if (confirmDelete) {
                // Trigger the form submission for user deletion
                this.closest('form').submit();
            }
        });
    });
});

function confirmDelete(userId) {
    var confirmDelete = confirm('Are you sure you want to delete this account? This action cannot be undone.');

    if (confirmDelete) {
        // Set the user ID to the delete_user input field
        document.getElementById('deleteUserId').value = userId;
        // Submit the form
        document.getElementById('deleteUserForm').submit();
    }
}


</script>

<script src="js/script.js"></script>

</body>

</html>
