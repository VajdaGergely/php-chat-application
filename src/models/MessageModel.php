<?php
require_once('src/models/Model.php');

class MessageModel extends Model
{
	public function getConversation($userId, $id)
	{
		if(!$this->isIdValid($userId) or !$this->isIdValid($id))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			//selecting rows from messages between two users
			//users.alias is shown and is fetched from users table instead of showing messages.id
			//alias of deleted users will be replaced with the 'deleted user' string
			$sqlString = "SELECT messages.id, t_sender.alias AS 'sender', t_receiver.alias AS 'receiver', messages.time, messages.text
				FROM
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_sender,
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_receiver,
				messages
				WHERE sender_id=? AND receiver_id=?
				UNION
				SELECT t_sender.alias AS 'sender', t_receiver.alias AS 'receiver', messages.time, messages.text
				FROM
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_sender,
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_receiver,
				messages
				WHERE sender_id=? AND receiver_id=?
				ORDER BY time;
			";
			return $this->query->execute($sqlString, array($userId, $id, $userId, $id, $id, $userId, $id, $userId));
		}
	}
	
	public function getNewMessages($userId, $partnerId, $last_msg_id)
	{
		if(!$this->isIdValid($userId) or !$this->isIdValid($id))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			//selecting rows from messages between two users
			//users.alias is shown and is fetched from users table instead of showing messages.id
			//alias of deleted users will be replaced with the 'deleted user' string
			$sqlString = "SELECT messages.id, t_sender.alias AS 'sender', t_receiver.alias AS 'receiver', messages.time, messages.text
				FROM
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_sender,
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_receiver,
				messages
				WHERE sender_id=? AND receiver_id=? AND messages.id>? 
				UNION
				SELECT t_sender.alias AS 'sender', t_receiver.alias AS 'receiver', messages.time, messages.text
				FROM
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_sender,
				(SELECT IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users WHERE id=?) AS t_receiver,
				messages
				WHERE sender_id=? AND receiver_id=? AND messages.id>? 
				ORDER BY time;
			";
			return $this->query->execute($sqlString, array($userId, $partnerId, $userId, $partnerId, $last_msg_id, $partnerId, $userId, $partnerId, $userId, $last_msg_id));
		}
	}
	
	public function getConversationList($id)
	{
		if(!$this->isIdValid($id))
		{
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			$sqlString = "SELECT id, IF(alias is NULL, 'Deleted User', alias) AS 'alias' FROM users 
				WHERE id IN 
				(SELECT DISTINCT sender_id As 'id' FROM messages
				WHERE receiver_id=?
				UNION
				SELECT DISTINCT receiver_id As 'id' FROM messages
				WHERE sender_id=?)
				ORDER BY alias;
      ";
			return $this->query->execute($sqlString, array($id, $userId));
		}
	}
	
	public function create($senderId, $receiverId, $text)
	{
		if(!$this->isIdValid($senderId) or 
			!$this->isIdValid($receiverId) or !$this->isTextValid($text))
		{
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			return $this->query->execute("INSERT INTO messages (sender_id, receiver_id, text) VALUES (?, ?, ?);", array($senderId, $receiverId, $text));
		}
	}
	
	protected function isIdValid($id)
	{
		if(!empty($id) and gettype($id) === "string" and strlen($id) === 16 and is_numeric($id) and substr($id, 0, 1) !== "0")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function isTextValid($text)
	{
		if(gettype($text) === "string" and strlen($text) > 0 and strlen($text) <= 500)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>
