<?php
require_once('src/models/UserModel.php');
require_once('src/models/AuthModel.php');
require_once('src/views/JsonView.php');
require_once('src/controllers/Controller.php');

class AccountController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->model = new UserModel($this->query);
		$this->authModel = new AuthModel($this->query, $this->session);
		$this->view = new JsonView();
	}

	public function find()
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
			return $this->view->sendResponse($this->model->find($result["data"])); //result["data"] contains id value
		}
	}
	
	public function findAll()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
	
	public function create()
	{
		$input = $this->getJsonData();
		//intro can be empty
		if($input === false or !isset($input["logname"], $input["pass"], $input["alias"], $input["age"], $input["gender"], $input["intro"]))
		{
			//client side validation should have caught it!
			return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
		}
		else
		{
			$result = $this->authModel->create($input["logname"], $input["pass"]);
			if($result["status"] !== "success")
			{
				return $this->view->sendResponse($result);
			}
			else if($result["data"] === false)
			{
				return $this->view->sendResponse(array("status" => "error", "type" => "error", "message" => "Can't register auth record!"));
			}
			else
			{
				//result["data"] contains userId value
				return $this->view->sendResponse($this->model->create($result["data"], $input["alias"], $input["age"], $input["gender"], $input["intro"]));
			}
		}
	}
	
	public function update()
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
			$input = $this->getJsonData();
			if($input === false or !isset($input["alias"], $input["age"], $input["gender"], $input["intro"]))
			{
				//client side validation should have caught it!
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				//result["data"] contains id value
				return $this->view->sendResponse($this->model->update($input["alias"], $input["age"], $input["gender"], $input["intro"], $result["data"]));
			}
		}
	}
	
	public function delete()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
	
	public function disable()
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
			//disable account
			//filling fields containing personal data with NULL values in the -users- and -auth- table
			//and then forcing logout
			if($this->model->disable($result["data"])["status"] !== "success" or 
				$this->authModel->disable($result["data"])["status"] !== "success" or $this->authModel->logout()["status"] !== "success") //result["data"] contains id value
			{
				return $this->view->sendResponse(array("status" => "error", "type" => "error", "message" => "Disabling account is not successful!"));
			}
			else
			{
				return $this->view->sendResponse(array("status" => "success", "type" => "info", "message" => "Account is disabled successfully!"));
			}
		}
	}
}

?>
