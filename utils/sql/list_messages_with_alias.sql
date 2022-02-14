SELECT 
messages.id, 
IF(users.alias is NULL, 'Deleted User', users.alias) AS 'alias', 
IF(users2.alias is NULL, 'Deleted User', users2.alias) AS 'alias', 
messages.time, 
messages.text 
FROM 
messages, 
users, 
(SELECT id, alias FROM users) AS users2 
WHERE 
messages.sender_id = users.id 
AND 
messages.receiver_id = users2.id;
