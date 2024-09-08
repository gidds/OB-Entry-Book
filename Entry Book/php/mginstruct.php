<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Instructions</title>
    <link rel="stylesheet" href="../css/mginstruct.css">
</head>
<body>
    <h1>Management Instructions</h1>
    <!-- Your content here -->
</body>
</html>
