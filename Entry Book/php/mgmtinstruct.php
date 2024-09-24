<!-- mginstruct.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Management Instruction</title>
    <link rel="stylesheet" href="../css/mginstruct.css"> <!-- Load style.css -->
</head>
<body>
    <h1 class="main-title">Add Management Instruction</h1>
    <nav>
        <ul>
            <li><a href="../php/MainEntry.php">Home</a></li>
            <li><a href="../php/add-entry.php">Add Entry</a></li>
        <li><a href="../php/search-entries.php">Search Entries</a></li> <!-- New Search Entries link -->
            <li><a href="../php/mgmtinstruct.php">Management Instructions</a></li>
        </ul>
    </nav>
    <div class="container">
        <form id="instruct-form">
            <label for="instruction-date">Date:</label>
            <input type="text" id="instruction-date" name="date" readonly>

            <label for="manager-select">Manager:</label>
            <select id="manager-select" name="manager">
            </select>

            <label for="instruction-text">Instruction:</label>
            <textarea id="instruction-text" name="instruction" required></textarea>

            <button type="submit">Add Instruction</button>
        </form>
    </div>
    <script src="../js/instruct.js" defer></script>
</body>
</html>
