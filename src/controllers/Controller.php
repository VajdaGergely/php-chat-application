<?php
require_once('src/core/DbConnection.php');
require_once('src/core/Logger.php');
require_once('src/core/SqlQuery.php');
require_once('src/core/Session.php');

abstract class Controller
{
	protected $query;
	protected $session;
	protected $model;
	protected $view;
	protected $logger;
	
	public function __construct()
	{
		try
		{
			$this->query = new SqlQuery();
		}
		catch(Exception $e)
		{
			//rethrow exception to the creator of instance
			throw $e;
		}
		
		$this->session = new Session($this->query);
		
		//child has to implement model and view objects
		$this->model = null; 
		$this->view = null; 
	}
	
	
	
	/* because of unknown parameters child classes has to implement create() and update() methods */
	 
	/* child classes should override methods that they don't want to use with some "empty like" code */
	
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
			$input = $this->getJsonData();
			if($input === false or !isset($input["id"]))
			{
				//client side validation should have caught it!
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				return $this->view->sendResponse($this->model->find((string)$input["id"]));
			}
		}
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
			return $this->view->sendResponse($this->model->findAll());
		}
	}
	
	public function delete()
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
			if($input === false or !isset($input["id"]))
			{
				//client side validation should have caught it!
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				return $this->view->sendResponse($this->model->delete((string)$input["id"]));
			}
		}
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
			$input = $this->getJsonData();
			if($input === false or !isset($input["id"]))
			{
				//client side validation should have caught it!
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				return $this->view->sendResponse($this->model->disable((string)$input["id"]));
			}
		}
	}
	
	protected function getJsonData()
	{
		$input = json_decode(file_get_contents('php://input'),true); //get json data from HTTP request body to assoc array
		if(!isset($input) or gettype($input) !== "array")
		{
			return false;
		}
		else
		{
			return $input;
		}
	}
	
	public function isAuthenticated()
	{
		return $this->session->isExists();
	}
}

?>
