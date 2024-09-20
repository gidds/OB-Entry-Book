<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];

    $auth_file = '../data/auth.xml';
    $auth_xml = simplexml_load_file($auth_file);

    // Check password and operator name validity
    $operatorName = '';
    foreach ($auth_xml->operator as $operator) {
        if ((string)$operator->password === $password) {
            $operatorName = (string)$operator->name;
            break;
        }
    }

    if ($operatorName !== '') {
        $xml_file = '../data/instructions.xml';
        $xml = simplexml_load_file($xml_file);

        // Find the instruction by ID
        foreach ($xml->instruction as $instruction) {
            if ((string)$instruction['id'] === $id) {
                // Update the ackop field with the operator name
                $instruction->ackop = $operatorName;

                // Remove 'new' attribute if it exists
                if (isset($instruction['new'])) {
                    unset($instruction['new']);
                }

                // Save the updated XML file
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
