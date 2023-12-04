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

            header('Location: admin_totalaccounts.php');
            exit;
        }
    }
}

// Check if the 'delete' parameter is set in the URL
if (isset($_GET['delete'])) {
   $user_id_to_delete = $_GET['delete'];

   // Delete user by user ID
   $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
   $delete_user->execute([$user_id_to_delete]);

   header('Location: admin_totalaccounts.php');
   exit;
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
                <input type="text" class="box" name="search_box" id="searchInput" placeholder="search users...">
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
                     <input type="text" name="name" class="box" placeholder="enter name" required>
                     <input type="email" name="email" class="box" placeholder="enter email" required>
                     <input type="number" name="number" class="box" placeholder="enter number" required>
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
                    <td><a href="admin_totalaccounts.php?delete=<?= $fetch_users['id']; ?>" onclick="return confirm('Delete this user?');" class="delete-btn">&times;</a></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <p id="noUserFoundMessage" style="display: none; color: red; font-size: large;">No user found.</p>
</div>

    </section>

<style>
/* ... Existing CSS ... */

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}


.modal-content form {
    width: 50rem;
    background-color: var(--white);
    border-radius: .5rem;
    box-shadow: var(--box-shadow);
    border: var(--border);
    text-align: center;
    padding: 2rem;
}

.modal-content form h3 {
    font-size: 3rem;
    color: var(--black);
    margin-bottom: 1rem;
    text-transform: uppercase;
}

.modal-content form .box {
    width: 100%;
    margin: 1rem 0;
    border-radius: .5rem;
    border: var(--border);
    padding: 1.2rem 1.4rem;
    font-size: 1.8rem;
    color: var(--black);
    background-color: var(--light-bg);
}

.modal-content form p {
    margin-top: 2rem;
    font-size: 2.2rem;
    color: var(--light-color);
}

.modal-content form p a {
    color: var(--green);
}

.modal-content form p a:hover {
    text-decoration: underline;
}

/* Table styles */
.user-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.user-table th,
.user-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 16px; /* Adjust the font size as needed */
}

.user-table th {
    background-color: #f2f2f2;
}

.user-table tbody tr:hover {
    background-color: #f5f5f5;
}

.user-table img.user-image {
    width: 40px; /* Set the width of the circular image */
    height: 40px; /* Set the height of the circular image */
    object-fit: cover; /* Make the image cover the circle */
    border-radius: 50%; /* Create a circular shape */
    margin-right: 10px; /* Adjust the spacing between the image and text */
    vertical-align: middle;
}

/* Responsive styles using media queries */
@media (max-width: 768px) {
    /* Adjust the modal width for smaller screens */
    .modal-content {
        width: 90%;
    }

    /* Adjust font size for better readability on smaller screens */
    .modal-content form h3 {
        font-size: 2rem;
    }

    .modal-content form .box {
        font-size: 1.6rem;
    }

    .user-table th,
    .user-table td {
        font-size: 14px;
    }

    /* Ensure images in the table are responsive */
    .user-table img.user-image {
        width: 30px;
        height: 30px;
    }
   /* Adjusted modal width for smaller screens */
   .modal-content {
      background-color: #fefefe;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 90%; /* Adjusted width for smaller screens */
   }

   /* Form styles */
   .modal-content form {
      width: 100%; /* Full width on small screens */
      max-width: 375px; /* Max width for small screens */
      margin: 0 auto; /* Center the form on small screens */
   }

   /* Adjusted font size for better readability on smaller screens */
   .modal-content form h3 {
      font-size: 2rem;
   }

   .modal-content form .box {
      font-size: 1.6rem;
   }
}

/* Additional responsive styles for screens with max-width: 450px */
@media (max-width: 450px) {
    .modal-content {
        width: 95%;
    }

    .user-table th,
    .user-table td {
        font-size: 10px;
    }

    .user-table img.user-image {
        width: 25px;
        height: 25px;
    }
}

</style>

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

</script>

<script src="js/script.js"></script>

</body>

</html>
