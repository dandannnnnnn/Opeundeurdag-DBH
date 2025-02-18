<?php
// check_login.php

// (Database credentials - as before) ...
$dbHost = 'localhost';  // Or your Raspberry Pi's IP
$dbUser = 'your_db_user';
$dbPass = 'your_db_password';
$dbName = 'your_db_name';

// Function to check and/or insert user
function checkUser($achternaam, $voornaam, $email) {
    global $dbHost, $dbUser, $dbPass, $dbName;

    // Connect to the database (using MySQLi - preferred)
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        return ['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error];
    }

    // Sanitize inputs (VERY IMPORTANT to prevent SQL injection)
    $achternaam = $conn->real_escape_string($achternaam);
    $voornaam = $conn->real_escape_string($voornaam);
    $email = $conn->real_escape_string($email);

    // Check if the user already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists
        $conn->close();
        return ['status' => 'success', 'message' => 'User already exists'];  // Or perhaps 'login_success'
    } else {
        // User does not exist, insert new user
        $sql = "INSERT INTO users (achternaam, voornaam, email) VALUES ('$achternaam', '$voornaam', '$email')";
        if ($conn->query($sql) === TRUE) {
            $conn->close();
            return ['status' => 'new_user', 'message' => 'New user added']; // CLEARLY indicate new user
        } else {
            $conn->close();
            return ['status' => 'error', 'message' => 'Error inserting user: ' . $conn->error];
        }
    }
}