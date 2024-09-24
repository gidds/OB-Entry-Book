<?php
// Entry Book/php/search-entries.php
session_start();
// Prevent browser caching for this page
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    // Load XML data for entries
    $entriesXml = simplexml_load_file('../data/entries.xml');

    // Search functionality
    if (isset($_POST['search-type'])) {
        $searchType = htmlspecialchars($_POST['search-type']);
        $searchTerm = isset($_POST['search-term']) ? htmlspecialchars(trim($_POST['search-term'])) : '';
        $searchDate = isset($_POST['search-date']) ? htmlspecialchars(trim($_POST['search-date'])) : '';

        // Convert date to YYYY-MM-DD format if searching by date
        if ($searchType === 'date' && !empty($searchDate)) {
            $searchTerm = date('Y-m-d', strtotime($searchDate));
        }

        // Search by customer, instruction, date, or ob number
        $searchResults = [];
        foreach ($entriesXml->entry as $entry) {
            if ($searchType === 'customer' && stripos($entry->customer, $searchTerm) !== false) {
                $searchResults[] = $entry;
            } elseif ($searchType === 'instruction' && stripos($entry->obentry, $searchTerm) !== false) {
                $searchResults[] = $entry;
            } elseif ($searchType === 'date' && $entry->date == $searchTerm) {
                $searchResults[] = $entry;
            } elseif ($searchType === 'ob-number' && str_replace('\\', '\\\\', $entryObNumber) === str_replace('\\', '\\\\', $searchTerm)) {
                $searchResults[] = $entry;
            }
        }

        // Display search results
        echo '<h2>Search Results</h2>';
        echo '<div id="search-results-container">';
        if (empty($searchResults)) {
            echo '<p>No information found.</p>';
        } else {
            foreach ($searchResults as $entry) {
                echo '<p>Customer: ' . htmlspecialchars($entry->customer) . '</p>';
                echo '<p>Instruction: ' . htmlspecialchars($entry->obentry) . '</p>';
                echo '<p>Date: ' . htmlspecialchars($entry->date) . '</p>';
                echo '<p>OB Number: ' . htmlspecialchars($entry->ob_number) . '</p>';
                echo '<hr>';
            }
        }
        echo '</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Entries</title>
    <link rel="stylesheet" href="../css/search.css">
</head>
<body>
    <h1 class="main-title">Search Entries</h1>
    <nav>
        <ul>
            <li><a href="../php/MainEntry.php">Home</a></li>
            <li><a href="../php/add-entry.php">Add Entry</a></li>
            <li><a href="../php/search-entries.php">Search Entries</a></li>
            <?php echo $loginLink; ?>
            <?php echo $mgmtLink; ?>
        </ul>
    </nav>
    <div class="container" id="search-container">
        <form id="search-form" action="" method="post">
            <label for="search-type">Search by:</label>
            <select id="search-type" name="search-type">
                <option value="customer">Customer</option>
                <option value="instruction">Instruction</option>
                <option value="date">Date</option>
                <option value="ob-number">OB Number</option>
            </select>
            <br>
            <!-- This input field will change based on search-type -->
            <label for="search-term">Search term:</label>
            <input type="text" id="search-term" name="search-term">
            <input type="date" id="search-date" name="search-date" style="display:none;"> <!-- Hidden by default -->
            <br>
            <button type="submit">Search</button>
            form 1
        </form>
    </div>
    <div id="search-results-container">
        <!-- The results will be dynamically inserted here by AJAX -->
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/userlogged.js"></script>
    <script>
        $(document).ready(function() {
    // When search type changes, toggle the input fields
    $('#search-type').on('change', function() {
        if ($(this).val() === 'date') {
            // Show date picker and hide text input
            $('#search-term').hide();
            $('#search-date').show();
        } else {
            // Show text input and hide date picker
            $('#search-term').show();
            $('#search-date').hide();
        }
    });

    // Handle form submission via AJAX
    $('#search-form').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    var searchType = $('#search-type').val();
    var formData;

    if (searchType === 'date') {
        formData = { 'search-type': searchType, 'search-term': $('#search-date').val() };
    } else {
        formData = $(this).serialize(); // Default serialization for other types
    }

    $.ajax({
        url: '../php/search-entries.php',
        type: 'POST',
        data: formData,
        success: function(data) {
            $('#search-results-container').html(data); // Display search results
        }
    });
});

});

    </script>
</body>
</html>
