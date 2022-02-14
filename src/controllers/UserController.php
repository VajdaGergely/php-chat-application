<?php
require_once('src/models/UserModel.php');
require_once('src/views/JsonView.php');
require_once('src/controllers/Controller.php');

class UserController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->model = new UserModel($this->query);
		$this->view = new JsonView();
	}
	
	public function findAll()
	{
		$result = $this->isAuthenticated();
		if($result["status"] !== "success")
		{
			return $this->view->sendResponse($result);
		}
		else if($result["data"] === false)
		{
			return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Authentication needed!"));
		}
		else
		{
			return $this->view->sendResponse($this->model->findAll($result["data"]));
		}
	}
	
	public function delete()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
	
	public function disable()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
}

?>
