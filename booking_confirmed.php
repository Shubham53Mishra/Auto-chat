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
                    $message .= "<p class='success'>Message sent successfully to $phoneNumber</p>";
                } else {
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
    // WhatsApp Business API URL and Access Token
    $whatsapp_api_url = "https://graph.facebook.com/v20.0/430568443461658/messages";
    $access_token = "EAARMxPiCSEQBO0XRmY6G7g3EitQePB0YCR4DtIBW5V6iEYV2kQgZCNZAvphR1GztyOuY6cahfDKOdWvbTNu29ZAnT9PfrQeBgiPhuV2RYJwnr7OenBLFN7c0uDBhjBNuUqHYBs8UZAy4DyRFZAvzeCaZCwvTE9FDPkbVicSYBuzDddUtp5PLSTalUZAXZBtRXQnwV1x0T4eHFRL3ZCskL4gZBAPZAZBcgB4VF0HOPmcZD";

    // Send message functionality
    $message .= sendMessage($conn, $whatsapp_api_url, $access_token, 'booking_confirmed');
}

// Close database connection
$conn->close();

// Redirect back to the admin dashboard with a message
$_SESSION['message'] = $message;
header("Location: admin_dashboard.php");
exit();
?>
