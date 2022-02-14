--delete event
DROP EVENT delete_inactive_sessions;

--delete database
SELECT 'DELETING DATABASE AND TABLES...' AS 'TASK 1';
DROP DATABASE IF EXISTS chat_app_db;

--delete user
SELECT 'DELETING USER AND PRIVILEGES...' AS 'TASK 2';
DROP USER IF EXISTS 'chat_app_user'@'localhost';
