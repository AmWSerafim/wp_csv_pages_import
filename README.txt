Few steps for make correct import

1. Create DB backup from LIVE site.
2. Import DB to temp DB on your local server.
3. Open import.php file and change: DB access, CSV file path and site URL.
4. Save import.php file.
5. Run import.php file on your local with key "run=true" ( http://domain.com/import.php?run=true ).
6. Crate DB dump from local server.
7. Restore DB on LIVE ver from dump.