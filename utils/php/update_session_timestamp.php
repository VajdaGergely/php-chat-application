<?php
require_once('src/core/DbConnection.php');

//update the last_action_time field in sessions table record with id (from session cookie)
//due to avoid record deletion by "delete_inactive_sessions" event
function updateSessionTimestamp()
{
	if(!empty($_COOKIE["session"]) and in_array(gettype($_COOKIE["session"]), array("integer", "double", "string"), true))
	{
		$dbConnection = new DbConnection();
		$con = $dbConnection->getConnection();
		$stmt = $con->prepare("UPDATE sessions SET last_action_time=NOW() WHERE id=?;");
		$stmt->bind_param("s", $_COOKIE["session"]);
		$stmt->execute();
		$stmt->close();
		$con->close();
	}
}

?>
