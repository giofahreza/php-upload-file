Create an application with file upload functionality.

Project Specifications:

1. Convert the form with the following input fields into valid HTML:
    - File input (image/*) (file selection)
    - Text input (alternate text)

2. Establish a database with a table to store images, having the following columns:
    - ID
    - Path (file path)
    - Alt (alternate text)

3. Develop a class for file uploads, which includes the following tasks:
    - Alternative text validation
    - File type validation
    - File size validation
    - Generate a date-coded path (e.g., 31/12/2022)
    - Rename files to avoid duplicates (overwriting)
    - Upload files to the date-coded path

4. Build a database connection using PDO.

5. Store relative file paths and alternate text as new entries in the database table.

6. If the upload is successful, output a JSON string containing all information about the new database entry:
    - Include the content type as Application/JSON in the response header.
    - Return HTTP status code 201.

7. If the upload fails, output a JSON string with error messages related to the file upload:
    - Include the content type as Application/JSON in the response header.
    - Return HTTP status code 422.
