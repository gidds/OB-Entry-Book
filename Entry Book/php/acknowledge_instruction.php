<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // Load passwords and operators XML
    $auth_file = '../data/auth.xml';
    $auth_xml = simplexml_load_file($auth_file);

    // Check password validity
    $valid_password = false;
    foreach ($auth_xml->passwords->password as $pwd) {
        if ((string)$pwd === $password) {
            $valid_password = true;
            break;
        }
    }

    if ($valid_password) {
        $xml_file = '../data/instructions.xml';
        $xml = simplexml_load_file($xml_file);

        // Find the entry by ID
        foreach ($xml->entry as $entry) {
            if ((string)$entry['id'] === $id) {
                // Add operator name
                $operator = $_SESSION['username'] ?? 'Unknown'; // Replace with actual operator name if available
                $entry->addChild('acknowledged_by', $operator);

                // Remove 'new' attribute
                unset($entry['new']);

                $xml->asXML($xml_file);
                echo 'success';
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
