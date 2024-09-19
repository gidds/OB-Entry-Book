$(document).ready(function() {
    // Highlight unacknowledged instructions in red
    $('.instruction-entry').each(function() {
        const ackop = $(this).data('ackop'); // Assuming ackop is passed as a data attribute
        if (ackop === 'none') {
            // Highlight the unacknowledged entry
            $(this).css({
                'background-color': 'red',
                'color': 'white'
            });
        }
    });

    // Handle ACK button click
    $('.ack-button').on('click', function() {
        const $instruction = $(this).closest('.instruction-entry');
        const instructionId = $instruction.data('id'); // Assuming each entry has an ID
        const ackop = $instruction.data('ackop');

        if (ackop !== 'none') {
            alert('This instruction has already been acknowledged.');
            return;
        }

        // Show password prompt
        const password = prompt('Enter 4-digit password:');
        if (password && /^[0-9]{4}$/.test(password)) {
            // Show operator name prompt if password is correct
            const operator = prompt('Enter your operator name:');
            if (operator) {
                $.ajax({
                    url: '../php/acknowledge_instruction.php',
                    type: 'POST',
                    data: {
                        id: instructionId,
                        password: password,
                        operator: operator // Send operator name with the request
                    },
                    success: function(response) {
                        if (response === 'success') {
                            // Update instruction style and save operator name
                            $instruction.css({
                                'background-color': 'white',
                                'color': 'black'
                            }).data('ackop', operator); // Update ackop value

                            alert('Instruction acknowledged successfully.');
                        } else {
                            alert('Incorrect password or acknowledgment failed.');
                        }
                    }
                });
            } else {
                alert('Please enter a valid operator name.');
            }
        } else {
            alert('Please enter a valid 4-digit password.');
        }
    });
});
