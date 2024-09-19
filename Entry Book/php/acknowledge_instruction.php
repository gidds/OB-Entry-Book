<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];
    $operatorName = $_POST['operator'];

    $auth_file = '../data/auth.xml';
    $auth_xml = simplexml_load_file($auth_file);

    // Check password and operator name validity
    $valid_operator = false;
    foreach ($auth_xml->operators->operator as $operator) {
        if ((string)$operator->password === $password && (string)$operator->name === $operatorName) {
            $valid_operator = true;
            break;
        }
    }

    if ($valid_operator) {
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
        echo 'Invalid password or operator';
    }
} else {
    echo 'Invalid request';
}

?>
