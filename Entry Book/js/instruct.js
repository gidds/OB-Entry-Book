document.addEventListener('DOMContentLoaded', function () {
    // Set the current date
    const dateInput = document.getElementById('instruction-date');
    const today = new Date().toISOString().split('T')[0]; // Format as yyyy-mm-dd
    dateInput.value = today;

    // Populate the manager dropdown
    const managerDropdown = document.getElementById('manager-select');
    const managers = ['Manager 1', 'Manager 2', 'Manager 3', 'Manager 4'];
    managers.forEach(function (manager) {
        const option = document.createElement('option');
        option.value = manager;
        option.textContent = manager;
        managerDropdown.appendChild(option);
    });

    // Handle form submission
    document.getElementById('instruct-form').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        const instructionData = {
            date: dateInput.value,
            manager: managerDropdown.value,
            instruction: document.getElementById('instruction-text').value,
        };

        // Send data to PHP script
        fetch('../php/add_instruction.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(instructionData).toString()
        })
        .then(response => response.text())
        .then(result => {
            if (result === 'success') {
                alert('Instruction added successfully!');
                document.getElementById('instruct-form').reset();
                dateInput.value = today; // Reset the date
            } else {
                alert('Error adding instruction: ' + result);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the instruction.');
        });
    });
});
