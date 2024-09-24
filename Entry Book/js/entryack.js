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

    // Function to bind ACK button click events
    function bindAckButtonEvents() {
        $('.ack-button').off('click').on('click', function() {
            const $instruction = $(this).closest('.instruction-entry');
            const instructionId = $instruction.data('id');
            const ackop = $instruction.data('ackop');
            console.info('ACK button clicked for ID: ', instructionId);

            if (ackop !== 'none') {
                alert('This instruction has already been acknowledged.');
                return;
            }

            const password = prompt('Enter 4-digit password:');
            console.info('Password Entered');

            if (password && /^[0-9]{4}$/.test(password)) {
                //console.info('Sending password using Ajax');
                $('#loader').show(); // Show loader

                $.ajax({
                    url: '../php/acknowledge_instruction.php',
                    type: 'POST',
                    data: {
                        id: instructionId,
                        password: password
                    },
                    success: function(response) {
                        //console.info('ACK response received');
                        $('#loader').hide(); // Hide loader
                        
                        if (response.includes('success:')) {
                            const operatorName = response.split(':')[1];
                            $instruction.css({
                                'background-color': 'white',
                                'color': 'black'
                            }).data('ackop', operatorName);
                            //Alert window to see if instruction is acknowledged
                            //alert('Instruction acknowledged successfully.');

                            // Reload instructions
                            loadInstructions(); // Ensure loadInstructions updates the content
                        } else {
                            alert('Incorrect password or acknowledgment failed.');
                        }
                    },
                    error: function(xhr, status, error) {
                        //console.error('Error during the ACK request:', error);
                        $('#loader').hide();
                        alert('An error occurred. Please try again.');
                    }
                });
            } else {
                alert('Please enter a valid 4-digit password.');
            }
        });
    }

    // Function to load and display instructions (with cache busting)
    function loadInstructions() {
        $.ajax({
            url: `../php/MainEntry.php?t=${new Date().getTime()}`, // Cache-busting with timestamp
            type: 'GET',
            success: function(response) {
                $('#instructions-container').html(response);
                //console.info('Instructions container updated');

                // Re-bind the event handler after content is loaded
                bindAckButtonEvents();
            },
            error: function(xhr, status, error) {
                //console.error('Error while loading instructions:', error);
                alert('Failed to load instructions. Please try again.');
            }
        });
    }

    // Initial binding of ACK button events
    bindAckButtonEvents();
// Load instructions
    loadInstructions();
});
