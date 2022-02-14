<?php
require_once('src/controllers/WrongRequestController.php');
require_once('src/core/DbConnection.php');
require_once('src/core/Logger.php');
require_once('src/controllers/AuthController.php');
require_once('src/controllers/UserController.php');
require_once('src/controllers/AccountController.php');
require_once('src/controllers/MessageController.php');
require_once('utils/php/update_session_timestamp.php');

/* API valid uri's after "localhost/index.php/"
 * 
 * (empty)
 * /login
 * /logout
 * /get/user/
 * /get/user/list
 * /get/account/
 * /create/account/
 * /edit/account/
 * /delete/account/
 * /get/message/
 * /get/message/list
 * /create/message/
 */

try
{
	//init output text (HTML content or JSON response)
	$output = null;

	//get url parts
	$urlParts = explode('/', $_SERVER["REQUEST_URI"]);
	array_shift($urlParts);


	//update session timestamp to avoid the automatic deletion mechnism of inactive sessions
	updateSessionTimestamp();

	if($_SERVER["REQUEST_METHOD"] === "GET" and empty($urlParts[1]))
	{
		//we simple send back the whole html content and all client side functionality within
		
		//read the content of index.html
		$output = file_get_contents("index.html");
		if($output === false)
		{
			header("HTTP/1.1 404 Not Found");
			die();
		}
		
		//adding headers to response
		header("Content-Type: text/html; charset=UTF-8");
	}
	else if($_SERVER["REQUEST_METHOD"] === "POST" and !empty($urlParts[1]))
	{
		//we send only json data to ajax request through api methods
		header("Content-Type: application/json");
		
		switch($urlParts[1]) //action
		{
			case "login":
				$controller = new AuthController();
				$output = $controller->login();
				break;
			case "logout":
				$controller = new AuthController();
				$output = $controller->logout();
				break;
			default:
				if(empty($urlParts[2])) //controller
				{
					$controller = new WrongRequestController();
					$output = $controller->wrongRequest();
				}
				else
				{
					$subAction = null;
					if(!empty($urlParts[3])) //sub-action
					{
						$subAction = $urlParts[3];
					}
					$output = map_request_to_controller_method($urlParts[1], $urlParts[2], $subAction);
				}
				break;
		}
		
		//if($controller !== null)
		if(isset($controller))
		{
			//closing $controller to avoid use of two opened sql connection in one request
			//(logger use separate sql connection)
			unset($controller);
		}
		
		//logging controller messages
		if(isset($output["message"]))
		{
			///////////////////
			//later we want to filter messages that we want to log!!!!
			//probably the Security class will help us with it!
			///////////////////
			Logger::makeLog($output["type"], $output["message"]);
		}
	}
	else
	{
		//esetleg itt a pontos request type-ot is logolhatnank, illetve ip cim, stb...
		//csak DOS tamadas aldozatava ne valljunk...
		Logger::makeLog("warning", "Invalid HTTP request type!");
		header("HTTP/1.1 404 Not Found");
		die();
	}
}
catch(Exception $e)
{
	$output = json_encode(array("status" => "error", "message" => "An error occured! Request can't be completed!"));
}
finally
{
	//put json content to HTTP response message
	print $output;
}


function map_request_to_controller_method($action, $controllerName, $subAction)
{
	try
	{
		switch($controllerName)
		{
			case "user":
				$controller = new UserController();
				switch($action)
				{
					case "get":
						if($subAction === null)
						{
							return $controller->find();
						}
						else if($subAction === "list")
						{
							return $controller->findAll();
						}
						else
						{
							$controller = new WrongRequestController();
							return $controller->wrongRequest();
						}
					default:
						$controller = new WrongRequestController();
						return $controller->wrongRequest();
				}
				break;
			case "account":
				$controller = new AccountController();
				switch($action)
				{
					case "get":
						return $controller->find();
					case "create":
						return $controller->create();
					case "edit":
						return $controller->update();
					case "delete":
						//we dont want to totally delete the records, just want to null the personal data fields
						return $controller->disable(); //calling disable() instead of delete()
					default:
						$controller = new WrongRequestController();
						return $controller->wrongRequest();
				}
				break;
			case "message":
				$controller = new MessageController();
				switch($action)
				{			
					case "get":
						if($subAction === null)
						{
							return $controller->getConversation();
						}
						else if($subAction === "list")
						{
							return $controller->getConversationList();
						}
						else if($subAction === "new")
						{
							return $controller->getNewMessages();
						}
						else
						{
							$controller = new WrongRequestController();
							return $controller->wrongRequest();
						}
					case "create":
						return $controller->create();
					default:
						$controller = new WrongRequestController();
						return $controller->wrongRequest();
				}
				break;
			default:
				$controller = new WrongRequestController();
				return $controller->wrongRequest();
		}
	}
	catch(Exception $e)
	{
		//rethrow exception to caller code
		throw $e;
	}
}
?>
