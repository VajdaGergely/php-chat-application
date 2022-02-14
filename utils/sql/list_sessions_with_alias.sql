SELECT sessions.id, users.alias, sessions.last_action_time
FROM sessions JOIN users ON sessions.user_id=users.id;
