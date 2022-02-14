--creating database tables
SELECT 'CREATING DATABASE AND TABLES...' AS 'TASK 1';
CREATE DATABASE IF NOT EXISTS chat_app_db;
USE chat_app_db;

CREATE TABLE IF NOT EXISTS auth (
	id BIGINT UNSIGNED NOT NULL, 
	logname VARCHAR(10) UNIQUE, 
	pass CHAR(128),
	is_active TINYINT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (id));

CREATE TABLE IF NOT EXISTS sessions (
	id BIGINT UNSIGNED NOT NULL, 
	user_id BIGINT UNSIGNED NOT NULL UNIQUE,
	last_action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	FOREIGN KEY (user_id) REFERENCES auth(id));
		
CREATE TABLE IF NOT EXISTS logs (
	id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, 
	category VARCHAR(10) NOT NULL, 
	time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
	file VARCHAR(200) NOT NULL,
	function VARCHAR(50),
	message VARCHAR(500) NOT NULL,
	PRIMARY KEY (id));

CREATE TABLE IF NOT EXISTS users (
	id BIGINT UNSIGNED NOT NULL UNIQUE, 
	alias VARCHAR(20) UNIQUE,
	age TINYINT UNSIGNED,
	gender TINYINT(1),
	intro VARCHAR(500),
	is_active TINYINT(1) NOT NULL DEFAULT 1,
	FOREIGN KEY (id) REFERENCES auth(id));

CREATE TABLE IF NOT EXISTS messages (
	id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	sender_id BIGINT UNSIGNED NOT NULL,
	receiver_id BIGINT UNSIGNED NOT NULL,
	time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	text VARCHAR(500) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (sender_id) REFERENCES users(id),
	FOREIGN KEY (receiver_id) REFERENCES users(id));

--creating user and setting privileges
SELECT 'CREATING USER PRIVILEGES...' AS 'TASK 2';
CREATE USER IF NOT EXISTS 'chat_app_user'@'localhost' IDENTIFIED BY PASSWORD '*7FB377AEFFDB0338D1C21904BD1E290565F68F27';
FLUSH PRIVILEGES;
GRANT SELECT ON *.* TO 'chat_app_user'@'localhost';
GRANT ALL PRIVILEGES ON `chat_app_db`.* TO 'chat_app_user'@'localhost';
FLUSH PRIVILEGES;

--modify root pwd for security and access reasons
ALTER USER 'root'@'localhost' IDENTIFIED BY PASSWORD '*7A41A70AD989FEB8CC633D8EDE85F9A08FCB598F';

--set mysql query logs (log_ouput value is the 'table' string!!!! not a table name!!!
SELECT 'SET SQL QUERY LOGGING ON...' AS 'TASK 4';
SET global general_log = 1;
SET global log_output = 'table';

--set event, that delete every session after 20 minutes inactivity
CREATE EVENT delete_inactive_sessions 
ON SCHEDULE
EVERY 60 SECOND
DO
DELETE FROM sessions WHERE last_action_time < NOW() - INTERVAL 20 MINUTE;

--turn on event scheduler
SET GLOBAL event_scheduler=ON;
