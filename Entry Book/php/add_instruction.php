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
    
    // Create a new instruction entry
    $newEntry = $xml->addChild('instruction');
    $newEntry->addChild('date', htmlspecialchars($date));
    $newEntry->addChild('manager', htmlspecialchars($manager));
    $newEntry->addChild('instruction_text', htmlspecialchars($instruction));

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
