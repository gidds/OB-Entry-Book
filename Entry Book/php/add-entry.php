<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Entry</title>
    <link rel="stylesheet" href="../css/add-entry.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/userlogged.js"></script>
    <script src="../js/entryscript.js" defer></script>
</head>
<body>
    <h1 class="main-title">Add Entry</h1>
    <nav>
        <ul>
            <li><a href="../html/MainEntry.html">Home</a></li>
            <li><a href="../php/add-entry.php">Add Entry</a></li>
            <li id="login-item"><a href="../html/login.html">Login</a></li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                <li><a href="../html/mginstruct.html">Add Management Instruction</a></li>
            <?php } ?>
        </ul>
    </nav>
    <form id="add-entry-form" action="update_entries.php" method="post">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" readonly><br><br>
        <label for="ob_number">OB Number:</label>
        <input type="text" id="ob_number" name="ob_number" readonly><br><br>
        <label for="customer">Customer:</label>
        <input type="text" id="customer" name="customer"><br><br>
        <label for="obentry">Entry:</label> 
        <textarea id="obentry" name="obentry"></textarea><br><br> 
        <button id="submit-btn" type="submit">Submit</button>
    </form>
</body>
</html>