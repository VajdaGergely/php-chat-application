<?php

class Security
{
	public static function generate_random_number($digits)
	{
		if($digits <= 0)
		{
			return 0;
		}
		else
		{
			$random_value = (string)strval(mt_rand(1,9));
			for($i = 1; $i < $digits; $i++)
			{
				$random_value .= (string)strval(mt_rand(0,9));
			}
			return $random_value;
		}
	}
	
	public static function isPasswordSafe($pass)
	{
		$uppercase = preg_match("/[A-Z]/", $pass);
		$lowercase = preg_match("/[a-z]/", $pass);
		$number = preg_match("/[0-9]/", $pass);
		
		if($uppercase and $lowercase and $number and strlen($pass) >= 8)
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
