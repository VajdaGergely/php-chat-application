--inserting test records...
USE chat_app_db;
SELECT 'CREATING TEST RECORDS' AS 'TASK 3';
INSERT INTO auth (id, logname, pass) VALUES 
(4539405823458230, 'peter', 'b109f3bbbc244eb82441917ed06d618b9008dd09b3befd1b5e07394c706a8bb980b1d7785e5976ec049b46df5f1326af5a2ea6d103fd07c95385ffab0cacbc86'), 
(5832472374983479, 'nicol', 'fa585d89c851dd338a70dcf535aa2a92fee7836dd6aff1226583e88e0996293f16bc009c652826e0fc5c706695a03cddce372f139eff4d13959da6f1f5d3eabe'), 
(8934746783405123, 'andrew', '0dd3e512642c97ca3f747f9a76e374fbda73f9292823c0313be9d78add7cdd8f72235af0c553dd26797e78e1854edee0ae002f8aba074b066dfce1af114e32f8'), 
(4132554208721303, 'martha', 'bda73f9292823c0313be9d78add7c3d4d788f372a2b2d3c55a3f0c553dd26797e78e1854edee0ae002fd72384787a348387cd43ba937b48a6d1bcd8979ef3292');

INSERT INTO sessions (id, user_id) VALUES 
(9367482634959237, 5832472374983479), 
(1957787523674592, 8934746783405123);

INSERT INTO logs (category, file, function, message) VALUES 
('app', 'file1', 'func1', 'Unsuccessful login with username -adam-.'), 
('app', 'file2', 'func2', 'Unsuccessful login with username -adam-.'), 
('http', 'file3', 'func3', 'Request is not valid.'), 
('sql', 'file4', 'func4', 'SQL query: SELECT * FROM auth;');

INSERT INTO users (id, alias, age, gender, intro) VALUES 
(4539405823458230, 'pete3424', 21, 1, "Nothing to say about me!"), 
(5832472374983479, 'blck_cat', 18, 0, "."), 
(8934746783405123, 'andeyBoi', 21, 1, "I'm the boss. Don't mess with me.");

INSERT INTO messages (sender_id, receiver_id, text) VALUES 
(4539405823458230, 8934746783405123, "Hey!"), 
(4539405823458230, 8934746783405123, "Hey! What's up?"), 
(4539405823458230, 8934746783405123, "Helooooo."), 
(8934746783405123, 4539405823458230, "Leave me alone..."),
(5832472374983479, 4539405823458230, "Msg1"),
(5832472374983479, 4539405823458230, "Msg2"),
(5832472374983479, 4539405823458230, "Msg3"),
(4539405823458230, 5832472374983479, "Resp1"),
(4539405823458230, 5832472374983479, "Resp2");
