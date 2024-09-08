<?php
session_start();

// Assuming you have code here that validates the login credentials
$username = $_POST['username'];
$password = $_POST['password'];

// Replace this with your actual authentication logic
if ($username === 'admin' && $password === 'password123') {
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username;
    
    // Set the cookie
    echo "<script>window.opener.loginSuccess(); window.close();</script>";
    header("Location: ../html/mginstruct.html");
    exit();
} else {
    // Redirect back to the login page with an error message
    header("Location: ../php/login.php?error=invalid_credentials");
    exit();
}
?>
