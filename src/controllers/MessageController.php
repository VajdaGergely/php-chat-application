<?php
require_once('src/models/MessageModel.php');
require_once('src/views/JsonView.php');
require_once('src/controllers/Controller.php');

class MessageController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->model = new MessageModel($this->query);
		$this->view = new JsonView();
	}
	
	public function getConversation()
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
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				//result["data"] contains userId value
				return $this->view->sendResponse($this->model->getConversation($result["data"], $input["id"]));
			}
		}
	}
	
	public function getNewMessages()
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
			if($input === false or !isset($input["partner_id"], $input["last_msg_id"]))
			{
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				//result["data"] contains userId value
				return $this->view->sendResponse($this->model->getConversation($result["data"], $input["partner_id"], $input["last_msg_id"]));
			}
		}
	}
	
	public function getConversationList()
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
			return $this->view->sendResponse($this->model->getConversationList($result["data"])); //result["data"] contains userId value
		}
	}
	
	public function find()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
	
	public function findAll()
	{
		return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Invalid controller action"));
	}
	
	public function create()
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
			if($input === false or !isset($input["receiver_id"], $input["text"]))
			{
				return $this->view->sendResponse(array("status" => "fail", "type" => "warning", "message" => "Bad request! Invalid JSON input data!"));
			}
			else
			{
				return $this->view->sendResponse($this->model->create($result["data"], $input["receiver_id"], $input["text"])); //result["data"] contains senderId value
			}
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
