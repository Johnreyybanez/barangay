<!DOCTYPE html>
<html>
<?php
session_start();
?>
<head>
    <meta charset="UTF-8">
    <title>Barangay Information System</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" href="img/logo.png">
    <!-- Font Awesome for eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
    body {
        font-family: 'Times New Roman', Times, serif;
        color: #333;
        background: linear-gradient(135deg, #1e3c72, #2a5298, #89CFF0);
        min-height: 100vh;
        overflow-x: hidden;
        position: relative;
    }

    /* Animated Bubbles Background */
    .bubbles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .bubble {
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        animation: float 8s linear infinite;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
    }

    .bubble:nth-child(1) {
        width: 60px;
        height: 60px;
        left: 10%;
        bottom: -60px;
        animation-delay: 0s;
        animation-duration: 12s;
    }

    .bubble:nth-child(2) {
        width: 30px;
        height: 30px;
        left: 20%;
        bottom: -30px;
        animation-delay: 2s;
        animation-duration: 10s;
    }

    .bubble:nth-child(3) {
        width: 80px;
        height: 80px;
        left: 35%;
        bottom: -80px;
        animation-delay: 4s;
        animation-duration: 15s;
    }

    .bubble:nth-child(4) {
        width: 40px;
        height: 40px;
        left: 50%;
        bottom: -40px;
        animation-delay: 1s;
        animation-duration: 11s;
    }

    .bubble:nth-child(5) {
        width: 50px;
        height: 50px;
        left: 65%;
        bottom: -50px;
        animation-delay: 3s;
        animation-duration: 13s;
    }

    .bubble:nth-child(6) {
        width: 35px;
        height: 35px;
        left: 75%;
        bottom: -35px;
        animation-delay: 5s;
        animation-duration: 9s;
    }

    .bubble:nth-child(7) {
        width: 70px;
        height: 70px;
        left: 85%;
        bottom: -70px;
        animation-delay: 7s;
        animation-duration: 14s;
    }

    .bubble:nth-child(8) {
        width: 25px;
        height: 25px;
        left: 15%;
        bottom: -25px;
        animation-delay: 6s;
        animation-duration: 8s;
    }

    .bubble:nth-child(9) {
        width: 45px;
        height: 45px;
        left: 55%;
        bottom: -45px;
        animation-delay: 8s;
        animation-duration: 12s;
    }

    .bubble:nth-child(10) {
        width: 55px;
        height: 55px;
        left: 25%;
        bottom: -55px;
        animation-delay: 9s;
        animation-duration: 16s;
    }

    @keyframes float {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 0.7;
        }
        90% {
            opacity: 0.7;
        }
        100% {
            transform: translateY(-100vh) rotate(360deg);
            opacity: 0;
        }
    }

    h3.panel-title {
        font-family: 'Times New Roman', Times, serif;
        color: white;
    }

    .panel-heading {
        text-align: center;
        background: linear-gradient(135deg,rgb(25, 26, 29), #89CFF0);
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .panel {
        border-radius: 15px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .panel-body {
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .form-control {
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .btn {
        border-radius: 10px;
    }

    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .input-group-addon {
        background: white;
        border: 1px solid #ccc;
        border-left: none;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        cursor: pointer;
    }

    .position-relative {
        position: relative;
    }

    .input-icon {
        position: absolute;
        top: 70%;
        left: 10px;
        transform: translateY(-50%);
        font-size: 18px;
        color: #888;
    }

    .form-control {
        padding-left: 30px;
    }

    .container {
        position: relative;
        z-index: 1;
    }

    /* Footer Styling */
    footer {
        color: white;
        text-align: center;
        padding: 40px 0;
        position: relative;
        bottom: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
    }
    
    .logo-bounce-wrapper {
        position: relative;
        display: inline-block;
    }

    .logo-bounce {
        animation: bounceInPlace 1s ease;
        position: relative;
        z-index: 2;
    }

    /* Logo bounce without moving down the page */
    @keyframes bounceInPlace {
        0%   { transform: translateY(0); }
        20%  { transform: translateY(-30px); }
        40%  { transform: translateY(0); }
        60%  { transform: translateY(-15px); }
        80%  { transform: translateY(0); }
        100% { transform: translateY(0); }
    }

    /* Water splash effect */
    .splash {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%) scale(0);
        width: 100px;
        height: 40px;
        background: radial-gradient(ellipse at center, rgba(135,206,250,0.5) 0%, rgba(135,206,250,0) 80%);
        border-radius: 50%;
        animation: splashEffect 2s ease forwards;
        z-index: 1;
        opacity: 0.6;
    }

    @keyframes splashEffect {
        30% {
            transform: translateX(-50%) scale(1);
            opacity: 1;
        }
        100% {
            transform: translateX(-50%) scale(1.5);
            opacity: 0;
        }
    }
</style>
<body class="skin-black">
    <!-- Animated Bubbles Background -->
    <div class="bubbles">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="container" style="margin-top:100px">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="logo-bounce-wrapper">
                        <img src="img/logo.png" class="logo-bounce" style="height:150px;" />
                        <div class="splash"></div>
                    </div>
                    <h3 class="panel-title"><strong>Barangay Information System</strong></h3>
                </div>

                <div class="panel-body">
                    <form role="form" method="post">
                        <!-- Username Field -->
                        <div class="form-group position-relative">
                            <label for="txt_username">Username</label>
                            <i class="fa fa-user input-icon"></i>
                            <input type="text" class="form-control" name="txt_username" placeholder="Enter Username" required>
                        </div>

                        <!-- Password Field -->
                        <div class="form-group position-relative">
                            <label for="txt_password">Password</label>
                            <i class="fa fa-lock input-icon"></i>
                            <input type="password" class="form-control" name="txt_password" id="txt_password" placeholder="Enter Password" required>  
                        </div>
                       
                        <!-- Role Field -->
                        <div class="form-group position-relative">
                            <label for="role">Select Role</label>
                            <i class="fa fa-users input-icon"></i> <!-- Added icon for the role field -->
                            <select class="form-control" name="role" required>
                                <option value="">-- Select Role --</option>
                                <option value="Admin">Admin</option>
                                <option value="Clerk">Clerk</option>
                                <option value="Official">Official</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <center><button type="submit" class="btn btn-l btn-success" name="btn_login">Log in</button></center>
                        <!-- Back Button -->
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-secondary">
                                Back to Homepage
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
include "pages/connection.php";

if (isset($_POST['btn_login'])) {
    $username = mysqli_real_escape_string($con, $_POST['txt_username']);
    $password = $_POST['txt_password'];
    $selectedRole = $_POST['role']; 

    if (empty($selectedRole)) {
        $_SESSION['error'] = "Please select a role.";
    } else {
        $query = "SELECT id, fullname, username, password_hash, role FROM tbluser WHERE username = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password_hash'])) {
                if ($row['role'] === $selectedRole) {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['fullname'] = $row['fullname'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    $roleRedirects = [
                        'Admin' => 'pages/dashboard/dashboard.php',
                        'Clerk' => 'pages/resident/resident.php',
                        'Official' => 'pages/resident/.php'
                    ];

                    echo "<script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Login Successful',
                                text: 'Welcome, {$row['role']}!',
                                showConfirmButton: true,
                            }).then(() => {
                                window.location.href = '{$roleRedirects[$row['role']]}';
                            });
                          </script>";
                    exit();
                } else {
                    $_SESSION['error'] = "Selected role does not match your actual role.";
                }
            } else {
                $_SESSION['error'] = "Incorrect password.";
            }
        } else {
            $_SESSION['error'] = "User not found.";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
}

if (isset($_SESSION['error'])) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '{$_SESSION['error']}',
                showConfirmButton: true
            });
          </script>";
    unset($_SESSION['error']);
}
?>

<!-- JavaScript for toggle password -->
<script>
function togglePassword() {
    const passwordInput = document.getElementById("txt_password");
    const eyeIcon = document.getElementById("eyeIcon");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    }
}
</script>

</body>
</html>