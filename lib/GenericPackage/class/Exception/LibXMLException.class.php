<?php

class LibXMLException extends Exception
{
	public function __construct($argLibXMLErrors, $argCode=NULL, $argPrevius=NULL){
		$msg = "Failed loading XML" . PHP_EOL;
		foreach($argLibXMLErrors as $error) {
			$msg .= "\t" . $error->message . PHP_EOL;
		}
		parent::__construct($msg, $argCode, $argPrevius);
	}	
}

?>