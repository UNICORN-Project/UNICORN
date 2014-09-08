<?php

/**
 * HTTP_Request2操作のラッパークラス
 * @author 
 */
class GenericHttpRequest{
	public static function requestToPostMethod($argURL, $argParams=NULL, $argFiles=NULL, $argTimeOut=60){
		$HttpRequestObj = new HTTP_Request2();
		$urls = parse_url($argURL);
		if(isset($urls["user"]) && isset($urls["pass"])){
			$HttpRequestObj->setAuth($urls["user"], $urls["pass"]);
		}
		if(isset($urls["port"])){
			$url = $urls["scheme"] . '://' . $urls["host"] . ':' . $urls["port"];
		}
		else{
			$url = $urls["scheme"] . '://' . $urls["host"];
		}
		if(isset($urls["path"])){
			$url .= $urls["path"];
		}
		$HttpRequestObj->setUrl($url);
		$HttpRequestObj->setMethod(HTTP_Request2::METHOD_POST);
		if('https' === $urls["scheme"]){
			$HttpRequestObj->setConfig(array('connect_timeout' => $argTimeOut, 'timeout' => $argTimeOut, 'adapter'=>'HTTP_Request2_Adapter_Curl', 'ssl_verify_peer'=>FALSE, 'ssl_verify_host'=>FALSE));
		}
		else{
			$HttpRequestObj->setConfig(array('connect_timeout' => $argTimeOut, 'timeout' => $argTimeOut));
		}

		if(is_array($argParams)){
			foreach($argParams as $key => $value){
				$HttpRequestObj->addPostParameter($key,$value);
			}
		}

		// ファイルをアップロードする場合
		if(is_array($argFiles)){
			foreach($argFiles as $key => $value){
				$HttpRequestObj->addUpload($key,$value);
			}
		}

		// リクエストを送信
		$response = $HttpRequestObj->send();
		// レスポンスのボディ部を表示
		return $response;
	}
}

?>