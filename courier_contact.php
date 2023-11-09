<?php
// Include the configuration file and start the session
@include 'config.php';
session_start();

// Get courier ID from the session
$cour_id = $_SESSION['cour_id'];

// Redirect to login if courier ID is not set
if (!isset($cour_id)) {
    header('location: login.php');
    exit;
}

// Initialize variables
$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null; // Added this line
$messages = array();
$orderNumber = $paymentMethod = $deliveryAddress = $totalProducts = $timeOfOrder = "";
$user_id = null; // Initialize $user_id

// Fetch messages for the selected user from the 'messages' table
if ($selectedUserId && $order_id) { // Updated this line
    $query = $conn->prepare("SELECT sender_id, receiver_id, order_id, message_content, timestamp FROM messages WHERE
        (sender_id = ? AND receiver_id = ? AND order_id = ?) OR
        (sender_id = ? AND receiver_id = ? AND order_id = ?)
        ORDER BY timestamp ASC");
    $query->execute([$selectedUserId, $cour_id, $order_id, $cour_id, $selectedUserId, $order_id]);
    $messages = $query->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read in the database
    $markAsReadQuery = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND order_id = ?");
    $markAsReadQuery->execute([$selectedUserId, $cour_id, $order_id]);

    // Fetch user ID for the selected user
    $userQuery = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $userQuery->execute([$selectedUserId]);
    $userData = $userQuery->fetch(PDO::FETCH_ASSOC);

    // Assign the user ID to $user_id variable
    $user_id = $userData['id'];
}

// Handle sending a message
if (isset($_POST['send_message'])) {
    $messageText = $_POST['message'];
    $order_id = $_POST['order_id']; // Added this line
    $senderUserId = $cour_id;

    // Insert the new message into the 'messages' table
    $insertMessageQuery = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, order_id, message_content, timestamp, is_read, is_deleted)
        VALUES (?, ?, ?, ?, NOW(), 0, 0)");
    $insertMessageQuery->execute([$senderUserId, $selectedUserId, $order_id, $messageText]);

    // Redirect to the same page with the selected user and order
    header("Location: courier_contact.php?user_id=$selectedUserId&order_id=$order_id");
    exit;
}

// Fetch the order details and user associated with this order
if ($order_id) {
    try {
        $orderQuery = $conn->prepare("SELECT * FROM `orders` WHERE order_id = ?");
        $orderQuery->execute([$order_id]);
        $orderData = $orderQuery->fetch(PDO::FETCH_ASSOC);

        if ($orderData) {
            $user_id = $orderData['user_id'];
            $transaction_id = $orderData['transaction_id'];
            $orderNumber = $orderData['number'];
            $paymentMethod = $orderData['method'];
            $deliveryAddress = $orderData['address'];
            $totalProducts = $orderData['total_products'];
            $timeOfOrder = $orderData['time_of_order'];
            $status= $orderData['payment_status'];
        } else {
            // Handle the case where the order is not found, or other error handling
            // You can redirect or show an error message here.
            echo "Order not found or other error occurred.";
        }
    } catch (PDOException $e) {
        // Handle the exception (display an error message or redirect)
        echo "Error fetching order details: " . $e->getMessage();
    }
} else {
    // Handle the case where order_id is not set
    echo "Order ID is not set. Please make sure you are accessing this page from the correct link.";
    // Optionally, you can redirect the user or provide a link to go back to the orders page
}

// Fetch the selected user's image URL from the database
$userImage = null;
if ($selectedUserId) {
    $userImageQuery = $conn->prepare("SELECT image FROM users WHERE id = ?");
    $userImageQuery->execute([$selectedUserId]);
    $userImageData = $userImageQuery->fetch(PDO::FETCH_ASSOC);

    if ($userImageData) {
        $userImage = 'uploaded_img/' . $userImageData['image'];
    }
}

// Function to get the count of unread messages from a specific sender
function getUnreadMessageCount($conn, $currentUserId, $senderId, $order_id)
{
    $query = $conn->prepare("SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = ? AND sender_id = ? AND is_read = 0 AND order_id = ?");
    $query->execute([$currentUserId, $senderId, $order_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['unread_count'];
}

$currentUserId = $cour_id;
$queryRecentUsers = $conn->prepare("SELECT u.id, u.name, u.image, MAX(m.timestamp) as latest_message_time
FROM users u
LEFT JOIN messages m ON (u.id = m.sender_id AND m.receiver_id = :currentUserId)
WHERE u.id = :user_id
GROUP BY u.id, u.name, u.image
ORDER BY latest_message_time DESC");
$queryRecentUsers->execute(['currentUserId' => $cour_id, 'user_id' => $user_id]);
$recentUsers = $queryRecentUsers->fetchAll(PDO::FETCH_ASSOC);

// If no user is selected and there are recent users, set the selected user to the most recent
if (!$selectedUserId && !empty($recentUsers)) {
    $selectedUserId = $recentUsers[0]['id'];
    // You can redirect to this user's messages immediately if needed
    header("Location: courier_contact.php?user_id=$selectedUserId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE, edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Messages</title>
    <link rel="icon" type="image/x-icon" href="images/title.ico">

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'courier_header.php'; ?>

    <section class="messages">
        <h1 class="title">Order Messages</h1>

        <div class="message-container">
            <div class="users">
                <h1>Order Details</h1>
                <div class="order-details" style="font-size :small ; word-wrap: break-word;">
                    <p><strong>Order ID:</strong> <?php echo $transaction_id; ?></p>
                    <p><strong>Phone Number:</strong> <?php echo $orderNumber; ?></p>
                    <p><strong>Payment Method:</strong> <?php echo $paymentMethod; ?></p>
                    <p><strong>Delivery Address:</strong> <?php echo $deliveryAddress; ?></p>
                    <p><strong>Total Products:</strong> <?php echo $totalProducts; ?></p>
                    <p><strong>Time of Order:</strong> <?php echo $timeOfOrder; ?></p>
                    <p><strong>Status:</strong> <?php echo $status; ?></p>
                </div>
            </div>
            <div class="messages">
                <!-- Header for the selected user's profile -->
                <div class="user-profile">
                    <!-- You can display the selected user's image here -->
                    <div class="user-image">
                        <?php if ($userImage) : ?>
                            <img src="<?= $userImage ?>" alt="User Image">
                        <?php endif; ?>
                    </div>
                    <div class="user-name">
                        <?php
                        if ($selectedUserId) {
                            // Fetch the selected user's name from the database
                            $userQuery = $conn->prepare("SELECT name FROM users WHERE id = ?");
                            $userQuery->execute([$selectedUserId]);
                            $userData = $userQuery->fetch(PDO::FETCH_ASSOC);

                            // Assign the name to the $selectedUserName variable
                            $selectedUserName = $userData['name'];
                        }
                        // Display the selected user's name
                        if ($selectedUserId) {
                            // Fetch the selected user's name from your database
                            echo $selectedUserName;
                        }
                        ?>
                    </div>
                </div>
                <div class="message-display">
                    <div class="message-bar"></div>
                    <?php
                    // Sort messages by timestamp in descending order (newest first)
                    usort($messages, function ($a, $b) {
                        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                    });

                    date_default_timezone_set('Asia/Manila'); // Set the timezone to GMT+8 (Philippines timezone)
                    if ($selectedUserId) {
                        foreach ($messages as $message) {
                            $senderId = $message['sender_id'];
                            $timestamp = date("M j, Y H:i", strtotime($message['timestamp']));
                            $messageClass = $senderId == $cour_id ? 'sent' : '';

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
                    } else {
                        ?>
                        <p>Select a user to start a conversation.</p>
                    <?php
                    }
                    ?>
                </div>
                <div class="message-box">
                    <form method="post" class="text">
                        <input type="text" name="message" placeholder="Type your message" required>
                        <input type="hidden" name="order_id" value="<?= $order_id ?>">
                        <input type="hidden" name="send_message" value="1">
                        <button type="submit"><i class="fa-regular fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <script src="js/script.js"></script>
</body>
</html>

