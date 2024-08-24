<?php
session_start();
include('db_connection.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
    } else {
        echo "<p style='color: red; text-align: center;'>Invalid username or password</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #e8f5e9; /* Light green background */
            font-family: Arial, sans-serif;
        }
        header, footer {
            width: 100%;
            background-color: #2e7d32; /* Dark green */
            color: white;
            padding: 15px 0;
            text-align: center;
            font-size: 24px;
        }
        .login-container {
            background-color: #ffffff;
            padding: 25px 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #2e7d32; /* Dark green */
            font-size: 28px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #2e7d32; /* Dark green border */
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        .login-container button:hover {
            background-color: #45a049; /* Darker green */
        }
        .login-container p {
            margin-top: 15px;
            color: red;
            font-size: 14px;
        }
        footer {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <header>
        Admin Panel
    </header>

    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

    <footer>
        © 2024 Your Company Name. All Rights Reserved.
    </footer>
</body>
</html>
