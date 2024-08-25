<?php
// Start the session
session_start();

// Check if the 'admin' session variable is set
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    // Redirect to admin login if 'admin' session is not set
    header("Location: admin_login.php");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "autochat_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to add a new user to the database
function addUser($conn) {
    if (isset($_POST['full_name']) && isset($_POST['phone_number']) && !isset($_POST['send_message'])) {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email'] ?? ''); // Email is optional

        // SQL query to insert user data
        $sql = "INSERT INTO Customers (FullName, PhoneNumber, Email) VALUES ('$full_name', '$phone_number', '$email')";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            return "<p class='success'>User added successfully.</p>";
        } else {
            return "<p class='error'>Error adding user: " . mysqli_error($conn) . "</p>";
        }
    }
    return '';
}

// Function to send messages to selected users via WhatsApp API
function sendMessage($conn, $whatsapp_api_url, $access_token, $template_name) {
    $selected_users = $_POST['users'] ?? [];
    $message = '';

    if (!empty($selected_users)) {
        foreach ($selected_users as $phoneNumber) {
            // Prepare the data for the API request
            $data = json_encode([
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => $template_name,
                    'language' => [
                        'code' => 'en_US'
                    ]
                ]
            ]);

            // Initialize cURL session
            $ch = curl_init($whatsapp_api_url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                'authorization: Bearer ' . $access_token,
                'content-type: application/json'
            ]);

            // Execute cURL request and capture the response
            $response = curl_exec($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                $message .= "<p class='error'>cURL Error: " . curl_error($ch) . "</p>";
            } else {
                // Get HTTP status code and response
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $response_data = json_decode($response, true);

                // Handle API response based on HTTP status code
                if ($http_code == 200) {
                    // Message sent successfully
                    $message .= "<p class='success'>Message sent successfully to $phoneNumber</p>";
                } else {
                    // Display error message with response details for debugging
                    $message .= "<p class='error'>Failed to send message to $phoneNumber. HTTP Status Code: $http_code</p>";
                    $message .= "<pre>" . print_r($response_data, true) . "</pre>";
                }
            }

            // Close cURL session
            curl_close($ch);
        }
    } else {
        $message .= "<p class='error'>Please select users to send a message.</p>";
    }

    return $message;
}

// Initialize message variable
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add user functionality
    $message .= addUser($conn);

    // WhatsApp Business API URL and Access Token
    $whatsapp_api_url = "https://graph.facebook.com/v20.0/430568443461658/messages";
    $access_token = "EAARMxPiCSEQBO0XRmY6G7g3EitQePB0YCR4DtIBW5V6iEYV2kQgZCNZAvphR1GztyOuY6cahfDKOdWvbTNu29ZAnT9PfrQeBgiPhuV2RYJwnr7OenBLFN7c0uDBhjBNuUqHYBs8UZAy4DyRFZAvzeCaZCwvTE9FDPkbVicSYBuzDddUtp5PLSTalUZAXZBtRXQnwV1x0T4eHFRL3ZCskL4gZBAPZAZBcgB4VF0HOPmcZD";

    // Send message functionality
    if (isset($_POST['send_message'])) {
        $message .= sendMessage($conn, $whatsapp_api_url, $access_token, 'booking_confirmed'); // Replace with the actual template name
    }

    // Hello World button functionality
    if (isset($_POST['hello_world'])) {
        $message .= sendMessage($conn, $whatsapp_api_url, $access_token, 'hello_world'); // Replace with the actual template name
    }

    // New button for Extend_Test template
    if (isset($_POST['booking_test'])) {
        $message .= sendMessage($conn, $whatsapp_api_url, $access_token, 'extend_test'); // Replaced template name
    }

    // New button for Cancel_Test template
    if (isset($_POST['cancel_test'])) {
        $message .= sendMessage($conn, $whatsapp_api_url, $access_token, 'cancel_test'); // Added template name
    }
}

// Fetch customer details for display
$sql = "SELECT FullName, PhoneNumber FROM Customers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header, footer {
            background-color: #4CAF50; /* Green color */
            color: white;
            text-align: center;
            padding: 10px 0;
        }
        .dashboard-container {
            width: 60%; /* Decreased width */
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center-align text */
        }
        .dashboard-box {
            margin: 0 auto;
            padding: 20px;
            background-color: #e8f5e9; /* Light green background */
            border: 1px solid #4CAF50; /* Green border */
            border-radius: 5px;
            display: inline-block; /* Center-align box */
            text-align: left; /* Align text inside the box to left */
            width: 100%; /* Full width within the container */
        }
        .message p.success {
            color: green;
            font-weight: bold;
        }
        .message p.error {
            color: red;
            font-weight: bold;
        }
        .input-box form input,
        .input-box form button {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
            border-radius: 5px;
        }
        .input-box form input {
            border: 1px solid #4CAF50; /* Green border */
            background-color: #e8f5e9; /* Light green background */
            color: #4CAF50; /* Green text color */
        }
        .input-box form button {
            background-color: #4CAF50; /* Green background */
            color: white;
            border: none;
        }
        .input-box form button:hover {
            background-color: #45a049; /* Darker green on hover */
        }
        .user-box {
            margin-top: 20px;
        }
        .user-list .user-checkbox {
            margin: 5px 0;
        }
        .button-container {
            margin-top: 20px;
        }
        .action-btn {
            margin: 5px;
            padding: 10px;
            background-color: #4CAF50; /* Green color */
            color: white;
            border: none;
            cursor: pointer;
        }
        .action-btn:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.user-box input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }
    </script>
</head>
<body>

<header>
    Admin Dashboard
</header>

<div class="dashboard-container">
    <div class="dashboard-box">

        <!-- Display any messages -->
        <div class="message">
            <?php echo $message; ?>
        </div>

        <!-- User Input Form -->
        <div class="input-box">
            <form method="POST" action="">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="text" name="phone_number" placeholder="Phone Number" required>
                <input type="email" name="email" placeholder="Email (Optional)">
                <button type="submit">Add User</button>
            </form>
        </div>

        <!-- User List and Actions -->
        <div class="user-box">
            <form method="POST" action="">
                <div class="user-list">
                    <?php if ($result->num_rows > 0): ?>
                        <label><input type="checkbox" onclick="toggleSelectAll(this)"> Select All</label><br>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="user-checkbox">
                                <label><input type="checkbox" name="users[]" value="<?php echo $row['PhoneNumber']; ?>"> <?php echo $row['FullName']; ?> (<?php echo $row['PhoneNumber']; ?>)</label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No users found.</p>
                    <?php endif; ?>
                </div>
                <div class="button-container">
                    <button class="action-btn" type="submit" name="send_message">Booking Confirmed</button>
                    <button class="action-btn" type="submit" name="hello_world">Hello World</button>
                    <button class="action-btn" type="submit" name="booking_test">Extend Test</button> <!-- Updated button name -->
                    <button class="action-btn" type="submit" name="cancel_test">Cancel Test</button> <!-- Added button for cancel_test -->
                </div>
            </form>
        </div>
    </div>
</div>

<footer>
    &copy; 2024 Your Company Name. All rights reserved.
</footer>

</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
