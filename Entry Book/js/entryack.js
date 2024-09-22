$(document).ready(function() {
    // Highlight unacknowledged instructions in red
    $('.instruction-entry').each(function() {
        const ackop = $(this).data('ackop');
        if (ackop === 'none') {
            $(this).css({
                'background-color': 'red',
                'color': 'white'
            });
        }
    });

    // Handle ACK button click
    $('.ack-button').on('click', function() {
        const $instruction = $(this).closest('.instruction-entry');
        const instructionId = $instruction.data('id');
        const ackop = $instruction.data('ackop');

        if (ackop !== 'none') {
            alert('This instruction has already been acknowledged.');
            return;
        }

        const password = prompt('Enter 4-digit password:');

        if (password && /^[0-9]{4}$/.test(password)) {
            $('#loader').show(); // Show loader

            $.ajax({
                url: '../php/acknowledge_instruction.php',
                type: 'POST',
                data: {
                    id: instructionId,
                    password: password
                },
                success: function(response) {
                    $('#loader').hide(); // Hide loader
                    if (response.startsWith('success:')) {
                        const operatorName = response.split(':')[1];
                        $instruction.css({
                            'background-color': 'white',
                            'color': 'black'
                        }).data('ackop', operatorName);

                        alert('Instruction acknowledged successfully.');

                        $.ajax({
                            url: '../php/MainEntry.php',
                            type: 'GET',
                            success: function(updatedResponse) {
                                $('#instructions-container').html(updatedResponse);
                            }
                        });
                    } else {
                        alert('Incorrect password or acknowledgment failed.');
                    }
                },
                error: function() {
                    $('#loader').hide(); // Hide loader
                    alert('An error occurred while processing your request.');
                }
            });
        } else {
            alert('Please enter a valid 4-digit password.');
        }
    });
});
