<?php
require_once('src/core/SqlQuery.php');

abstract class Model
{
	protected $query;
	
	public function __construct(SqlQuery $query)
	{
		if($query !== null)
		{
			$this->query = $query;
		}
	}
}

?>
