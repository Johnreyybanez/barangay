<!DOCTYPE html>
<html lang="en">
<?php
session_start();
?>
<head>
    <meta charset="UTF-8">
    <title>Barangay Information System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js"></script>
    <style>
        /* Adjust icon vertical position */
        .input-icon {
            top: 70%;
            transform: translateY(-50%);
        }
    </style>
</head>
<body class="bg-purple-900 relative min-h-screen flex items-center justify-center">

    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-gray-900 to-purple-800"></div>

    <!-- Login Card -->
    <div class="relative z-10 flex justify-center items-center w-full">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <!-- Header with updated gradient color -->
            <div class="bg-gradient-to-r from-purple-700 via-pink-600 to-purple-900 rounded-t-2xl text-center p-5">
                <img src="img/logo.png" alt="Logo" class="mx-auto h-20 mb-2">
                <h1 class="text-xl font-bold text-white">Barangay Information System</h1>
            </div>

            <!-- Login Form -->
            <div class="p-5">
                <form method="post" class="space-y-4">
                    <!-- Username -->
                    <div class="relative">
                        <label class="block mb-1 text-gray-700">Username</label>
                        <span class="absolute left-3 input-icon text-gray-400">
                            <i class="fa fa-user"></i>
                        </span>
                        <input name="txt_username" type="text" placeholder="Enter Username" required
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label class="block mb-1 text-gray-700">Password</label>
                        <span class="absolute left-3 input-icon text-gray-400">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input :type="show ? 'text' : 'password'" name="txt_password" placeholder="Enter Password" required
                            class="w-full pl-10 pr-10 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <span class="absolute right-3 input-icon text-purple-600 cursor-pointer">
                            <i @click="show = !show" :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                        </span>
                    </div>

                    <!-- Role Dropdown -->
                    <div class="relative">
                        <label class="block mb-1 text-gray-700">Select Role</label>
                        <span class="absolute left-3 input-icon text-gray-400">
                            <i class="fa fa-user-tag"></i>
                        </span>
                        <select name="role" required
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">-- Select Role --</option>
                            <option value="Admin">Admin</option>
                            <option value="Clerk">Clerk</option>
                            <option value="Official">Official</option>
                        </select>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" name="btn_login"
                        class="w-full bg-purple-700 text-white py-2 rounded-lg font-semibold hover:bg-purple-800 transition duration-300">Log In</button>

                    <!-- Back to Homepage -->
                    <div class="text-center mt-2">
                        <a href="index.php" class="text-purple-600 hover:text-purple-800 text-sm">← Back to Homepage</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PHP Login Logic -->
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
                                    timer: 2000,
                                    showConfirmButton: false
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
                    timer: 2500,
                    showConfirmButton: false
                });
              </script>";
        unset($_SESSION['error']);
    }
    ?>
</body>
</html>
