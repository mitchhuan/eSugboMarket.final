<?php
ob_start();
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$selectedOrderId = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$messages = array();

// Fetch couriers related to the orders made by the current user from the 'messages' table
$users = array();
$queryUsers = $conn->prepare("SELECT DISTINCT m.sender_id AS cour_id, m.order_id, u.name, u.image
                            FROM messages m
                            INNER JOIN users u ON m.sender_id = u.id
                            WHERE m.receiver_id = ? AND m.sender_id != ?
                            ORDER BY m.timestamp DESC");
$queryUsers->execute([$user_id, $user_id]);
$users = $queryUsers->fetchAll(PDO::FETCH_ASSOC);

// Fetch the selected user's image URL from the database
$userImageQuery = $conn->prepare("SELECT image FROM users WHERE id = ?");
$userImageQuery->execute([$selectedUserId]);
$userImageData = $userImageQuery->fetch(PDO::FETCH_ASSOC);
$selectedUserImage = 'uploaded_img/' . ($userImageData['image'] ?? 'default.png'); // Assuming 'image' is the column in the users table containing the image file name

// Function to get the count of unread messages from a specific sender
function getUnreadMessageCount($conn, $currentUserId, $senderId)
{
    $query = $conn->prepare("SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = ? AND sender_id = ? AND is_read = 0");
    $query->execute([$currentUserId, $senderId]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['unread_count'];
}

$currentUserId = $_SESSION['user_id']; // Assuming you have a session for the current user
$queryRecentUsers = $conn->prepare("SELECT u.id, u.name, u.image, MAX(m.timestamp) as latest_message_time
FROM users u
LEFT JOIN messages m ON (u.id = m.sender_id AND m.receiver_id = :currentUserId)
WHERE u.id != :currentUserId
GROUP BY u.id, u.name, u.image
ORDER BY latest_message_time DESC");
$queryRecentUsers->execute(['currentUserId' => $_SESSION['user_id']]);
$recentUsers = $queryRecentUsers->fetchAll(PDO::FETCH_ASSOC);

if ($selectedUserId && $selectedOrderId) {
    // Fetch the selected user's name from the database
    $userQuery = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $userQuery->execute([$selectedUserId]);
    $userData = $userQuery->fetch(PDO::FETCH_ASSOC);

    // Assign the name to the $selectedUserName variable
    $selectedUserName = $userData['name'];

    // Fetch messages for the selected user and order from the 'messages' table
    $query = $conn->prepare("SELECT sender_id, receiver_id, order_id, message_content, timestamp FROM messages WHERE
        (receiver_id = ? AND sender_id = ?) AND order_id = ?
        ORDER BY timestamp ASC");
    $query->execute([$user_id, $selectedUserId, $selectedOrderId]);
    $messages = $query->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read in the database
    $markAsReadQuery = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND order_id = ?");
    $markAsReadQuery->execute([$selectedUserId, $user_id, $selectedOrderId]);
}


if (isset($_POST['send_message'])) {
    $messageText = $_POST['message'];
    $senderUserId = $_SESSION['user_id'];

    // Insert the new message into the 'messages' table
    $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, order_id, message_content, timestamp, is_read, is_deleted)
        VALUES (?, ?, ?, ?, NOW(), 0, 0)");
    $insertMessageQuery->execute([$senderUserId, $selectedUserId, $selectedOrderId, $messageText]);

    // Redirect to the same page with the selected user and order
    header("Location: contact.php?user_id=$selectedUserId&order_id=$selectedOrderId");
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
    <title>Messages</title>
    <link rel="icon" type="image/x-icon" href="images/title.ico">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'header.php'; ?>


    <section class="messages">

        <h1 class="title">Messages</h1>

        <div class="message-container">
        <div class="users">
                    <h1>Users</h1>
                    <div class="search-bar">
                        <input type="text" id="user-search" placeholder="Search Users">
                    </div>
                    <ul class="user-list">
                        <?php foreach ($users as $user) : ?>
                            <li <?php if (getUnreadMessageCount($conn, $currentUserId, $user['cour_id']) > 0) : ?> class="unread" <?php endif; ?>>
                                <a href="contact.php?user_id=<?= $user['cour_id'] ?>&order_id=<?= $user['order_id'] ?>">
                                    <div class="user-box">
                                        <img src="uploaded_img/<?= $user['image'] ?>" alt="<?= $user['name'] ?>'s Image">
                                        <span><?= $user['name'] ?></span>
                                        <?php $unreadCount = getUnreadMessageCount($conn, $currentUserId, $user['cour_id']); ?>
                                        <?php if ($unreadCount > 0) : ?>
                                            <span class="unread-tally">(<?= $unreadCount ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <div class="messages">
                <!-- Header for the selected user's profile -->
                <div class="user-profile">
                    <!-- You can display the selected user's image here -->
                    <div class="user-image">
                        <img src="<?= $selectedUserImage ?>" alt="User Image">
                    </div>
                    <div class="user-name">
                        <?php
                        if ($selectedUserId && $selectedOrderId) {
                            // Fetch the selected user's name from the database
                            $userQuery = $conn->prepare("SELECT name FROM users WHERE id = ?");
                            $userQuery->execute([$selectedUserId]);
                            $userData = $userQuery->fetch(PDO::FETCH_ASSOC);

                            // Assign the name to the $selectedUserName variable
                            $selectedUserName = $userData['name'];

                            // Fetch messages for the selected user from the 'messages' table
                            $query = $conn->prepare("SELECT sender_id, receiver_id, order_id, message_content, timestamp FROM messages WHERE
                                (sender_id = ? AND receiver_id = ?) OR
                                (sender_id = ? AND receiver_id = ?)
                                ORDER BY timestamp ASC");
                            $query->execute([$selectedUserId, $_SESSION['user_id'], $_SESSION['user_id'], $selectedUserId]);
                            $messages = $query->fetchAll(PDO::FETCH_ASSOC);
                        }
                        // Display the selected user's name
                        if ($selectedUserId) {
                            // Fetch the selected user's name from your database
                            echo $selectedUserName;
                        }
                        ?>
                        (<span>Order No. <?= $selectedOrderId ?></span>)
                    </div>
                </div>
                <div class="message-display">
                    <div class="message-bar"></div>
                    <?php

                    
                    // Fetch messages for the selected user and order from the 'messages' table
                    $query = $conn->prepare("SELECT sender_id, receiver_id, order_id, message_content, timestamp FROM messages WHERE
                        ((receiver_id = ? AND sender_id = ?) OR (receiver_id = ? AND sender_id = ?)) AND order_id = ?
                        ORDER BY timestamp ASC");
                    $query->execute([$user_id, $selectedUserId, $selectedUserId, $user_id, $selectedOrderId]);
                    $messages = $query->fetchAll(PDO::FETCH_ASSOC);

                        // Sort messages by timestamp in descending order (newest first)
                        usort($messages, function ($a, $b) {
                            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                        });

                        date_default_timezone_set('Asia/Manila'); // Set the timezone to GMT+8 (Philippines timezone)

                    // Display the messages
                    foreach ($messages as $message) {
                        $senderId = $message['sender_id'];
                        $timestamp = date("M j, Y H:i", strtotime($message['timestamp']));
                        $messageClass = $senderId == $_SESSION['user_id'] ? 'sent' : '';

                        // Fetch the sender's name for this message
                        $userQuery = $conn->prepare("SELECT name FROM users WHERE id = ?");
                        $userQuery->execute([$senderId]);
                        $userData = $userQuery->fetch(PDO::FETCH_ASSOC);
                        $sender = $userData['name'];
                        ?>

                        <div class="message-item <?= $messageClass ?>">
                            <div class="message-header">
                                <div class="user-info">
                                    <span class="user-names"><?= $sender ?></span>
                                    <span class="message-timestamp"><?= $timestamp ?></span>
                                </div>
                            </div>
                            <p><?= $message['message_content'] ?></p>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="message-box">
                    <form method="post" class="text">
                        <input type="text" name="message" placeholder="Type your message" required>
                        <input type="hidden" name="order_id" value="<?= $selectedOrderId ?>"> <!-- Added this line -->
                        <input type="hidden" name="send_message" value="1">
                        <button type="submit"><i class="fa-regular fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>

    </section>

    <script>
        // JavaScript to filter users
        document.getElementById('user-search').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-list li');

            userItems.forEach(function (user) {
                const userName = user.textContent.toLowerCase();
                if (userName.includes(searchTerm)) {
                    user.style.display = 'block';
                } else {
                    user.style.display = 'none';
                }
            });
        });
    </script>

    <?php include 'footer.php'; ?>

    <script src="js/script.js"></script>

</body>

</html>
