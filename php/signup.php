<?php

// require_once "db.php";

// $username = $_POST["username"];
// $email = $_POST["email"];
// $password = $_POST["password"];
// $confirmPassword = $_POST["confirm_password"];

// if ($password !== $confirmPassword) {
//     die("Passwords do not match!");
// }

// $passwordHash = password_hash($password, PASSWORD_DEFAULT);

// $sql = "INSERT INTO users (username, email, password_hash)
//         VALUES (?, ?, ?)";

// $stmt = $conn->prepare($sql);

// $stmt->bind_param(
//     "sss",
//     $username,
//     $email,
//     $passwordHash
// );

// if ($stmt->execute()) {

//     echo "Account created successfully!";

// } else {

//     echo "Error creating account: " . $stmt->error;

// }

// $stmt->close();
// $conn->close();

// ?>