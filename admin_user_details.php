<?php
ob_start();
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Retrieve detailed information about the user
    $select_user_details = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $select_user_details->execute([$user_id]);
    $user_details = $select_user_details->fetch(PDO::FETCH_ASSOC);

    // Retrieve documents related to the user
    $select_documents = $conn->prepare("SELECT * FROM documents WHERE courier_id = ?");
    $select_documents->execute([$user_id]);
    $documents = $select_documents->fetchAll(PDO::FETCH_ASSOC);
}

if(isset($_GET['update'])){
    $update_id = $_GET['update'];
    
    $update_users = $conn->prepare("UPDATE `users` SET user_type = 'cour' WHERE id = ?");
    $update_users->execute([$update_id]);
    
    header('location:admin_uncouriers.php');
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_users = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_users->execute([$delete_id]);
    header('location:admin_user_details.php'); 
 }

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="icon" type="image/x-icon" href="images/title.ico">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">

    <!-- Add the following styles to your head section -->
<style>
    .box {
        /* Your existing styles for .box */
    }

    .documents-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        padding: 16px;
        background-color: #f8f9fa; /* Background color for the document container */
        border-radius: 8px; /* Rounded corners for the document container */
    }

    .document-box {
        border: 1px solid #ced4da; /* Border color for each document box */
        border-radius: 8px; /* Rounded corners for each document box */
        padding: 8px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .document-box a {
        text-decoration: none;
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .document-box a:hover {
        text-decoration: underline;
    }

</style>

</head>

<body>

    <?php include 'admin_header.php'; ?>

    <section class="user-accounts">

        <h1 class="title">User Details</h1>

        <div class="box-container">
            <?php if (isset($user_details)): ?>
                <div class="box">
                    <img src="uploaded_img/<?= $user_details['image']; ?>" alt="">
                    <p> User ID: <span><?= $user_details['id']; ?></span></p>
                    <p> Username: <span><?= $user_details['name']; ?></span></p>
                    <p> Email: <span><?= $user_details['email']; ?></span></p>
                    <p> Number: <span><?= $user_details['number']; ?></span></p>
                    <p> User Type: <span
                            style="color:<?php if ($user_details['user_type'] == 'ucour') {
                                echo 'orange';
                            }; ?>"><?= $user_details['user_type']; ?></span></p>
                    <a href="admin_user_details.php?update=<?= $user_details['id']; ?>" onclick="return confirm('Approve application?');" class="option-btn">Approve</a>
                    <a href="admin_user_details.php?delete=<?= $user_details['id']; ?>" onclick="return confirm('Delete this user?');" class="delete-btn">Delete</a>
                    <label for="documents">Documents for Verification:</label>
                    <!-- Inside the box div where you display user details -->
                        <?php if (!empty($documents)): ?>
                            <div class="documents-list">
                                <?php foreach ($documents as $document): ?>
                                    <div class="box">
                                        <a href="<?= $document['document_path']; ?>" target="_blank"><?= $document['document_name']; ?></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No documents found.</p>
                        <?php endif; ?>
                </div>
            <?php else: ?>
                <p>User details not found.</p>
            <?php endif; ?>
        </div>

    </section>

    <script src="js/script.js"></script>

</body>
</html>

