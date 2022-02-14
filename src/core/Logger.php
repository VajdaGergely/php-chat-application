<?php
require_once('DbConnection.php');

/* Logger that store log messages with timestamp in db table */


/*
 * category lehet
 * 
 * error, warning, info
 * Incident esetleg vagy risk vagy security
 * 
 */

/*** SINGLE MESSAGES VERSION OF LOGGER!!!! ***/

class Logger
{
	public static function makeLog($category, $message)
	{
		try
		{
			//init mysqli instance
			$con = DbConnection::getConnection();
			
			//read log entries from buffer and insert them with category,
			//caller function metainformation (function name and source file name), and log message
			$stmt = $con->prepare("INSERT INTO logs (category, file, function, message) VALUES (?, ?, ?, ?);");
			$backtraceFile = substr(debug_backtrace()[0]["file"], 13, 100);
			$backtraceFunction = (!empty(debug_backtrace()[1]["function"]) ? debug_backtrace()[1]["function"] : null);
			$stmt->bind_param("ssss", $category, $backtraceFile, $backtraceFunction, $message);
			$stmt->execute();
		}
		catch(Exception $e)
		{
			unset($e);
		}
		finally
		{
			$stmt->close();
			$con->close();
		}
	}
	
	/*** THE VERSION OF LOGGER WITH BUFFER FOR LOG MESSAGES AND WRITE THEM ALL AT ONCE!!!! ***/
	
	//protected static $buffer;
	
	/*
	public static function addLog(string $category, string $message)
	{
		//add new log entry to buffer
		$this->buffer[] = array($category, $message);
	}
	*/
	/*
	public static function saveLogs()
	{
		try
		{
			$category = null;
			$message = null;
			
			//init mysqli instance
			$con = DbConnection::getConnection();
			
			//read log entries from buffer and insert them with category,
			//caller function metainformation (function name and source file name), and log message
			$stmt = $con->prepare("INSERT INTO logs (category, file, function, message) VALUES (?, ?, ?, ?);");
			$backtraceFile = substr(debug_backtrace()[0]["file"], 13, 100);
			$backtraceFunction = (!empty(debug_backtrace()[1]["function"]) ? debug_backtrace()[1]["function"] : null);
			$stmt->bind_param("ssss", $category, $backtraceFile, $backtraceFunction, $message);
			
			foreach($buffer as $row)
			{
				$category = $row[0];
				$message = $row[1];
				$stmt->execute();
			}
		}
		catch(Exception $e)
		{
			unset($e);
		}
		finally
		{
			$stmt->close();
			$con->close();
		}
	}
	*/
}

?>
