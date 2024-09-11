<h1>Entry Book</h1>
A web application for managing entries with management requests.

Features
Auto-generate unique OB numbers
View and manage entries in a table format
Login functionality for management instructions
Secure storage of manager passwords (pending)
Getting Started
Clone the repository: git clone https://github.com/your-username/gidds.git
Open the index.html file in your web browser to access the application.
File Structure
[css/](cci:7://c:\xampp\htdocs\Entry Book\css:0:0-0:0): Stylesheets for the application
mainentry.css: Styles for the main entry page
[data/](cci:7://c:\xampp\htdocs\Entry Book\data:0:0-0:0): Data files for the application
entries.xml: XML file containing entry data
[js/](cci:7://c:\xampp\htdocs\Entry Book\js:0:0-0:0): JavaScript files for the application
[entryscript.js](cci:4://c:/xampp/htdocs/Entry Book/js/entryscript.js:0:0-16:0): Script for generating OB numbers and managing entries
[php/](cci:7://c:\xampp\htdocs\Entry Book\php:0:0-0:0): PHP files for the application
update_entries.php: Script for updating entry data

<h2>High Priority</h2>
Secure Login Database: Implement a secure database for managing passwords.
Acknowledgment for Management Entry: Add a button to each entry for acknowledgment and highlight or display a popup. Note: XML updates are not yet reflected in mainentry.php.

<h2>In Progress</h2>
Search Functionality: Add a search page to list and manage entries.
Enhanced Login Security: Explore options like Hashed JSON, Local Storage, IndexedDB, or authentication libraries such as Auth0 and Okta for secure management logins.

<h2>Pending</h2>
Manage Customer Entries: Create pages for adding and removing customer entries.
Search Box: Implement a search box on the mainentry page to search OB Entries.

<h2>Completed</h2>
Fix Login Issue: Ensure that mginstruct shows when logged in and remains visible across all pages.
Sort Instructions: Sort instructions by latest entry in mainentry.html. Currently using unique ID auto-increments for sorting management instructions.

License
This project is licensed under the MIT License.

