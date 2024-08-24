<?php
include('db_connection.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashing the password for security

    $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green; text-align: center;'>Admin registered successfully!</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .registration-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            flex: 1; /* Allow the container to take up available space */
        }
        .registration-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 28px;
        }
        .registration-container input[type="text"],
        .registration-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .registration-container input[type="text"]:focus,
        .registration-container input[type="password"]:focus {
            border-color: #4CAF50; /* Green border on focus */
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5); /* Green shadow on focus */
        }
        .registration-container button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            margin-top: 10px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .registration-container button:hover {
            background-color: #45a049; /* Darker green */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .registration-container p {
            margin-top: 15px;
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        Admin Panel
    </header>

    <div class="registration-container">
        <h2>Admin Registration</h2>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
    </div>

    <footer>
        © 2024 Your Company Name. All Rights Reserved.
    </footer>
</body>
</html>
