# CyberCell-CDR-Management

# Uploading and Searching Project (Large datafiles like CDR)

This project allows users to upload Excel and CSV files, create databases, and perform searches within the databases. It provides a web interface for easy interaction with the functionality.

## Features

- Upload Excel and CSV Files: Users can choose and upload Excel and CSV files to be processed.
- Create Databases: Users can either select an existing database or create a new one for storing the uploaded data.
- Table Creation: The project automatically creates a table in the selected database to store the data from the uploaded files.
- Data Insertion: The data from the Excel and CSV files is inserted into the corresponding table in the database.
- Searching: Users can perform searches within the selected database and table based on multiple conditions.

## Prerequisites

To run this project locally, you need to have the following:

- PHP (version 7.0 or higher)
- MySQL Server
- PhpSpreadsheet library (included in the project's `vendor` directory)

## Getting Started

1. Clone the project repository to your local machine.
2. Import the database structure by executing the SQL script provided in the `database_structure.sql` file.
3. Configure the database connection settings in the `upload.php` and `search.php` files.
4. Start a local web server (e.g., using XAMPP or WAMP).
5. Open the project in your web browser by accessing the appropriate URL (e.g., `http://localhost/uploading-and-searching`).

## Usage

1. Upload File:
   - Choose an Excel or CSV file to upload using the "Choose File" button.
   - Select an existing database from the dropdown or enter a new database name to create a new one.
   - Click the "Upload" button to start the upload process.
   - A success or error message will be displayed upon completion.

2. Search Database:
   - Select the desired database from the dropdown.
   - Choose one or multiple columns to search within from the dropdown.
   - Enter the desired keywords for each selected column.
   - Click the "Search" button to perform the search.
   - The results will be displayed on the next page.

## Troubleshooting

- If you encounter any errors during the upload process, ensure that the database connection settings are correct and the required PHP and MySQL extensions are enabled.
- If the web interface appears broken or the layout is not as expected, check the CSS file (`excel.css`) and ensure that it is properly linked to the HTML file (`excel.php`).

