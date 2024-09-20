<?php
session_start();

// Check if the user is logged in
$loginLink = '';
$mgmtLink = '';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Hide the login link and show the management instructions link
    $mgmtLink = '<li><a href="../php/mgmtinstruct.php">Management Instructions</a></li>';
} else {
    // Show the login link
    $loginLink = '<li><a href="../html/login.html">Login</a></li>';
}

// Load XML data for instructions
$instructionsXml = simplexml_load_file('../data/instructions.xml');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainentry.css"> <!-- Load style.css -->
    <title>Entry Book</title>
</head>
<body>
    <h1 class="main-title">Entry Book</h1>
    <nav>
        <ul>
            <li><a href="../php/MainEntry.php">Home</a></li> <!-- Home link -->
            <li><a href="../php/add-entry.php">Add Entry</a></li> <!-- Add Entry link -->
            <?php
            // Show login or management instructions link based on user session
            echo $loginLink;
            echo $mgmtLink;
            ?>
        </ul>
    </nav>
    <div class="container">
        <div id="entry-container">
            <h2>OB Entries</h2> <!-- Entries header -->
            <!-- You can load entries dynamically via AJAX or PHP -->
        </div>
		<div id="loader" style="display:none;">Loading...</div>
        <div id="instructions-container">
            <h2>Management Instructions</h2> <!-- Instructions header -->

           <?php
           // Gather instructions into an array
           $instructionsArray = [];
           foreach ($instructionsXml->instruction as $instruction) {
               $instructionsArray[] = $instruction;
           }

           // Sort the instructions by 'id' (or 'date')
           usort($instructionsArray, function($a, $b) {
               return (int)$b['id'] - (int)$a['id']; // Sorts by 'id' in descending order (newest to oldest)
           });

           // Loop through each sorted instruction and display it
           foreach ($instructionsArray as $instruction) {
               // Check the value of the ackop field
               $ackop = (string)$instruction->ackop;

               // Apply a highlight class if the instruction is not acknowledged (ackop = '')
               $highlightClass = $ackop === '' ? 'highlight-red' : '';

               echo '<div class="instruction-entry ' . $highlightClass . '" data-id="' . $instruction['id'] . '" data-ackop="' . $ackop . '">';
               echo '<p><strong>Manager:</strong> ' . $instruction->manager . '</p>';
               echo '<p><strong>Instruction:</strong> ' . $instruction->instruction_text . '</p>';
               echo '<p><strong>Date:</strong> ' . $instruction->date . '</p>';

               // Show ACK button if the instruction is not acknowledged (ackop = 'none')
               if ($ackop === 'none') {
                   echo '<button class="ack-button" data-id="' . $instruction['id'] . '">ACK</button>';
               } else {
                   echo '<p><strong>Acknowledged by:</strong> ' . $ackop . '</p>';
               }

               echo '</div>';
               echo '<hr>'; // Add a line after each instruction entry
           }
           ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Load jQuery -->
    <script src="../js/script.js" defer></script> <!-- Load the script.js -->
    <script src="../js/entryack.js" defer></script> <!-- Load entryack.js for ACK button logic -->
    <script src="../js/userlogged.js"></script> <!-- Load userlogged.js to handle user session -->
	<!-- Countdown timer display commented out
    <div id="countdown"></div>
    -->
</body>
</html>
