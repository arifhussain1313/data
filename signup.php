<?php
$servername = "localhost";
$username = "root";
$password = "";

// Connection to MySQL
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_email = $conn->real_escape_string($_POST['email']);
    $user_password = $conn->real_escape_string($_POST['password']);
    $user_name = $conn->real_escape_string($_POST['username']);

    // Hash the password
    $hashed_password = password_hash($user_password, PASSWORD_BCRYPT);

    // Database name based on user's email (remove special characters from email for db name)
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $user_email);

    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $dbName";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully for $user_email.<br>";
    } else {
        die("Error creating database: " . $conn->error);
    }

    // Connect to the newly created database
    $conn->select_db($dbName);

    // Create a table for the user in their database
    $tableName = 'users'; // or use $user_name if each user gets their own table

    $sql = "CREATE TABLE IF NOT EXISTS $tableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        username VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table '$tableName' created successfully.<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }

    // Insert the user data into the table
    $sql = "INSERT INTO $tableName (email, password, username) VALUES ('$user_email', '$hashed_password', '$user_name')";

    if ($conn->query($sql) === TRUE) {
        echo "Sign-up successful! Welcome, $user_name!<br>";
    } else {
        die("Error inserting data: " . $conn->error);
    }

    $conn->close();
}
?>

