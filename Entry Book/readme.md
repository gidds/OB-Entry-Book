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

To-Do
1) Fix login issue: mginstruct to show when logged in and stay visible on all pages when logged in, hide login on menu. (Done )
2) Sort instructions by latest entry on mainentry.html, might need to add time and sort by time.
3) Create a secured login database for manager passwords.(See point 7)
4) Acknowledge management entry with user custom password
- highlight + possible popup. 
    - button is added to each entry, but entry xml is not updating on mainentry.php. TODO
5) search entry page - list possibilities
6) management add / remove customer page
7) use Hashed JSON, Local Storage, IndexDB or AUTH Library like AUTH0 and Okta for management logins. 
8) Add seach box to mainentry page to search OB Entries. 

License
This project is licensed under the MIT License.

Note that this is just a rough draft, and you can modify it to fit your specific needs and project details.
