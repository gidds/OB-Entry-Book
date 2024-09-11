// script.js
$(document).ready(function() {
    // Set the refresh interval in milliseconds
    const refreshInterval = 60000; // 60 seconds

    // Function to update the countdown display
    function updateCountdown() {
        const countdownElement = document.getElementById("countdown");
        const seconds = Math.floor((refreshInterval - (Date.now() % refreshInterval)) / 1000);
        countdownElement.textContent = `Next refresh in ${seconds} seconds`;
    }

    // Function to load entries from XML and update the page
    function loadEntries() {
        $.ajax({
            type: 'GET',
            url: '../data/entries.xml',
            dataType: 'xml',
            
            success: function(xml) {
                var $entries = $(xml).find('entry');
                var entriesArray = [];

                $entries.each(function() {
                    var id = $(this).attr('id');
                    var date = $(this).find('date').text().trim() || 'No Date';
                    var obNumber = $(this).find('ob_number').text().trim();
                    var customer = $(this).find('customer').text().trim();
                    var obentry = $(this).find('obentry').text().trim() || 'No Entry'; // Default text for empty fields

                    entriesArray.push({
                        id: id,
                        date: date,
                        obNumber: obNumber,
                        customer: customer,
                        obentry: obentry
                    });
                });

                // Sort entries by OB number in descending order
                entriesArray.sort(function(a, b) {
                    return parseInt(b.obNumber, 10) - parseInt(a.obNumber, 10);
                });

                // Clear existing entries in the container
                $("#entry-container").empty();

                // Append sorted entries to the container
                entriesArray.forEach(function(entry) {
                    var entryHTML = `
                        <div>
                            <h2>OB Entry</h2>
                            <h3>Entry ${entry.id}</h3>
                            ${entry.date !== 'No Date' ? `<p><strong>Date:</strong> ${entry.date}</p>` : ''}
                            ${entry.obNumber ? `<p><strong>OB Number:</strong> ${entry.obNumber}</p>` : ''}
                            ${entry.customer ? `<p><strong>Customer:</strong> ${entry.customer}</p>` : ''}
                            ${entry.obentry !== 'No Entry' ? `<p><strong>Entry:</strong> ${entry.obentry}</p>` : ''} <!-- Handle empty 'obentry' -->
                            <hr /> <!-- Line between entries -->
                        </div>
                    `;
                    $("#entry-container").append(entryHTML);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to load the XML file:', textStatus, errorThrown);
                console.log('jqXHR:', jqXHR);
            }
        });
    }

    // Function to load instructions from XML and update the page
    function loadInstructions() {
        $.ajax({
            type: 'GET',
            url: '../data/instructions.xml',
            dataType: 'xml',
            success: function(xml) {
                // Debugging: Log the loaded XML to the console
                console.log('XML Loaded:', xml);
    
                var $instructions = $(xml).find('instruction');
                var instructionsArray = [];
    
                $instructions.each(function() {
                    var id = $(this).attr('id');
                    var date = $(this).find('date').text().trim() || 'No Date';
                    var manager = $(this).find('manager').text().trim() || 'No Manager';
                    var instructionText = $(this).find('instruction_text').text().trim() || 'No Instruction';
                    var datetime = $(this).find('entry_time').text().trim() || 'No Date';
    
                    instructionsArray.push({
                        id: id,
                        date: date,
                        manager: manager,
                        instructionText: instructionText,
                        datetime: datetime
                    });
                });
    
                 // Sort instructions by ID in descending order
                instructionsArray.sort(function(a, b) {
                    return parseInt(b.id, 10) - parseInt(a.id, 10);
                });
    
                // Clear existing instructions in the container
                $("#instructions-container").empty();
    
                // Append instructions to the container
                instructionsArray.forEach(function(instruction) {
                    var instructionHTML = `
                        <div>
                            <h2>Management Instruction</h2>
                            <h3>ID: ${instruction.id}</h3>
                            <p><strong>Date:</strong> ${instruction.date}</p>
                            <p><strong>Manager:</strong> ${instruction.manager}</p>
                            <p><strong>Instruction:</strong> ${instruction.instructionText}</p>
                            <p><strong>Datetime:</strong> ${instruction.datetime}</p>
                            <hr /> <!-- Line between instructions -->
                        </div>
                    `;
                    $("#instructions-container").append(instructionHTML);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to load the XML file:', textStatus, errorThrown);
                console.log('jqXHR:', jqXHR);
            }
        });
    }
    
    // Initialize countdown and entries loading
    updateCountdown();
    loadEntries();
    loadInstructions(); // Load instructions

    // Set up interval to update countdown every second
    setInterval(updateCountdown, 1000);

    // Set up interval to reload entries and instructions
    setInterval(function() {
        loadEntries();
        loadInstructions();
    }, refreshInterval);
});
