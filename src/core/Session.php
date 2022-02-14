<?php
require_once('SqlQuery.php');
require_once('Security.php');

/* Session handling with cookies and database entries	*/

class Session
{
	protected $query;
	
	public function __construct(SqlQuery $query)
	{	
		if($query != null)
		{
			$this->query = $query;
		}
	}
	
	public function start($userId)
	{
		if(!$this->isUserIdValid($userId))
		{
			return array("status" => "fail", "type" => "warning", "message" => "Invalid user id!");
		}
		else
		{
			//check if session exists
			$result = $this->query->execute("SELECT id FROM sessions WHERE user_id=?;", array($userId));
			if($result["status"] !== "success")
			{
				return $result;
			}
			else if($result["data"] !== null)
			{
				//session already exists
				$sessionId = (string)$result["data"][0]["id"];
			}
			else
			{
				//session not exists yet
				
				//create new session token
				$sessionId = $this->generateNewSessionId();
				if($sessionId === false)
				{
					return array("status" => "error", "type" => "error", "message" => "Can't generate new session id!");
				}
				
				//create new session record
				$result = $this->query->execute("INSERT INTO sessions (id, user_id) VALUES (?, ?);", array($sessionId, $userId));
				if($result["status"] !== "success")
				{
					return $result;
				}
			}	
			
			//in case of no errors create session cookie
			$this->createSessionCookie($sessionId);
			return array("status" => "success");
		}
	}
	
	public function close()
	{
		$sessionId = $this->getSessionIdFromCookie();
		if($sessionId === null)
		{
			return array("status" => "fail", "type" => "warning", "message" => "Invalid session id!");
		}
		else
		{
			//delete session cookie
			$this->deleteSessionCookie();
			
			//delete session record from db
			$result = $this->query->execute("DELETE FROM sessions WHERE id=?;", array($sessionId));
			if($result["status"] !== "success")
			{
				return $result;
			}
			else
			{
				//closed session successfully
				return array("status" => "success");
			}
		}
	}
	
	public function isExists()
	{
		$sessionId = $this->getSessionIdFromCookie();
		if($sessionId === null)
		{
			return array("status" => "fail", "type" => "warning", "message" => "Invalid session id!");
		}
		else
		{
			//search session record in db
			$result = $this->query->execute("SELECT user_id FROM sessions WHERE id=?;", array($sessionId));
			if($result["status"] !== "success")
			{
				return $result;
			}
			else if($result["data"] === null)
			{
				//no active session
				return array("status" => "success", "data" => false);
			}
			else
			{
				//active session found
				return array("status" => "success", "data" => (string)$result["data"][0]["user_id"]);
			}
		}
	}
	
	protected function getSessionIdFromCookie()
	{
		if(isset($_COOKIE["session"]) and $this->isSessionIdValid($_COOKIE["session"]))
		{
			return $_COOKIE["session"];
		}
		else
		{
			return null;
		}
	}
	
	protected function isUserIdValid($userId)
	{
		if(!empty($userId) and gettype($userId) === "string" and strlen($userId) === 16 and is_numeric($userId) and substr($userId, 0, 1) !== "0")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function isSessionIdValid($sessionId)
	{
		if(!empty($sessionId) and gettype($sessionId) === "string" and strlen($sessionId) === 16 and is_numeric($sessionId) and substr($sessionId, 0, 1) !== "0")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function createSessionCookie($sessionId)
	{
		setcookie("session", "$sessionId");
	}
	
	protected function deleteSessionCookie()
	{
		setcookie("session", "", 0);
	}
	
	protected function generateNewSessionId()
	{
		$id = null;
		$counter = 0;
		do
		{
			//generate new id
			$id = Security::generate_random_number(16);
			$result = $this->query->execute("SELECT id FROM sessions WHERE id=?;", array($id));
			if($result["status"] !== "success" or $counter > 50)
			{
				//in case of sql error or after 50 times trying returning false
				return false;
			}
			$counter++;
			//regenerate id if already used
		} while ($result["data"] !== null);
		return $id;
	}
}

?>
