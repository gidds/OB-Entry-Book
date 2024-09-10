<?php
// add_instruction.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $manager = $_POST['manager'];
    $instruction = $_POST['instruction'];

    $xmlFile = '../data/instructions.xml';

    // Load existing XML file or create a new one
    if (file_exists($xmlFile)) {
        $xml = simplexml_load_file($xmlFile);
    } else {
        $xml = new SimpleXMLElement('<instructions></instructions>');
    }

    // Get the last instruction ID
    $lastInstruction = $xml->xpath('//instruction[last()]');
    if ($lastInstruction) {
        $lastId = (int) substr($lastInstruction[0]['id'], 4); // Extract the ID number
        $newId = $lastId + 1;
    } else {
        $newId = 1; // First instruction
    }

    // Create a new instruction entry
    $newEntry = $xml->addChild('instruction');
    $newEntry->addAttribute('id', 'instruction-' . $newId);
    $newEntry->addChild('date', htmlspecialchars($date));
    $newEntry->addChild('manager', htmlspecialchars($manager));
    $newEntry->addChild('instruction_text', htmlspecialchars($instruction));
    $newEntry->addChild('entry_time', date('Y-m-d H:i:s')); // Add current time of entry

    // Save the updated XML file
    $xml->asXML($xmlFile);

    // Return success
    echo 'success';
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo 'Invalid request method';
}
?>