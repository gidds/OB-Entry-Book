// Entry Book/php/menu.php

<nav>
    <ul>
        <li><a href="../php/MainEntry.php">Home</a></li> <!-- Home link -->
        <li><a href="../php/add-entry.php">Add Entry</a></li> <!-- Add Entry link -->
        <li><a href="../php/search-entries.php">Search Entries</a></li> <!-- New Search Entries link -->
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
            <li><a href="../php/mgmtinstruct.php">Management Instructions</a></li>
        <?php } ?>
        <?php echo $loginLink; ?>
    </ul>
</nav>