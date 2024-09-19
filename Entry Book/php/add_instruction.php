<?php
// add_instruction.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $manager = isset($_POST['manager']) ? $_POST['manager'] : '';
    $instruction = isset($_POST['instruction']) ? $_POST['instruction'] : '';

    $xmlFile = '../data/instructions.xml';

    // Load existing XML file or create a new one
    if (file_exists($xmlFile)) {
        $xml = simplexml_load_file($xmlFile);
        if ($xml === false) {
            echo 'Failed to load XML file.';
            exit;
        }
    } else {
        $xml = new SimpleXMLElement('<instructions></instructions>');
    }

    // Get the last instruction ID
    $lastInstruction = $xml->xpath('//instruction[last()]');
    if ($lastInstruction) {
        $lastId = (int) $lastInstruction[0]['id']; // Extract the numeric ID
        $newId = $lastId + 1;
    } else {
        $newId = 1; // First instruction
    }


    // Create a new instruction entry
    $newEntry = $xml->addChild('instruction');
    $newEntry->addAttribute('id', $newId); // Use just the numeric ID
    $newEntry->addChild('date', htmlspecialchars($date));
    $newEntry->addChild('manager', htmlspecialchars($manager));
    $newEntry->addChild('instruction_text', htmlspecialchars($instruction));
    $newEntry->addChild('entry_time', date('Y-m-d H:i:s')); // Add current time of entry

    // Convert the SimpleXMLElement to DOMDocument for formatting
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false; // Remove extra whitespaces
    $dom->formatOutput = true; // Enable pretty print
    $dom->loadXML($xml->asXML());

    // Save the updated XML file
    if ($xml->asXML($xmlFile) === false) {
        echo 'Failed to save XML file.';
        exit;
    }

    // Return success
    echo 'success';
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo 'Invalid request method';
}
?>
