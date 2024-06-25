# phpJsonBuilder

phpJsonBuilder is a simple PHP-based JSON database management system that allows you to create, read, update, and delete JSON data through a web interface and API.

## Features

- Create and manage multiple JSON files as a table.
- Add, edit, and delete records.
- Search functionality.
- RESTful API for data manipulation.
- Web interface for easy data management.

## Files

1. `api.php`: Handles API requests and routes them to appropriate actions
2. `DatabaseBuilder.php`: Core class for managing JSON data operations
3. `index.html`: Web interface for interacting with the JSON data

## Installation

1. Clone this repository to your web server directory:
   ```
   git clone https://github.com/LitalHayIL/phpJsonBuilder.git
   ```

2. Ensure your web server has PHP installed and configured.

3. Use the `databases` folder in the project root directory to store JSON files.

## Usage

### Web Interface

1. Open `index.html` in a web browser.
2. Use the interface to create tables, add records, and manage data.

### API Endpoints

- `GET api.php?action=getTables`: List all available tables
- `GET api.php?action=getTableStructure&table={tableName}`: Get structure of a specific table
- `POST api.php?action=createTable&table={tableName}`: Create a new table
- `GET api.php?action=get&table={tableName}`: Get all records from a table
- `GET api.php?action=find&table={tableName}&id={id}`: Find a specific record by ID
- `POST api.php?action=insert&table={tableName}`: Insert a new record
- `POST api.php?action=update&table={tableName}&id={id}`: Update an existing record
- `GET api.php?action=delete&table={tableName}&id={id}`: Delete a record
- `POST api.php?action=addField&table={tableName}`: Add a new field to a table
- `GET api.php?action=search&table={tableName}&term={searchTerm}`: Search records in a table

## Requirements

- PHP 7.4 or higher
- Web server (e.g., Apache, Nginx)
- Write permissions for the `databases` folder
