// entryscript.js

$(document).ready(function() {
    // Get the current date
    var currentDate = new Date();
    var day = currentDate.getDate();
    var month = currentDate.getMonth() + 1; // Month is zero-based, so add 1
    var year = currentDate.getFullYear();

    // Populate the date field with the current date in the format YYYY-MM-DD
    $('#date').val(year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day);

    // Fetch the last OB number from the XML file
    $.ajax({
        type: 'GET',
        url: '../data/entries.xml',
        dataType: 'xml',
        success: function(xml) {
            var lastObNumber = 0;
            var lastMonth = 0;
            var lastYear = 0;

            // Iterate through the entries and find the last OB number
            $(xml).find('entry').each(function() {
                var obNumber = $(this).find('ob_number').text().trim();
                var parts = obNumber.split('\\');
                if (parts.length === 3) {
                    var number = parseInt(parts[0]);
                    var obMonth = parseInt(parts[1]);
                    var obYear = parseInt(parts[2]);

                    if (obYear === year && obMonth === month) {
                        lastObNumber = Math.max(lastObNumber, number);
                    }

                    lastMonth = obMonth;
                    lastYear = obYear;
                }
            });

            // Increment the OB number if month and year are the same, otherwise reset
            var nextObNumber = (lastMonth === month && lastYear === year) ? lastObNumber + 1 : 1;

            // Generate the OB number in the format number\month\year
            var obNumberValue = nextObNumber + '\\' + month + '\\' + year;
            $('#ob_number').val(obNumberValue);

            // Store the current month and year in localStorage
            localStorage.setItem('month', month.toString());
            localStorage.setItem('year', year.toString());
        },
        error: function() {
            console.error('Failed to fetch entries.xml');
            // Handle error appropriately
        }
    });

    function toggleSubmitButton() {
        var entryText = $('#obentry').val().trim();
        var isValid = entryText !== '';

        $('#submit-btn').prop('disabled', !isValid);
    }

    // Initialize submit button state
    toggleSubmitButton();

    // Enable/Disable the submit button based on form input
    $('#add-entry-form').on('input', function() {
        toggleSubmitButton();
    });

    // Increment the counter and update localStorage on form submission
    $('#add-entry-form').submit(function(event) {
        event.preventDefault();
        var $submitButton = $('#submit-btn'); // Get the submit button
        $submitButton.prop('disabled', true); // Disable the button
    
        var date = $('#date').val();
        var obNumber = $('#ob_number').val();
        var customer = $('#customer').val();
        var obentry = $('#obentry').val(); // Updated to match the textarea ID
    
        // Send the new entry data to the server-side script
        $.ajax({
            type: 'POST',
            url: '../php/update_entries.php',
            data: {
                date: date,
                ob_number: obNumber,
                customer: customer,
                obentry: obentry // Send as 'obentry' to match the XML structure
            },
            success: function() {
                console.log('Entry added successfully!');
                // Optionally reset the form here
                $('#add-entry-form')[0].reset();
                // Re-enable the submit button
                $submitButton.prop('disabled', false);
                // Redirect to MainEntry.html
                window.location.href = '../html/MainEntry.html';
            },
            error: function(xhr, status, error) {
                console.error('Error adding entry: ' + error);
                // Re-enable the submit button if there was an error
                $submitButton.prop('disabled', false);
            }
        });
    });
    
});
