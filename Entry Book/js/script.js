$(document).ready(function() {
    // Set the refresh interval in milliseconds
    const refreshInterval = 60000; // 60 seconds

    // Function to load entries from XML and update the page
    function loadEntries() {
        $("#entry-container").hide(); // Hide before loading
        $("#loader").show(); // Show loader
        $.ajax({
            type: 'GET',
            url: '../data/entries.xml',
            dataType: 'xml',
            success: function(xml) {
                var $entries = $(xml).find('entry');
                var entriesArray = [];

                $entries.each(function() {
                    var date = $(this).find('date').text().trim() || 'No Date';
                    var obNumber = $(this).find('ob_number').text().trim() || 'No OB Number';
                    var customer = $(this).find('customer').text().trim() || 'No Customer';
                    var obentry = $(this).find('obentry').text().trim() || 'No Entry';

                    entriesArray.push({
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
                            <h3>Entry Details</h3>
                            ${entry.date !== 'No Date' ? `<p><strong>Date:</strong> ${entry.date}</p>` : ''}
                            ${entry.obNumber ? `<p><strong>OB Number:</strong> ${entry.obNumber}</p>` : ''}
                            ${entry.customer ? `<p><strong>Customer:</strong> ${entry.customer}</p>` : ''}
                            ${entry.obentry !== 'No Entry' ? `<p><strong>Entry:</strong> ${entry.obentry}</p>` : ''}
                            <hr /> <!-- Line between entries -->
                        </div>
                    `;
                    $("#entry-container").append(entryHTML);
                });

                $("#loader").hide(); // Hide loader after loading
                $("#entry-container").show(); // Show after loading
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to load the XML file:', textStatus, errorThrown);
                $("#loader").hide(); // Hide loader on error
                $("#entry-container").show(); // Ensure it's shown even on error
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
                var $instructions = $(xml).find('instruction');
                var instructionsArray = [];

                $instructions.each(function() {
                    var id = $(this).attr('id');
                    var date = $(this).find('date').text().trim() || 'No Date';
                    var manager = $(this).find('manager').text().trim() || 'No Manager';
                    var instructionText = $(this).find('instruction_text').text().trim() || 'No Instruction';
                    var datetime = $(this).find('entry_time').text().trim() || 'No Date';
                    var ackop = $(this).find('ackop').text().trim(); // Get the acknowledgment operator

                    instructionsArray.push({
                        id: id,
                        date: date,
                        manager: manager,
                        instructionText: instructionText,
                        datetime: datetime,
                        ackop: ackop
                    });
                });

                // Sort instructions by ID in descending order
                instructionsArray.sort(function(a, b) {
                    return parseInt(b.id, 10) - parseInt(a.id, 10);
                });

                // Clear existing instructions in the container
                $("#instructions-container").empty();

                // Append sorted instructions to the container
                instructionsArray.forEach(function(instruction) {
                    // Create the HTML for each instruction
                    var instructionHTML = `
                        <div class="instruction-entry ${instruction.ackop === 'none' ? 'highlight-red' : ''}" data-id="${instruction.id}" data-ackop="${instruction.ackop}">
                            <h2>Management Instruction</h2>
                            <h3>ID: ${instruction.id}</h3>
                            <p><strong>Date:</strong> ${instruction.date}</p>
                            <p><strong>Manager:</strong> ${instruction.manager}</p>
                            <p><strong>Instruction:</strong> ${instruction.instructionText}</p>
                            <p><strong>Datetime:</strong> ${instruction.datetime}</p>
                            ${instruction.ackop === 'none' ? '<button class="ack-button" data-id="' + instruction.id + '">ACK</button>' : '<p>Acknowledged by: ' + instruction.ackop + '</p>'}
                            <hr />
                        </div>
                    `;

                    // Append the instruction HTML to the container
                    $("#instructions-container").append(instructionHTML);
                });

                // Reapply event handlers for the ACK button after instructions are reloaded
                //applyAckButtonHandlers();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to load the XML file:', textStatus, errorThrown);
            }
        });
    }

    // Initialize loading
    loadEntries(); // Then load entries
    loadInstructions(); // Load instructions 
    

    // Set up interval to reload xml files
    setInterval(function() {
        loadEntries(); // Reload entries only
    }, refreshInterval);
});
