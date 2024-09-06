<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection to MySQL
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$success_msg = "";
$error_msg = "";

// Create database and table dynamically based on the username
if (isset($_POST['create'])) {
    $user_name = $conn->real_escape_string($_POST['username']);
    $user_password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Password hashing for security
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $user_name); // Sanitize username for DB name

    // Create database for the user
    $sql = "CREATE DATABASE IF NOT EXISTS $dbName";
    if ($conn->query($sql) === TRUE) {
        $success_msg = "Database '$dbName' created successfully!<br>";
    } else {
        $error_msg = "Error creating database: " . $conn->error . "<br>";
    }

    // Connect to the newly created database
    $conn->select_db($dbName);

    // Create a table for the user
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql) === TRUE) {
        $success_msg .= "Table 'users' created successfully!<br>";
        $sql_insert = "INSERT INTO users (username, password) VALUES ('$user_name', '$user_password')";
        if ($conn->query($sql_insert) === TRUE) {
            $success_msg .= "Welcome, $user_name! Your information has been added to the database.<br>";
        } else {
            $error_msg .= "Error inserting user data: " . $conn->error . "<br>";
        }
    } else {
        $error_msg .= "Error creating table: " . $conn->error . "<br>";
    }
}

// Insert operation
if (isset($_POST['insert'])) {
    $user_name = $conn->real_escape_string($_POST['username']);
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $user_name);
    $conn->select_db($dbName);

    $new_name = $conn->real_escape_string($_POST['new_name']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$new_name', '$new_password')";
    if ($conn->query($sql) === TRUE) {
        $success_msg = "New record inserted successfully!<br>";
    } else {
        $error_msg = "Error inserting record: " . $conn->error . "<br>";
    }
}

// Update operation
if (isset($_POST['update'])) {
    $user_name = $conn->real_escape_string($_POST['username']);
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $user_name);
    $conn->select_db($dbName);

    $id = $conn->real_escape_string($_POST['id']);
    $new_name = $conn->real_escape_string($_POST['new_name']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql = "UPDATE users SET username='$new_name', password='$new_password' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $success_msg = "Record updated successfully!<br>";
    } else {
        $error_msg = "Error updating record: " . $conn->error . "<br>";
    }
}

// Delete operation
if (isset($_POST['delete'])) {
    $user_name = $conn->real_escape_string($_POST['username']);
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $user_name);
    $conn->select_db($dbName);

    $id = $conn->real_escape_string($_POST['id']);

    $sql = "DELETE FROM users WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        $success_msg = "Record deleted successfully!<br>";
    } else {
        $error_msg = "Error deleting record: " . $conn->error . "<br>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<body>

<h1>User Management System</h1>

<!-- Display success or error messages -->
<?php
if (!empty($success_msg)) {
    echo "<p style='color:green;'>$success_msg</p>";
}
if (!empty($error_msg)) {
    echo "<p style='color:red;'>$error_msg</p>";
}
?>

<!-- Form to create a new database and table for the user -->
<h2>Create Your Database and Table</h2>
<form method="POST" action="">
    <label for="username">Enter Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Enter Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <input type="submit" name="create" value="Create and Greet">
</form>

<!-- Insert new record -->
<h2>Insert New Data</h2>
<form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="new_name">New Name:</label>
    <input type="text" id="new_name" name="new_name" required><br><br>

    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required><br><br>

    <input type="submit" name="insert" value="Insert Data">
</form>

<!-- Update existing record -->
<h2>Update Data</h2>
<form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="id">Record ID to Update:</label>
    <input type="number" id="id" name="id" required><br><br>

    <label for="new_name">New Name:</label>
    <input type="text" id="new_name" name="new_name" required><br><br>

    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required><br><br>

    <input type="submit" name="update" value="Update Data">
</form>

<!-- Delete record -->
<h2>Delete Data</h2>
<form method="POST" action="">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="id">Record ID to Delete:</label>
    <input type="number" id="id" name="id" required><br><br>

    <input type="submit" name="delete" value="Delete Data">
</form>

</body>
</html>
