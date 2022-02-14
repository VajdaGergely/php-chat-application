<?php
require_once('src/views/JsonView.php');

class WrongRequestController
{
	protected $view;
	
	public function __construct()
	{
		$this->view = new JsonView();
	}
	
	public function wrongRequest()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid request!"));
	}
}

?>
