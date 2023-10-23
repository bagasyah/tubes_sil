<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "projek_sil";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>