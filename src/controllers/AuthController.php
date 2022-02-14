<?php
require_once('src/models/AuthModel.php');
require_once('src/views/JsonView.php');
require_once('src/controllers/Controller.php');

class AuthController extends Controller
{	
	public function __construct()
	{
		parent::__construct();
		$this->model = new AuthModel($this->query, $this->session);
		$this->view = new JsonView();
	}
	
	public function login()
	{
		$input = $this->getJsonData();
		if($input === false or !isset($input["logname"], $input["pass"]))
		{
			//client side validation should have caught it!
			return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
		}
		else
		{
			return $this->view->sendResponse($this->model->login($input["logname"], $input["pass"]));
		}
	}
	
	public function logout()
	{
		return $this->view->sendResponse($this->model->logout());
	}

	public function find()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
	
	public function findAll()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
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
