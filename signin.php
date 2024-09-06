<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "arif";

// connection
$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email'"; // Corrected "FORM" to "FROM"
    $result = $conn->query($sql);

    // check if the user exists
    if($result->num_rows > 0) {
        // fetch the user data
        $user = $result->fetch_assoc();

        // check password
        if (password_verify($password, $user['password'])) { // Corrected "pssword_Verify" to "password_verify"
            echo "Sign in successful! Welcome, " . $user['username'] . "!";
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "No user found with this email address.";
    }
    $conn->close();
}
?>
