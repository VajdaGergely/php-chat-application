<?php
require_once('src/models/Model.php');
require_once('src/core/Session.php');
require_once('src/core/Security.php');

class AuthModel extends Model
{
	protected $session;
	
	public function __construct(SqlQuery $query, Session $session)
	{
		parent::__construct($query);
		if($session !== null)
		{
			$this->session = $session;
		}
	}
	
	public function login($logname, $pass)
	{
		if(!$this->isLognameValid($logname) or !$this->isPassValid($pass))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty credentials get caught on server side!");
		}
		else
		{
			//check credentials
			$result = $this->query->execute("SELECT id FROM auth WHERE logname=? AND pass= BINARY ? AND is_active=1;", array($logname, hash("sha512", $pass, false)));
			if($result["status"] !== "success")
			{
				return $result;
			}
			else if($result["data"] === null)
			{
				//bad credentials
				return array("status" => "fail", "type" => "warning", "message" => "Bad credentials. Login unsuccessful with logname '$logname'!");
			}
			else
			{
				//good credentials
				
				//starting session and returning succes, failure or error status
				$userId = (string)$result["data"][0]["id"];
				$result = $this->session->start($userId);
				if($result["status"] === "success")
				{
					return array("status" => "success", "type" => "info", "message" => "Successful login with logname '$logname'!");
				}
				else
				{
					return $result;
				}
			}
		}
	}
	
	public function logout()
	{
		//closing session, and returning succes, failure or error status
		$result = $this->session->close();
		if($result["status"] === "success")
		{
			return array("status" => "success", "type" => "info", "message" => "Successful logout!");
		}
		else if($result["status"] === "fail")
		{
			return array("status" => "fail", "type" => "warning", "message" => $result["message"] . " | Logout unsuccessful!");
		}
		else
		{
			return $result;
		}
	}
	
	public function create($logname, $pass)
	{
		if(!$this->isLognameValid($logname) or !$this->isPassValid($pass))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty credentials get caught on server side!");
		}
		else
		{
			//is logname reserved
			$result = $this->query->execute("SELECT id FROM auth WHERE logname=?;", array($logname));
			if($result["status"] !== "success")
			{
				return $result;
			}
			else if($result["data"] !== null)
			{
				//logname is reserved
				return array("status" => "fail", "type" => "warning", "message" => "Logname '$logname' is reserved!");
			}
			else
			{
				//logname is not reserved
				
				//generating new user id
				$userId = $this->generateNewUserId();
				if($userId === false)
				{
					return array("status" => "error", "type" => "error", "message" => "Can't generate new user id!");
				}
				else
				{
					//insert new auth record
					$result = $this->query->execute("INSERT INTO auth (id, logname, pass) VALUES (?, ?, ?);", array($userId, $logname, hash("sha512", $pass, false)));
					if($result["status"] !== "success")
					{
						return $result;
					}
					else
					{
						//new auth record inserted, sending back generated user id
						return array("status" => "success", "type" => "info", "message" => "Registration successful with logname '$logname'!", "data" => $userId);
					}
				}
			}
		}
	}

	public function disable($id)
	{
		if(!$this->isIdValid($id))
		{
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty id parameter get caught on server side!");
		}
		else
		{
			//fill personal data fields with NULL values
			$result = $this->query->execute("UPDATE auth SET is_active=0, logname=NULL, pass=NULL WHERE id=?;", array($id));
			if($result["status"] !== "success")
			{
				return $result;
			}
			else
			{
				return array("status" => "success", "type" => "info", "message" => "Auth record successfully disabled with id '$id'!");
			}
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
	
	protected function isLognameValid($logname)
	{
		if(!empty($logname) and gettype($logname) === "string" and strlen($logname) <= 10)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function isPassValid($pass)
	{
		if(!empty($pass) and gettype($pass) === "string" and Security::isPasswordSafe($pass))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function generateNewUserId()
	{
		$id = null;
		$counter = 0;
		do
		{
			//generate new id
			$id = Security::generate_random_number(16);
			$result = $this->query->execute("SELECT id FROM auth WHERE id=?;", array($id));
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
