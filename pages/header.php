<?php

include '../connection.php';

// Check if user session exists and fetch latest user info from DB
if (isset($_SESSION['role']) && (isset($_SESSION['id']) || isset($_SESSION['userid']))) {
    $user_id = $_SESSION['id'] ?? $_SESSION['userid'];

    // Determine user table based on role
    $table = '';
    if ($_SESSION['role'] === 'Admin') {
        $table = 'tbluser';
    } elseif ($_SESSION['role'] === 'Clerk') {
        $table = 'tblclerk';
    } elseif ($_SESSION['role'] === 'Official') {
        $table = 'tblofficials';
    }

    if ($table && $user_id) {
        // Use prepared statement to prevent SQL injection
        $stmt = $con->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            // Store user image path in session for display
            $_SESSION['image'] = !empty($row['image']) ? $row['image'] : "../../img/default_user.png";
            $_SESSION['username'] = $row['username'] ?? '';
            // To display in modal, we use $row below as well
            $currentUserData = $row;
        } else {
            // Fallback for missing user
            $_SESSION['image'] = "../../img/default_user.png";
            $currentUserData = null;
        }
        $stmt->close();
    } else {
        $_SESSION['image'] = "../../img/default_user.png";
        $currentUserData = null;
    }
} else {
    $_SESSION['image'] = "../../img/default_user.png";
    $currentUserData = null;
}

// Variables for easier usage in HTML
$image = $_SESSION['image'];
$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? '';

// Function to fetch notifications for the current user
function fetchNotifications($con, $user_id, $limit = 10) {
    $notifications = [];
    $unread_count = 0;
    
    if ($user_id) {
        // Get notifications for the user, ordered by most recent first
        $stmt = $con->prepare("SELECT id, message, icon, status, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
            if ($row['status'] === 'unread') {
                $unread_count++;
            }
        }
        $stmt->close();
    }
    
    return ['notifications' => $notifications, 'unread_count' => $unread_count];
}

// Function to mark notification as read
function markNotificationAsRead($con, $notification_id, $user_id) {
    $stmt = $con->prepare("UPDATE notifications SET status = 'read' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// Function to mark all notifications as read
function markAllNotificationsAsRead($con, $user_id) {
    $stmt = $con->prepare("UPDATE notifications SET status = 'read' WHERE user_id = ? AND status = 'unread'");
    $stmt->bind_param("i", $user_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// Fetch notifications for current user
$notification_data = fetchNotifications($con, $user_id ?? 0);
$notifications = $notification_data['notifications'];
$unread_count = $notification_data['unread_count'];

// Handle AJAX requests for notifications
if (isset($_POST['action']) && $_POST['action'] === 'mark_read' && isset($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']);
    $success = markNotificationAsRead($con, $notification_id, $user_id ?? 0);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'mark_all_read') {
    $success = markAllNotificationsAsRead($con, $user_id ?? 0);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit();
}

// Handle Edit Profile form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_saveeditProfile'])) {
    $newUsername = trim($_POST['txt_username'] ?? '');
    $newPassword = trim($_POST['txt_password'] ?? '');

    if (!empty($newUsername) && !empty($role) && ($user_id ?? false)) {
        $table = '';
        if ($role === 'Admin') {
            $table = 'tbluser';
        } elseif ($role === 'Clerk') {
            $table = 'tblclerk';
        } elseif ($role === 'Official') {
            $table = 'tblofficials';
        }

        if ($table) {
            if (!empty($newPassword)) {
                // Hash the new password
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $con->prepare("UPDATE $table SET username = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssi", $newUsername, $passwordHash, $user_id);
            } else {
                // Update username only if password not provided
                $stmt = $con->prepare("UPDATE $table SET username = ? WHERE id = ?");
                $stmt->bind_param("si", $newUsername, $user_id);
            }

            if ($stmt->execute()) {
                // Update session username
                $_SESSION['username'] = $newUsername;

                // Optionally, show success message or redirect
                echo "<script>alert('Profile updated successfully. Please login again to see changes.'); window.location='../../logout.php';</script>";
                exit();
            } else {
                echo "<script>alert('Failed to update profile. Please try again.');</script>";
            }
            $stmt->close();
        }
    } else {
        echo "<script>alert('Username cannot be empty.');</script>";
    }
}
?>

<!-- HEADER -->
<header class="header" style="position: fixed; top: 0; left: 0;">
<a href="#" class="logo" style="
    display: flex; 
    align-items: center; 
    justify-content: left; 
    padding: 20px 8px; 
    font-family: 'Times New Roman', Times, serif;
    text-decoration: none; 
    background-color: #f0f0e7ff; /* <-- Background color added */
    color:black;              /* Optional: text color inside */
  
">
   <div style="display: flex; align-items: center;">
    <img src="../../img/bar.png" alt="Logo" style="height: 50px; margin-right:  30px;" />
    <span style="font-size: 20px; font-weight: bold;">DAY-AS</span>
    
</div>

</a>

  
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <div class="navbar-right">
            <ul class="nav navbar-nav">
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="position: relative;">
                        <i class="fa fa-bell" style="color: black;"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="label label-warning" style="position: absolute; top: 10; right: 10; transform: translate(50%, -50%); font-size: 9px; padding: 2px 5px;">
                                <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-label="Notifications menu" style="width: 300px;">
                        <li class="header" style="background-color:rgb(7, 141, 36); color: white; padding: 10px; text-align: center;">
                            You have <?php echo $unread_count; ?> unread notifications
                            <?php if ($unread_count > 0): ?>               
                            <?php endif; ?>
                        </li>
                        <li>
                            <ul class="menu" style="max-height: 300px; overflow-y: auto;">
                                <?php if (empty($notifications)): ?>
                                    <li style="padding: 15px; text-align: center; color: #999;">
                                        <i class="fa fa-info-circle"></i> No notifications yet
                                    </li>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <li style="border-bottom: 1px solid #f0f0f0;">
                                            <a href="#" onclick="markAsRead(<?php echo $notification['id']; ?>)" 
                                               style="display: block; padding: 10px; color: #333; text-decoration: none; <?php echo $notification['status'] === 'unread' ? 'background-color: #f0f0f0;' : ''; ?>">
                                                <i class="<?php echo htmlspecialchars($notification['icon']); ?>" style="margin-right: 5px;"></i>
                                                <span style="font-size: 12px;">
                                                    <?php echo htmlspecialchars($notification['message']); ?>
                                                </span>
                                                <small style="display: block; color: #999; margin-top: 3px;">
                                                    <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                                </small>
                                                <?php if ($notification['status'] === 'unread'): ?>
                                                    <span style="float: right; width: 8px; height: 8px; background-color: #3c8dbc; border-radius: 50%; margin-top: 5px;"></span>
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="footer">
                            <a href="../../notification_view.php" style="text-align: center; display: block; padding: 10px; background-color: #f9f9f9; color: #333;">
                                View all notifications
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- User Account Dropdown -->                 
                <li class="dropdown user user-menu">                     
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display: flex; align-items: center;" aria-haspopup="true" aria-expanded="false">                         
                        <img src="<?php echo htmlspecialchars($image); ?>" class="img-circle" alt="User profile image" style="height: 24px; width: 20px; margin-right: 5px;">                         
                        <span>                             
                            <?php echo htmlspecialchars($role);?>                             
                            <i class="caret"></i>                         
                        </span>                     
                    </a>                     
                    <ul class="dropdown-menu" role="menu" aria-label="User account menu">                         
                        <li class="user-header bg-gray text-center" style="background-color: #2c2c2cff; color:black;">                             
                            <center>                                 
                                <img src="<?php echo htmlspecialchars($image); ?>" class="img-circle" alt="User profile large image" style="height: 70px; width: 70px; margin-bottom: 10px; object-fit: cover; ">                             
                            </center>                             
                            <p><?php echo htmlspecialchars($role); ?></p>                         
                        </li>                         
                        <li class="user-footer">                             
                            <div class="pull-left">                                 
                                <button type="button" class="btn btn-default btn-flat" data-toggle="modal" data-target="#editProfileModal">Change Account</button>                             
                            </div>                             
                            <div class="pull-right">                                 
                                <a href="#" onclick="confirmLogout(); return false;" class="btn btn-default btn-flat" role="button">
                                    <i class="fa fa-sign-out" aria-hidden="true"></i> Sign out
                                </a>                             
                            </div>                         
                        </li>                     
                    </ul>                 
                </li>             
            </ul>         
        </div>     
    </nav> 
</header>

<!-- Make sure SweetAlert2 is loaded (add this in your head section if not already included) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->

<!-- Add this script before closing body tag or in your footer -->
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Logout Confirmation',
        text: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#868e96',
        confirmButtonText: '<i class="fa fa-sign"></i> Yes, Logout',
        cancelButtonText: '<i class="fa fa-times"></i> Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-primary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading message
            Swal.fire({
                icon: 'question',
                title: 'Logging out...',
                html: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><br>Please wait while we log you out safely.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirect to logout page after a brief delay
            setTimeout(() => {
                window.location.href = '../../logout.php';
            }, 1500);
        }
    });
}

// Function to mark a single notification as read
function markAsRead(notificationId) {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_read&notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page to update the notification count and styling
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Function to mark all notifications as read
function markAllAsRead() {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_all_read'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page to update the notification count and styling
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Auto-refresh notifications every 30 seconds (optional)
setInterval(function() {
    // You can implement a more sophisticated AJAX refresh here if needed
    // For now, we'll just reload the page periodically when idle
}, 30000);
</script>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <form method="post" novalidate>
        <div class="modal-dialog modal-sm" style="width:300px !important;" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding:12px 20px;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -12px; font-size: 25px;">&times;</button>
                    <h4 class="modal-title" id="editProfileModalLabel">Change Account</h4>
                </div>
                <div class="modal-body" style="padding: 15px 20px;">
                    <div class="form-group">
                        <label for="txt_username">Username:</label>
                        <input name="txt_username" id="txt_username" class="form-control input-sm" type="text" value="<?php echo htmlspecialchars($username); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="txt_password">New Password:</label>
                        <input name="txt_password" id="txt_password" class="form-control input-sm" type="password" placeholder="Enter new password (leave blank to keep current)" />
                    </div>
                </div>
                <div class="modal-footer" style="padding: 10px 20px;">
                    <input type="button" class="btn btn-default btn-sm" data-dismiss="modal" value="Cancel" />
                    <input type="submit" class="btn btn-primary btn-sm" id="btn_saveeditProfile" name="btn_saveeditProfile" value="Save" />
                </div>
            </div>
        </div>
    </form>
</div>