<?php

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/'.PATH_SEPARATOR.dirname(__FILE__).'/testerlib/');

// Unicodeエスケープされた文字列をUTF-8文字列に戻す
if(!function_exists('unicode_encode')) {
	function unicode_encode($str) {
		return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", "encode_callback", $str);
	}
	function encode_callback($matches) {
		$char = mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
		return $char;
	}
}

// 直アクセスを禁じる
if(!(isset($_POST['protocol']) && isset($_POST['server']) && isset($_POST['action']) && isset($_POST['method']) && isset($_POST['language']))){
	// forbidden
	echo 'forbidden';
	exit;
}

set_time_limit(900);
ini_set('memory_limit', '512M');

// 単なるライブラリとしてBCDのcoreを呼ぶ
//require_once (dirname(dirname(__FILE__)).'/core/main.php');
require_once (dirname(__FILE__).'/testerlib/Request2.php');


// XXX libでinitしてるのでそのまま下に処理が書ける

/**
 * ココからメイン
 */
try{

	$serverAdder = $_POST['protocol'] . '://' . $_POST['server'] . '/' . $_POST['action'];
	$method = strtoupper($_POST['method']);
	$language = $_POST['language'];
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	if(isset($_POST['version']) && isset($_POST['device'])){
		$userAgent = str_replace('%version%', $_POST['version'], $_POST['device']);
	}elseif(isset($_POST['device'])){
		$userAgent = str_replace('%version%', '', $_POST['device']);
	}
	$imageScale = 1;
	if(isset($_POST['imagescale']) && strlen($_POST['imagescale']) > 0 && is_numeric($_POST['imagescale'])){
		$imageScale = $_POST['imagescale'];
	}
	$gets = array();
	$posts = array();
	$cookies = array();
	$uploads = array();
	foreach($_POST['key'] as $key => $val){
		if(strlen($val) > 0){
			if('file' == $_POST['type'][$key]){
				if(isset($_FILES['file']['error'][$key])){
					if(UPLOAD_ERR_OK !== $_FILES['file']['error'][$key]){
						echo 'upload error !!!';
						exit;
						break;
					}elseif(isset($_FILES['file']['tmp_name'][$key]) && isset($_FILES['file']['name'][$key])){
						$uploads[] = array('filepath'=>$_FILES['file']['tmp_name'][$key],'filename'=>$_FILES['file']['name'][$key],'formname'=>$_POST['key'][$key]);
					}
				}
			}else{
				if('cookie' == $_POST['type'][$key]){
					$_POST['val'][$key] = rawurlencode($_POST['val'][$key]);
				}
				eval('$'.$_POST['type'][$key].'s[$_POST[\'key\'][$key]] = $_POST[\'val\'][$key];');
			}
		}
	}

	if(count($gets) > 0){
		$serverAdder .= '?'.httpBuildQueryStr($gets,null,'&');
	}

	echo PHP_EOL;
	echo '【Request】'.PHP_EOL;
	echo 'URL: '.$serverAdder.PHP_EOL;
	echo 'Method: '.$method.PHP_EOL;
	echo 'Language: '.$language.PHP_EOL;
	echo 'UserAgent: '.$userAgent.PHP_EOL;
	echo 'X-Image-Scale: '.$imageScale.PHP_EOL;
	echo 'Gets: '.PHP_EOL;
	print_r($gets);
	echo 'Posts: '.PHP_EOL;
	print_r($posts);
	echo 'Cookies: '.PHP_EOL;
	print_r($cookies);
	echo 'Uploads: '.PHP_EOL;
	print_r($uploads);

	$HttpRequest = new HTTP_Request2();
	$HttpRequest->setHeader(array('Accept-Language' => $language, 'User-Agent' => $userAgent, 'X-Image-Scale' => $imageScale));
	$HttpRequest->setUrl($serverAdder);
	// 65秒待つ
	$HttpRequest->setConfig(array('timeout'=>65, 'adapter'=>'HTTP_Request2_Adapter_Curl', 'ssl_verify_peer'=>FALSE, 'ssl_verify_host'=>FALSE));
	eval('$HttpRequest->setMethod(HTTP_Request2::METHOD_'.$method.');');
	if(count($posts) > 0){
		foreach($posts as $keysKey => $keysVal){
			$HttpRequest->addPostParameter($keysKey,$keysVal);
		}
	}
	if(count($cookies) > 0){
		foreach($cookies as $keysKey => $keysVal){
			$HttpRequest->addCookie($keysKey,$keysVal);
		}
	}
	if(count($uploads) > 0){
		for($uploadCnt=0;count($uploads)>$uploadCnt;$uploadCnt++){
			$HttpRequest->addUpload($uploads[$uploadCnt]['formname'],$uploads[$uploadCnt]['filepath'],$uploads[$uploadCnt]['filename']);
		}
	}

	echo PHP_EOL;
	echo '【Response】'.PHP_EOL;
	$Response = $HttpRequest->send();
	//print_r($HttpRequest);
	$statusCode = $Response->getStatus();
	if (200 == $statusCode) {
		echo 'status code: '.$Response->getStatus().PHP_EOL;
		foreach ($Response->getHeader() as $key => $value) {
			echo $key.': '.$value.PHP_EOL;
		}
	} else {
		echo 'Unexpected HTTP status: ' . $statusCode . ' ' .$Response->getReasonPhrase();
	}

	echo 'cookies: ';
	foreach($Response->getCookies() as $key => $child){
		foreach($child as $childkey => $childval){
			echo $key . PATH_SEPARATOR . rawurldecode($childkey) . ' = ' . rawurldecode($childval).PHP_EOL;
		}
		echo PHP_EOL;
	}
	echo PHP_EOL;

	$responseBody = $Response->getBody();
	echo PHP_EOL;
	echo 'response length: '.strlen($responseBody).PHP_EOL;
	echo 'response body: '.PHP_EOL;
	// XXX ホントはjson限定では無いのだけど
	echo unicode_encode($responseBody).PHP_EOL;
	echo '※実際の出力結果はunicodeにdecodeされて出力されています！！'.PHP_EOL;

} catch (HTTP_Request2_Exception $e) {
	die($e->getMessage());
} catch (Exception $e) {
	die($e->getMessage());
}

?>