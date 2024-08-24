<?php
$servername = "localhost";
$username = "root";
$password = "";
// $mobile_number=7418529632;
$dbname = "autochat_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
