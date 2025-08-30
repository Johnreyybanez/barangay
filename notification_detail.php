<?php
session_start();
include "pages/connection.php";

$user_id = $_SESSION['id'] ?? 0;
$notification_id = intval($_GET['id'] ?? 0);
$notification = null;

if ($notification_id && $user_id) {
    // Mark as read
    $stmt = $con->prepare("UPDATE notifications SET status = 'read' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Fetch details
    $stmt = $con->prepare("SELECT * FROM notifications WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notification = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notification Detail</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <?php if ($notification): ?>
        <h2>Notification Detail</h2>
        <p><strong>Message:</strong> <?= htmlspecialchars($notification['message']) ?></p>
        <p><strong>Status:</strong> <?= ucfirst($notification['status']) ?></p>
        <p><strong>Date:</strong> <?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?></p>
        <p><a href="notification_view.php">&larr; Back to all notifications</a></p>
    <?php else: ?>
      
    <?php endif; ?>
</body>
</html>
