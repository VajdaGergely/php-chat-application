<?php
require_once('src/views/BaseView.php');

class JsonView extends BaseView
{
	public function sendResponse($input)
	{
		$result = array("status" => $input["status"]);
		if($input["status"] === "success" and $input["data"] !== null)
		{
			$result["data"] = $input["data"];
		}
		//debugging...
		//->orig return value
			//return json_encode($result);
		//->debug return value
		return json_encode($input);
	}
}

?>
