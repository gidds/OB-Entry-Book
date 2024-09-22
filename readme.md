Entry Book

A web application for managing entries with management requests.
Features

    Auto-generate unique OB numbers
    View and manage entries in a table format
    Login functionality for management instructions
    Secure storage of manager passwords (pending)

High Priority

    Secure Login Database: Implement a secure database for managing passwords.
    Acknowledgment for Management Entry:
        Password shows incorrect after sending correct password, and updates the xml as expected. To see why the error comes up - to be tesed with other browsers and systems for broader perspective. 

In Progress

    Search Functionality: Add a search page to list and manage entries.
    Enhanced Login Security: Explore options like Hashed JSON, Local Storage, IndexedDB, or authentication libraries such as Auth0 and Okta for secure management logins.

Pending

    Manage Customer Entries: Create pages for adding and removing customer entries.
    Search Box: Implement a search box on the mainentry page to search OB Entries.

Completed

    Fix Login Issue: Ensure that mginstruct shows when logged in and remains visible across all pages.
    Sort Instructions: Sort instructions by the latest entry in mainentry.html. Currently using unique ID auto-increments for sorting management instructions.
    ack button added with highlight of each new un-acknowledged entries. 

Next Steps

    Update acknowledge_instruction.php:
        Implement logic to validate passwords from the XML file.
        Update XML with operator name upon acknowledgment.

    Update entryack.js:
        Add functionality to show a password prompt.
        Handle acknowledgment button clicks and update instruction status.

    Verify Instruction Highlighting:
        Ensure new instructions are highlighted red.
        Confirm that acknowledgment changes the background to white with black text.

    Test XML Updates:
        Check that acknowledgment data is correctly written to the XML file.
        Ensure XML changes are reflected in the mainentry.php interface.

License

This project is licensed under the MIT License.