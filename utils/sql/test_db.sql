
--testing if database created with the appropriate tables
SELECT 'DATABASES AND TABLES' AS 'TEST RESULT 1';
SHOW DATABASES;
USE chat_app_db;
SHOW TABLES;

SELECT 'AUTH' AS 'TABLE 1';
SHOW COLUMNS FROM auth;
SELECT 'SESSIONS' AS 'TABLE 2';
SHOW COLUMNS FROM sessions;
SELECT 'LOGS' AS 'TABLE 3';
SHOW COLUMNS FROM logs;
SELECT 'USERS' AS 'TABLE 4';
SHOW COLUMNS FROM users;
SELECT 'MESSAGES' AS 'TABLE 5';
SHOW COLUMNS FROM messages;

--testing if users and permissions OK
SELECT 'USERS AND PRIVILEGES' AS 'TEST RESULT 2';
SELECT host, user, password FROM mysql.user;
SHOW GRANTS FOR 'chat_app_user'@'localhost';

--testing if test records created successfully
SELECT 'TEST RECORDS' AS 'TEST RESULT 3';
SELECT 'AUTH' AS 'TABLE 1';
SELECT * FROM auth;
SELECT 'SESSIONS' AS 'TABLE 2';
SELECT * FROM sessions;
SELECT 'LOGS' AS 'TABLE 3';
SELECT * FROM logs;
SELECT 'USERS' AS 'TABLE 4';
SELECT * FROM users;
SELECT 'MESSAGES' AS 'TABLE 5';
SELECT * FROM messages;

--testing events
SELECT 'EVENTS' AS 'TEST RESULT 4';
SHOW CREATE EVENT delete_inactive_sessions;

--testing mysql logging functionality
SELECT 'LOGGING' AS 'TEST RESULT 5';
SELECT * FROM mysql.general_log ORDER BY event_time DESC LIMIT 5;
