$(document).ready(function() {
  // Highlight new instructions in red
  $('.instruction-entry').each(function() {
      const isNew = $(this).data('new'); // Assuming you have a data attribute to mark new instructions
      if (isNew) {
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

      // Show password prompt
      const password = prompt('Enter 4-digit password:');
      if (password && /^[0-9]{4}$/.test(password)) {
          $.ajax({
              url: '../php/acknowledge_instruction.php',
              type: 'POST',
              data: {
                  id: instructionId,
                  password: password
              },
              success: function(response) {
                  if (response === 'success') {
                      // Update instruction style and save operator name
                      $instruction.css({
                          'background-color': 'white',
                          'color': 'black'
                      }).data('new', false); // Mark as acknowledged

                      // Optionally update the DOM to reflect changes
                      // E.g., show a message or refresh the list
                  } else {
                      alert('Incorrect password.');
                  }
              }
          });
      } else {
          alert('Please enter a valid 4-digit password.');
      }
  });
});
