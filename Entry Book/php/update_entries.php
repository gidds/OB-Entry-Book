<?php
// update_entries.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $ob_number = $_POST['ob_number'];
    $customer = $_POST['customer'];
    $entry_text = $_POST['obentry'];

    $xml_file = '../data/entries.xml';

    // Load existing XML file
    $xml = simplexml_load_file($xml_file);

    // Create a new entry
    $new_entry = $xml->addChild('entry');
    $new_entry->addAttribute('id', $ob_number); // Assuming OB number as the ID for simplicity
    $new_entry->addChild('date', $date);
    $new_entry->addChild('ob_number', $ob_number);
    $new_entry->addChild('customer', $customer);
    $new_entry->addChild('obentry', $entry_text);

    // Save the updated XML
    if ($xml->asXML($xml_file) === false) {
        echo 'Failed to save XML file.';
    } else {
        echo 'Entry added successfully!';
    }
} else {
    echo 'Invalid request method';
}
?>
