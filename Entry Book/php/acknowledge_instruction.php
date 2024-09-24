<?php

// Prevent browser caching for this page
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0
header('Expires: 0'); // Proxies

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = trim($_POST['password']); // Trim whitespace from password

    // Load authentication data
    $auth_file = '../data/auth.xml';
    $auth_xml = simplexml_load_file($auth_file);
    if (!$auth_xml) {
        echo 'Failed to load auth.xml';
        exit();
    } 

    // Check password and operator name validity
    $operatorName = null;
    foreach ($auth_xml->operator as $operator) {
        if ((string)$operator->password === $password) {
            $operatorName = (string)$operator->name;
            break;
        }
    }
    

    if ($operatorName !== null) {
        $xml_file = '../data/instructions.xml';
        $instructionsXml = simplexml_load_file($xml_file);

        // Find the instruction by ID
        foreach ($instructionsXml->instruction as $instruction) {
            if ((string)$instruction['id'] === $id) {
                // Update the ackop field with the operator name
                $instruction->ackop = $operatorName;

                // Remove 'new' attribute if it exists
                if (isset($instruction['new'])) {
                    unset($instruction['new']);
                }

                // Save the updated XML file
                $instructionsXml->asXML($xml_file);
                echo 'success:' . $operatorName; // Return operator name with success
                exit();
            }
        }

        echo 'Entry not found';
    } else {
        echo 'Invalid password';
    }
} else {
    echo 'Invalid request';
}
?>
