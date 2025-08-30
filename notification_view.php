<?php 
session_start(); 
include "pages/connection.php";  

$user_id = $_SESSION['id'] ?? 0; 
$notifications = [];  

if ($user_id) {     
    $stmt = $con->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");     
    $stmt->bind_param("i", $user_id);     
    $stmt->execute();     
    $result = $stmt->get_result();     
    while ($row = $result->fetch_assoc()) {         
        $notifications[] = $row;     
    }     
    $stmt->close(); 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
            z-index: 1000;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx=".66" cy=".95" r=".6"><stop offset="0" stop-color="%23ffffff" stop-opacity=".3"/><stop offset="1" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><path d="M0 0v20h100V0z" fill="url(%23a)"/></svg>') no-repeat;
            background-size: cover;
            opacity: 0.3;
        }

        .header h1 {
            font-size: 2.5em;
            font-weight: 300;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header-icon {
            font-size: 1.2em;
            margin-right: 10px;
            position: relative;
            z-index: 1;
        }

        .notifications-count {
            position: relative;
            z-index: 1;
            opacity: 0.9;
            font-size: 1.1em;
        }

        .content {
            padding: 30px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4em;
            color: #d1d5db;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #374151;
        }

        .notification {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .notification::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(79, 172, 254, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .notification:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .notification:hover::before {
            opacity: 1;
        }

        .notification.unread {
            border-left-color: #4facfe;
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
        }

        .notification.unread::after {
            content: '';
            position: absolute;
            top: 15px;
            right: 15px;
            width: 8px;
            height: 8px;
            background: #4facfe;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.2);
        }

        .notification-message {
            font-size: 1.1em;
            color: #1f2937;
            margin-bottom: 12px;
            line-height: 1.5;
            font-weight: 500;
        }

        .notification-date {
            color: #6b7280;
            font-size: 0.9em;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .notification-date i {
            margin-right: 6px;
            font-size: 0.8em;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .stats-bar {
            background: #f8faff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 1.8em;
            font-weight: bold;
            color: #4facfe;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9em;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            padding: 5px;
            background: #f3f4f6;
            border-radius: 10px;
        }

        .filter-tab {
            padding: 10px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .filter-tab.active {
            background: white;
            color: #4facfe;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
            
            .notification {
                padding: 15px;
            }
            
            .stats-bar {
                flex-direction: column;
                gap: 15px;
            }
            
            .filter-tabs {
                flex-wrap: wrap;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <a href="javascript:history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back
    </a>
    
    <div class="container fade-in">
        <div class="header">
            <h1><i class="fas fa-bell header-icon"></i>Notifications</h1>
            <?php if (!empty($notifications)): ?>
                <div class="notifications-count">
                    <?php 
                    $unread_count = array_filter($notifications, function($n) { return $n['status'] === 'unread'; });
                    echo count($notifications) . ' total • ' . count($unread_count) . ' unread';
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="content">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No notifications yet</h3>
                    <p>When you receive notifications, they'll appear here.</p>
                </div>
            <?php else: ?>
                <?php 
                $total = count($notifications);
                $unread = count(array_filter($notifications, function($n) { return $n['status'] === 'unread'; }));
                $read = $total - $unread;
                ?>
                
                <div class="stats-bar">
                    <div class="stat">
                        <div class="stat-number"><?= $total ?></div>
                        <div class="stat-label">Total</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?= $unread ?></div>
                        <div class="stat-label">Unread</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number"><?= $read ?></div>
                        <div class="stat-label">Read</div>
                    </div>
                </div>

                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="filterNotifications('all')">All</button>
                    <button class="filter-tab" onclick="filterNotifications('unread')">Unread</button>
                    <button class="filter-tab" onclick="filterNotifications('read')">Read</button>
                </div>

                <div class="notifications-list">
                    <?php foreach ($notifications as $n): ?>
                        <div class="notification <?= $n['status'] === 'unread' ? 'unread' : 'read' ?>" data-status="<?= $n['status'] ?>">
                            <div class="notification-message">
                                <?= htmlspecialchars($n['message']) ?>
                            </div>
                            <div class="notification-date">
                                <i class="fas fa-clock"></i>
                                <?= date('M j, Y \a\t g:i A', strtotime($n['created_at'])) ?>
                            </div>
                            <div class="notification-actions">
                                <a href="notification_detail.php?id=<?= $n['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                                <?php if ($n['status'] === 'unread'): ?>
                                    <button class="btn btn-secondary" onclick="markAsRead(<?= $n['id'] ?>)">
                                        <i class="fas fa-check"></i>
                                        Mark Read
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function filterNotifications(type) {
            const notifications = document.querySelectorAll('.notification');
            const tabs = document.querySelectorAll('.filter-tab');
            
            // Update active tab
            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter notifications
            notifications.forEach(notification => {
                const status = notification.dataset.status;
                if (type === 'all' || status === type) {
                    notification.style.display = 'block';
                } else {
                    notification.style.display = 'none';
                }
            });
        }

        function markAsRead(notificationId) {
            // You would implement this AJAX call to update the notification status
            fetch('mark_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Add smooth loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach((notification, index) => {
                notification.style.animationDelay = (index * 0.1) + 's';
                notification.classList.add('fade-in');
            });
        });
    </script>
</body>
</html>