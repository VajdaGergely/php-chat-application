<?php
require_once('src/core/DbConnection.php');

class TestLogger
{
	static public function makeLog(string $category, string $message)
	{
		$dbCon = new DbConnection();
		$con = $dbCon->getConnection();
		$stmt = $con->prepare("INSERT INTO logs (category, file, function, message) VALUES (?, ?, ?, ?);");
		//we store the calling function name and the corresponding source file's name in the log entry
		$backtraceFile = substr(debug_backtrace()[0]["file"], 13, 100);
		$backtraceFunction = (!empty(debug_backtrace()[1]["function"]) ? debug_backtrace()[1]["function"] : null);
		$stmt->bind_param("ssss", $category, $backtraceFile, $backtraceFunction, $message);
		$stmt->execute();
		$con->close();
	}
	
	static public function makeSqlErrorAndWarningLogs($stmt)
	{
		//logging mysql generated errors and warnings
		if($stmt->error_list != null)
		{
			foreach($stmt->error_list as $error)
			{
				self::makeLog("[mysql generated error] " . $error["error"]);
			}
		}	
		$warnings = $stmt->get_warnings();
		if($warnings !== false)
		{
			{
				self::makeLog("[mysql generated warning] " . $warnings->message);
			} while ($warnings->next());
		}
	}
	
	static public function makeSqlRequestLog($sqlString, $param_types, $param_array)
	{
		$logStr = "Sql query: ";
		if($param_types === null and $param_array === null)
		{
			$logStr .= $sqlString; //logging the whole query
		}
		else
		{
			$logStr .= $this->build_query_str_for_log($sqlString, $param_types, $param_array);
		}
		self::makeLog($logStr);
	}
	
	protected function build_query_str_for_log($sqlString, $param_types, $parameters)
	{
		if(substr_count($sqlString, "?") != count($parameters) or strlen($param_types) != count($parameters))
		{
			return false; //invalid input parameters
		}
		else
		{
			$log_str = "";
			$sqlString_parts = explode("?", $sqlString);
			for($i = 0; $i < count($parameters); $i++)
			{
				$log_str .= $sqlString_parts[$i];
				if($param_types[$i] == 'i')
				{
					$log_str .= $parameters[$i];
				}
				else if($param_types[$i] == 's')
				{
					$log_str .= "'" . $parameters[$i] . "'";
				}
			}
			$log_str .= $sqlString_parts[$i]; //put the final part of sql statement after the final parameter
			return $log_str;
		}
	}
}
?>
