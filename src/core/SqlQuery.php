<?php
require_once('DbConnection.php');

//....tmp
require_once('src/core/Logger.php');
//....tmp

/* Data Access Object with prepared statements */

class SqlQuery
{
	protected $con;
	
	public function __construct()
	{
		try
		{
			$this->con = DbConnection::getConnection();
		}
		catch(Exception $e)
		{
			//rethrow exception to parent class
			throw $e;
		}
	}

	public function __destruct()
	{
		if($this->con !== null)
		{
			$this->con->close();
		}
	}
	
	public function execute(string $sqlString, ?array $parameters)
	{
		try
		{
			//make sql query
			$stmt = $this->con->prepare($sqlString);
			if(!empty($parameters))
			{	
				$stmt->bind_param(str_repeat("s", count($parameters)), ...$parameters);
			}
			$stmt->execute();
			
			//...tmp
			$tmp_value = $sqlString;
			foreach($parameters as $parameter)
			{
				$tmp_value .= "'" . $parameter . "', ";
			}
			Logger::makeLog("sql test", $tmp_value);
			//...tmp
			
			//check result
			if(in_array(substr($sqlString, 0, 6), array("INSERT", "UPDATE", "DELETE")))
			{
				$affectedRows = $stmt->affected_rows;
				$stmt->close();
				if(isset($affectedRows) and $affectedRows !== -1)
				{
					return array("status" => "success", "data" => $affectedRows);
				}
				else
				{
					return array("status" => "error", "type" => "error", "message" => "Sql error! No affected rows!");
				}
			}
			else if(substr($sqlString, 0, 6) === "SELECT")
			{
				$result = $stmt->get_result();
				$stmt->close();
				if(!isset($result) or !isset($result->num_rows))
				{
					return array("status" => "error", "type" => "error", "message" => "Sql error! No num rows!");
				}
				else if($result->num_rows === 0)
				{
					return array("status" => "success");
				}
				else if($result->num_rows > 0)
				{
					return array("status" => "success", "data" => $result->fetch_all(MYSQLI_ASSOC));
				}
				else
				{
					return array("status" => "error", "type" => "error", "message" => "Sql error! Bad value of num rows!");
				}
			}
		}
		catch(Exception $e)
		{
			if($stmt !== null)
			{
				$stmt->close();
			}
			
			if($con !== null)
			{
				$this->con->close();
			}
			
			unset($e);
			return array("status" => "error", "type" => "error", "message" => "An exception catched! | " . $e.getMessage());
		}
	}
}

?>
