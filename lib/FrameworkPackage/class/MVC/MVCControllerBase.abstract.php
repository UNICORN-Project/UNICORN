<?php

abstract class MVCControllerBase implements MVCController {

	public $controlerClassName = "";
	public $httpStatus = 200;
	public $outputType = "html";
	public $requestMethod = "GET";
	public $restResource = '';
	public $jsonUnescapedUnicode = true;

	public $deviceType = "PC";
	public $appVersion = "1.0.0";
	public $appleReviewd = false;
	public $mustAppVersioned = false;
	
	public function execute(){
		return FALSE;
	}
}

?>