<?php

class DbConnection
{
	public static function getConnection()
	{
		try
		{
			$con = new mysqli("localhost", "chat_app_user", "f83kz82m4PS7nd83GHD3SP51", "chat_app_db");
			if($con === null)
			{
				throw new Exception("Error mysqli instance is null!");
			}
			else
			{
				return $con;
			}
		}
		catch(Exception $e)
		{
			//rethrow exception to parent class
			throw $e;
		}
	}
}

?>
