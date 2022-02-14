<?php
require_once('src/models/Model.php');

class UserModel extends Model
{		
	public function find($id)
	{
		if(!$this->isIdValid($id))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			return $this->query->execute("SELECT id, alias, age, gender, intro FROM users WHERE id=? AND is_active=1;", array($id));
		}
	}
	
	public function findAll($id)
	{
		//list all user except the requestor
		return $this->query->execute("SELECT id, alias FROM users WHERE is_active=1 AND id<>?;", array($id));
	}
	
	public function create($id, $alias, $age, $gender, $intro)
	{
		if(!$this->isIdValid($id) or !$this->isAliasValid($alias) or 
			!$this->isAgeValid($age) or !$this->isGenderValid($gender) or !$this->isIntroValid($intro))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			//gender has to be "i" - integer type, because it is stored as binary in db
			return $this->query->execute("INSERT INTO users (id, alias, age, gender, intro) VALUES (?, ?, ?, ?, ?);", 
				array($id, $alias, $age, $gender, $intro));
		}
	}

	public function update($alias, $age, $gender, $intro, $id)
	{
		if(!$this->isIdValid($id) or 
			!$this->isAliasValid($alias) or !$this->isAgeValid($age) or 
			!$this->isGenderValid($gender) or !$this->isIntroValid($intro))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			return $this->query->execute("UPDATE users SET alias=?, age=?, gender=?, intro=? WHERE id=? AND is_active=1;", 
				array($alias, $age, $gender, $intro, $id));
		}
	}
	
	public function disable($id)
	{
		if(!$this->isIdValid($id))
		{
			//client side validation should have caught it!
			return array("status" => "fail", "type" => "warning", "message" => "Invalid or empty parameters get caught on server side!");
		}
		else
		{
			return $this->query->execute("UPDATE users SET is_active=0, alias=NULL, age=NULL, gender=NULL, intro=NULL WHERE id=?;", 
				array($id));
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
	
	protected function isAliasValid($alias)
	{
		if(!empty($alias) and gettype($alias) === "string" and strlen($alias) <= 20)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function isAgeValid($age)
	{
		if(!empty($age) and is_numeric($age) and $age > 0 and $age < 120)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function isGenderValid($gender)
	{
		if(is_numeric($gender) and ($gender === "0" or $gender === "1"))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function isIntroValid($intro)
	{
		if(gettype($intro) === "string" and strlen($intro) <= 500)
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
