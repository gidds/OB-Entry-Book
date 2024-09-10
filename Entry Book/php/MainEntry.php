<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Hide the login link
    $loginLink = '';
} else {
    // Show the login link
    $loginLink = '<li><a href="../html/login.html">Login</a></li>';
}
?>
<!-- MainEntry.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry Book</title>
    <link rel="stylesheet" href="../css/mainentry.css"> <!-- Load style.css -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Load jQuery -->
    <script src="../js/script.js" defer></script> <!-- Load the script.js -->
    <script src="../js/userlogged.js"></script> <!--Load user logged script-->
    <script src="../js/entryack.js"></script>
</head>
<body>
    <h1 class="main-title">Entry Book</h1>
    <nav>
        <ul>
            <li><a href="../php/MainEntry.php">Home</a></li> <!-- Home link -->
            <li><a href="../php/add-entry.php">Add Entry</a></li> <!-- Add Entry link -->
            <?php echo $loginLink; ?>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                <li><a href="../php/mgmtinstruct.php">Management Instructions</a></li>
            <?php } ?>
        </ul>
        <div id="countdown"></div> <!-- Countdown timer display -->
    </nav>
    <div class="container">
        <div id="entry-container">
            <h2> OBEntries</h2> <!-- Entries header -->
            <!-- Entries will be displayed here -->
        </div>
        <div id="instructions-container">
            <h2>Management Instructions</h2> <!-- Instructions header -->
            <!-- Instructions will be displayed here -->
        </div>
    </div>
</body>
</html>
