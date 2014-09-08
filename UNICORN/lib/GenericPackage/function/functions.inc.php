<?php


/**
 * グローバル関数定義
 */


/**
 * 変数をグローバルスコープ化するグローバル関数のコア
 */
function ParamStore($argMode, & $argment, $argKey = "") {

	static $paramStore;

	if (false === is_array($paramStore)) {

		// 消失してしまった可能性があるので、Backupから取得してみる
		$paramStore = & BackupParamStore("get", $paramStore);

		// Backupも空なら初期化する
		if (false === is_array($paramStore)) {

			// 初期化
			$paramStore = array ();

		}
	}

	// 初期化
	if ("init" === $argMode) {
		$paramStore = array ();
	}

	// 追加
	elseif ("set" === $argMode) {
		$paramStore[$argKey] = & $argment;
	}
	else {

		if (true === isset ($paramStore[$argKey])) {

			// 返却
			if ("get" === $argMode) {
				return $paramStore[$argKey];
			}

			// 削除
			if ("remove" === $argMode) {
				unset ($paramStore[$argKey]);
			}

		}
		else {
			return null;
		}
	}

	// Backup
	BackupParamStore("set", $paramStore);

	return true;

}

/**
 * グローバルスコープ化さてた変数をバックアップする(PHP4のstaticが消失するバグに対応するための措置)
 */
function BackupParamStore($argMode, & $argments) {

	static $backupParamStore;

	if ("get" === $argMode) {
		return $backupParamStore;
	}

	if ("set" === $argMode) {
		$backupParamStore = & $argments;
	}

	return true;

}

/**
 * グローバルスコープ化したい変数をセットする
 */
function setAttribute2ParamStore($argKey, & $argVal) {
	ParamStore("set", $argVal, $argKey);
}

/**
 * グローバルスコープ化した変数をゲットする
 */
function getAttribute4ParamStore($argKey) {
	$dummyArgment = "";
	return ParamStore("get", $dummyArgment, $argKey);
}

/**
 * グローバルスコープ化した変数を削除する
 */
function removeAttribute4ParamStore($argKey) {
	$dummyArgment = "";
	return ParamStore("remove", $dummyArgment, $argKey);
}

/**
 * setAttribute2ParamStoreのエイリアス
 */
function setAttributeToParamStore($argKey, & $argVal) {
	setAttribute2ParamStore($argKey, $argVal);
}
/**
 * getAttribute4ParamStoreのエイリアス
 */
function getAttributeForParamStore($argKey) {
	return getAttribute4ParamStore($argKey);
}
/**
 * removeAttribute4ParamStoreのエイリアス
 */
function removeAttributeForParamStore($argKey) {
	return removeAttribute4ParamStore($argKey);
}

/**
 * グローバルスコープ化変数群である、ParamStoreを初期化する
 */
function initParamStore() {
	$argVal = "";
	$dummyArgment = null;
	return ParamStore("init", $dummyArgment);
}

/**
 * resultSetをXML文字列に変換
 */
function convertObjectToXML($data, $level = 0) {

	$thisFunctionNameStr = __FUNCTION__ ;

	$lineCnt = 0;
	$xmlStr = "";
	while ( list ($key, $value) = each($data)) {
		$attr = "";
		// DB要素をXMLするための拡張
		if (is_array($value) && true == array_key_exists("type", $value) && true == array_key_exists("size", $value) && true == array_key_exists("value", $value)) {
			if (!is_array($value["value"]) && !is_object($value["value"])) {
				$attr = " type=\"".$value["type"]."\" size=\"".$value["size"]."\"";
			}
			$value = $value["value"];
		}
		// 複数エラー要素をXMLするための拡張
		elseif (is_array($value) && true == array_key_exists("id", $value) && true == array_key_exists("code", $value)) {
			if (!is_array($value["value"]) && !is_object($value["value"])) {
				$attr = " id=\"".$value["id"]."\" type=\"string\" code=\"".$value["code"]."\"";
			}
			$key = "error";
			$value = $value["value"];
		}

		if (is_numeric($key) && true == (is_array($value) || is_object($value))) {
			$tag = "list";
			$attr = " id=\"".$key."\"";
		}
		else {
			$tag = $key;
		}

		if (0 < $lineCnt) {
			$xmlStr .= PHP_EOL;
		}
		$xmlStr .= str_repeat("\t", $level)."<".$tag;
		if ("" != $attr) {
			$xmlStr .= $attr;
		}
		if (is_null($value)) {
			$xmlStr .= " />";
		}
		elseif (!is_array($value) && !is_object($value)) {
			$xmlStr .= ">";
			if (preg_match("/[^0-9a-zA-Z\.\_\-\:\x20]/", $value)) {
				$xmlStr .= "<![CDATA[".$value."]]>";
			}
			else {
				$xmlStr .= $value;
			}
			$xmlStr .= "</".$tag.">";
		}
		else {
			$xmlStr .= ">".PHP_EOL.$thisFunctionNameStr($value, $level+1).PHP_EOL.str_repeat("\t", $level)."</$tag>";
		}
		$lineCnt++;
	}
	return $xmlStr;
}

/**
 * convertObjectToXMLのエイリアス
 */
function convertObject2XML($data, $level = 0) {
	return convertObjectToXML($data, $level);
}

/**
 * resultSetをCSV文字列に変換
 */
function convertObjectToCSV($argArr, $argDelim = ',', $argRootFlag = true) {

	$csvStr = "";
	$thisFunctionNameStr = __FUNCTION__ ;

	while ( list ($key, $value) = each($argArr)) {
		if (true == is_array($value) || true == is_object($value)) {
			if ("size" !== $key && "type" !== $key) {
				$lineStr = $thisFunctionNameStr($value, $argDelim, false);
				if (true === $argRootFlag) {
					if (preg_match("/".$argDelim."$/m", $lineStr)) {
						$lineStr = substr($lineStr, 0, strlen($lineStr)-1);
					}
					$csvStr .= $lineStr."\r\n";
				}
				else {
					$csvStr .= $lineStr;
				}
			}
		}
		else {
			if ("size" != $key && "type" != $key) {
				$lineStr = "\"".$value."\"";
				if (true === $argRootFlag) {
					$csvStr .= $lineStr."\r\n";
				}
				else {
					$csvStr .= $lineStr.$argDelim;
				}
			}
		}
	}

	return $csvStr;
}

/**
 * convertObjectToCSVのエイリアス
 */
function convertObject2CSV($argArr, $argDelim = ',') {
	return convertObjectToCSV($argArr, $argDelim);
}

/**
 * 外部smtpサーバに接続してメールを送信する
 * @return
 * @param string $to
 * @param string $subject
 * @param string $message
 * @param string $from
 * @param string $bcc
 * @param string メールアドレス(エラーメール返信先)
 * @param string 拡張ヘッダー
 */
function sendEMail($smtp_host, $to = null, $subject = null, $message = null, $from, $bcc = null, $sender = null, $other_header = null) {

	$message = preg_replace("/\n/", "\r\n", $message);
	$other_header = preg_replace("/\n/", "\r\n", $other_header);

	if ( isset ($sender) && !$sender) {
		$sender = $from;
	}

	$connect = fsockopen($smtp_host, 25, $errno, $errstr, 30);

	if (!$connect) {
		return false;
	}
	if (!fgets($connect, 1024)) {
		return false;
	}
	fputs($connect, "HELO ".getenv('HOSTNAME')."\r\n");
	if (!fgets($connect, 1024)) {
		return false;
	}
	fputs($connect, "MAIL FROM:$sender"."\r\n");
	if (!fgets($connect, 1024)) {
		return false;
	}

	$rcptToArray = array ();
	$toArray = array ();
	$bccArray = array ();

	if (is_array($to)) {
		$rcptToArray = array_merge($rcptToArray, $to);
		$toArray = array_merge($toArray, $to);
	}
	else {
		$rcptToArray = array_merge($rcptToArray, preg_split("/,/", $to));
		$toArray = array_merge($toArray, preg_split("/,/", $to));
	}
	if (is_array($bcc)) {
		$rcptToArray = array_merge($rcptToArray, $bcc);
		$bccArray = array_merge($bccArray, $bcc);
	}
	else {
		$rcptToArray = array_merge($rcptToArray, split(",", $bcc));
		$bccArray = array_merge($bccArray, preg_split("/,/", $bcc));
	}
	foreach ($rcptToArray as $rcptTo) {
		if ($rcptTo) {
			fputs($connect, "RCPT TO:$rcptTo\r\n");
			if (!fgets($connect, 1024)) {
				return false;
			}
		}
	}
	fputs($connect, "DATA\n");
	if (!fgets($connect, 1024)) {
		return false;
	}
	fputs($connect, "From: $from\r\n");
	fputs($connect, "To: ".join(",", $toArray)."\r\n");
	fputs($connect, "X-SM-Envelope-From: ".$sender."\r\n");
	fputs($connect, "Subject: ".mb_encode_mimeheader($subject, "JIS", "SJIS")."\r\n");
	if ($other_header) {
		fputs($connect, $other_header."\r\n");
	}
	fputs($connect, "Date: ".date("r")."\r\n");
	fputs($connect, "Content-type: text/plain; charset=\"ISO-2022-JP\"\r\n");
	fputs($connect, "Content-Transfer-Encoding: 7bit\r\n");
	fputs($connect, "MIME-Version: 1.0\r\n");
	fputs($connect, "\r\n");
	fputs($connect, mb_convert_encoding($message, "JIS", "SJIS")."\r\n");
	fputs($connect, ".\r\n");
	if (!fgets($connect, 1024)) {
		return false;
	}
	fputs($connect, "QUIT\n");
	if (!fgets($connect, 1024)) {
		return false;
	}
	fclose($connect);

	return true;

}

/**
 * http_build_queryの拡張版
 * ※Xampp使用環境だと、戻り値の連結子が「&」でなく、「&amp;」で、それを変更するのは面倒なため、別メソッド化
 * ※ついでにPHP4でも使えるようにした
 * @param array データの多次元連想配列
 * @param string key名に付与する接頭子
 * @param string 連結子の明示指定
 * @return string key=val&key=val...と、なる文字列(urlencodeされる)
 */
function httpBuildQueryStr($data, $prefix = null, $sep = '') {
	$thisfuncNameStr = __FUNCTION__ ;
	$ret = array ();
	foreach ((array)$data as $k=>$v) {
		$k = urlencode($k);
		if ($prefix != null) {
			$k = $prefix."[".$k."]";
		}
		if (is_array($v) || is_object($v)) {
			array_push($ret, $thisfuncNameStr($v, $k, $sep));
		}
		else {
			array_push($ret, $k."=".@urlencode($v));
		}
	}

	if ( empty($sep)) {
		$sep = ini_get("arg_separator.output");
	}

	return implode($sep, $ret);
}

/**
 * http通信する
 * ※ついでにstream_contexst対応の未熟なPHP4で実装を簡単にする目的での対応
 * @param string URL文字列
 * @param array stream_content_params形式の、コンテキスト多次元配列
 * @param array ベーシック認証用のIDとPASSの組み合わせ配列
 * @return string 戻り値の文字列
 */
function httpRequest($argURLStr, $argContextArr = array (), $argTimeNum = 120, $argBasicAuthParams = array ()) {

	$method = "GET";
	if ( isset ($argContextArr["method"])) {
		$method = $argContextArr["method"];
	}
	$useragent = null;
	if ( isset ($argContextArr["user_agent"])) {
		$useragent = $argContextArr["user_agent"];
	}
	$headers = "";
	if ( isset ($argContextArr["header"])) {
		$headers = $argContextArr["header"];
	}
	$content = "";
	if ( isset ($argContextArr["content"])) {
		$content = $argContextArr["content"];
	}
	$post = null;
	if ( isset ($argContextArr["post"])) {
		$post = $argContextArr["post"];
	}
	$id = $argBasicAuthParams;

	/* URLを分解 */
	$URL = parse_url($argURLStr);

	if ("https" != $URL['scheme']) {

		/* クエリー */
		if ( isset ($URL['query'])) {
			$URL['query'] = "?".$URL['query'];
		}
		else {
			$URL['query'] = "";
		}

		/* デフォルトのポートは80 */
		if (! isset ($URL['port'])) {
			$URL['port'] = 80;
		}

		/* リクエストライン */
		$request = $method." ".$URL['path'].$URL['query']." HTTP/1.0\r\n";

		/* リクエストヘッダ */
		$request .= "Host: ".$URL['host']."\r\n";
		if (0 == strlen($useragent)) {
			$request .= "User-Agent: PHP/".phpversion()."\r\n";
		}
		else {
			$request .= "User-Agent: ".$useragent."\r\n";
		}

		/* Basic認証用のヘッダ */
		if ( isset ($URL['user']) && isset ($URL['pass'])) {
			$request .= "Authorization: Basic ".base64_encode($URL['user'].":".$URL['pass'])."\r\n";
		}
		else if ( isset ($id['user']) && isset ($id['pass'])) {
			$request .= "Authorization: Basic ".base64_encode($id['user'].":".$id['pass'])."\r\n";
		}

		/* 追加ヘッダ */
		$request .= $headers."\r\n";

		/* POSTの時はヘッダを追加して末尾にURLエンコードしたデータを添付 */
		if (strtoupper($method) == "POST") {
			if (0 == strlen($headers)) {
				$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
			}
			if (is_array($post)) {
				$tmpContent = httpBuildQueryStr($post, null, '&');
				if (0 < strlen($tmpContent) && 0 < strlen($content)) {
					$content .= "&";
				}
				$content .= $tmpContent;
			}
			$request .= "Content-Length: ".strlen($content)."\r\n";
			if ("" != $useragent) {
				$request .= "User-Agent: ".$useragent."\r\n";
			}
			$request .= "\r\n";
			$request .= $content;
		}
		else {
			$request .= "\r\n";
		}

		/* WEBサーバへ接続 */
		$errorNo = null;
		$errorMsg = null;
		$fp = fsockopen($URL['host'], $URL['port'], $errorNo, $errorMsg, $argTimeNum);

		/* 接続に失敗した時の処理 */
		if (!$fp) {
			return false;
		}

		//echo "\r\n<br/>".$request."\r\n<br/>";
		/* 要求データ送信 */
		fputs($fp, $request);

		/* 応答データ受信 */
		$response = "";
		while (!feof($fp)) {
			// 100kバイト単位くらいで受け取る
			$tmpStr = @fgets($fp);
			$response .= $tmpStr;
		}

		/* 接続を終了 */
		fclose($fp);

		/* ヘッダ部分とボディ部分を分離 */
		$DATA = split("\r\n\r\n", $response, 2);

		/* メッセージボディを返却 */
		return $DATA[1];

	}
	else {

		// httpsの場合の処理

		// 新しい cURL リソースを作成します
		$ch = curl_init($argURLStr);
		if ($ch) {

			if (is_array($post)) {
				$tmpContent = httpBuildQueryStr($post, null, '&');
				if (0 < strlen($tmpContent) && 0 < strlen($content)) {
					$content .= "&";
				}
				$content .= $tmpContent;
			}

			// URL その他のオプションを適切に設定します
			$options = array (
					CURLOPT_URL=>$argURLStr,
					CURLOPT_HEADER=>TRUE,
					CURLOPT_POST=>TRUE,
					CURLOPT_POSTFIELDS=>$content,
					CURLOPT_SSLVERSION=>3,
					CURLOPT_SSL_VERIFYPEER=>FALSE,
					CURLOPT_SSL_VERIFYHOST=>FALSE,
					CURLOPT_USERAGENT=>$useragent,
					CURLOPT_PORT=>443,
					CURLOPT_TIMEOUT=>$argTimeNum,
					CURLOPT_RETURNTRANSFER=>TRUE,
			);

			/* Basic認証突破用 */
			if ( isset ($URL['user']) && isset ($URL['pass'])) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $URL['user'].":".$URL['pass']);
			}
			else if ( isset ($id['user']) && isset ($id['pass'])) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $id['user'].":".$id['pass']);
			}

			curl_setopt_array($ch, $options);

			// curl_execは指定回リトライを試みる
			// XXX リトライ回数は指定出来るように後日変更
			$retryCount = 3;
			for ($i = 0; $i <= $retryCount; $i++) {
				$response = curl_exec($ch);
				$curl_errorno = curl_errno($ch);
				if ($response === false && $curl_errorno == "28") {
					// タイムアウトの場合はリトライしない。
					// 28は"CURLE_OPERATION_TIMEDOUT"のエラーコード
					$error_msg = " ". __FUNCTION__ ."のcurl_execでタイムアウトしました。\n";
					$error_msg .= "curl_errno=[".curl_errno($ch)."]\n";
					$error_msg .= "curl_error=[".curl_error($ch)."]\n";
					$error_msg .= "curl_getinfo結果↓\n";
					$info = curl_getinfo($ch);
					foreach ($info as $key=>$value) {
						$error_msg .= " ".$key."=[".$value."]\n";
					}
					error_log('[error] '.$error_msg);
					curl_close($ch);
					return false;
				}
				elseif ($response === false && $i === $retryCount) {
					// curl_execに指定回失敗したらfalseで返す。
					$error_msg = " ". __FUNCTION__ ."のcurl_execに失敗しました。\n";
					$error_msg .= "curl_errno=[".curl_errno($ch)."]\n";
					$error_msg .= "curl_error=[".curl_error($ch)."]\n";
					$error_msg .= "curl_getinfo結果↓\n";
					$info = curl_getinfo($ch);
					foreach ($info as $key=>$value) {
						$error_msg .= " ".$key."=[".$value."]\n";
					}
					curl_close($ch);
					error_log('[error] '.$error_msg);
					return false;
				}
				elseif ($response === false && $i < $retryCount) {
					// 指定回以前までの失敗はリトライ
					continue ;
				}
				else {
					// ヘッダー部分とボディーを分離
					$responseStr = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
					// 正常完。cURL リソースを閉じ、システムリソースを解放します
					curl_close($ch);
					return $responseStr;
				}
			}
		}
		else {
			// init失敗はどうしようもないので処理終了
			$error = getColorSizeTagStart("#FF0000")."情報が取得できませんでした｡再度アクセスしてください｡".getColorSizeTagEnd();
			$error_msg = __FUNCTION__ ."のcurl_initに失敗しました。";
			error_log('[error] '.$error_msg);
			return false;
		}

	}

}


if ( isset ($_SERVER["SERVER_SOFTWARE"]) && false !== stripos($_SERVER["SERVER_SOFTWARE"], "(win")) {
	function posix_getppid() {
		// 何もしない
	}
}

if (0 !== strpos(phpversion(), "5.3") && false === function_exists('lcfirst')) {
	function lcfirst($argStr) {
		return strtolower(substr($argStr, 0, 1)).substr($argStr, 1);
	}
}

/**
 * 奇数かどうか
 */
function isOdd($argNum){
	if(0 !== $argNum % 2){
		return FALSE;
	}
	return TRUE;
}

/**
 * pathInfoの拡張版
 * PHP6相当のpathInfo動作をする
 */
function pathInfoEX($argPath, $argKey) {
	$pathInfo = pathinfo($argPath);
	//if("filename" == $argKey && 5 > phpversion()){
	if ("filename" == $argKey) {
		return basename("/a/".$pathInfo["basename"], ".".$pathInfo["extension"]);
	}
	else {
		return $pathInfo[$argKey];
	}
}

// UTF-8文字列をUnicodeエスケープする。ただし英数字と記号はエスケープしない。
if(!function_exists('unicode_decode')) {
	function unicode_decode($str) {
		return preg_replace_callback("/((?:[^\x09\x0A\x0D\x20-\x7E]{3})+)/", "decode_callback", $str);
	}
	function decode_callback($matches) {
		$char = mb_convert_encoding($matches[1], "UTF-16", "UTF-8");
		$escaped = "";
		for ($i = 0, $l = strlen($char); $i < $l; $i += 2) {
			$escaped .=  "\u" . sprintf("%02x%02x", ord($char[$i]), ord($char[$i+1]));
		}
		return $escaped;
	}
}

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


/********************************
 * Retro-support of get_called_class()
* Tested and works in PHP 5.2.4
* http://www.sol1.com.au/
********************************/
if(!function_exists('get_called_class')) {
	function get_called_class($bt = false,$l = 1) {
		if (!$bt) $bt = debug_backtrace();
		if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep.");
		if (!isset($bt[$l]['type'])) {
			throw new Exception ('type not set');
		}
		else switch ($bt[$l]['type']) {
			case '::':
				$lines = file($bt[$l]['file']);
				$i = 0;
				$callerLine = '';
				do {
					$i++;
					$callerLine = $lines[$bt[$l]['line']-$i] . $callerLine;
				} while (stripos($callerLine,$bt[$l]['function']) === false);
				preg_match('/([a-zA-Z0-9\_]+)\x20*::\x20*'.$bt[$l]['function'].'/',
				$callerLine,
				$matches);
				if (!isset($matches[1])) {
					// must be an edge case.
					throw new Exception ("Could not find caller class: originating method call is obscured.");
				}
				switch ($matches[1]) {
					case 'self':
					case 'parent':
						return get_called_class($bt,$l+1);
					default:
						return $matches[1];
				}
				// won't get here.
			case '->': switch ($bt[$l]['function']) {
				case '__get':
					// edge case -> get class of calling object
					if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object.");
					return get_class($bt[$l]['object']);
				default: return $bt[$l]['class'];
			}

			default: throw new Exception ("Unknown backtrace method type");
		}
	}
}

function sha256($argment, $argRawOutput=FALSE){
	if(FALSE !== $argRawOutput){
		$argRawOutput = TRUE;
	}
	return hash("sha256", $argment, $argRawOutput);
}

/**
 * POPGATE用暗号化関数
 */
function popgate_enc ($str) {
	// ---------- 引数チェック
	//
	if (!isset($str)) {
		return('');
	}
	// echo("SRC=$str\n");

	// ---------- 初期化
	//
	$base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$slt = str_repeat('a', strlen($str));
	$reg_pat = array('/\+/', '/\//', '/=+$/');
	$rep_pat = array('_', '.', '');

	// ---------- 暗号化処理
	//
	$sum = 0;
	for ($i = 0; $i < strlen($str); $i++) {
		$sum += ord($str{$i}) - 40;
	}
	$parity = $base{$sum % 62};

	$len1 = $base{(int)(strlen($str) / 62)};
	$len2 = $base{strlen($str) % 62};

	$enc = $str ^ $slt;
	//    $enc = base64_encode(trim($enc));
	$enc = trim(base64_encode($enc));

	$enc = preg_replace($reg_pat, $rep_pat, $enc);
	// echo("ENC=$enc\n");

	return($enc.$len1.$len2.$parity);
}

/**
 * POPGATE用復号化関数
 */
function popgate_dec ($target) {
	// ---------- 引数チェック
	//
	if (!isset($target)) {
		return false;
	}

	// ---------- 引数分解
	//
	preg_match('/^(.+)(.)(.)(.)$/', $target, $matches);
	list( , $str, $len1, $len2, $parity) = $matches;
	// echo("$str:$len1:$len2:$parity\n");

	// ---------- 初期化
	//
	$base = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$reg_pat = array('/_/', '/\./');
	$rep_pat = array('+', '/');

	// ---------- 	復号化処理
	//
	$len1_index = strpos($base, $len1);
	if ($len1_index === FALSE) {
		//echo("${len1_index}length error(1-1)\n");
		return false;
	}
	$len2_index = strpos($base, $len2);
	if ($len2_index === FALSE) {
		//echo("length error(1-2)\n");
		return false;
	}
	$len = ($len1_index * 62) + $len2_index;
	$slt = str_repeat('a', $len);

	$parity_index = strpos($base, $parity);
	if ($parity_index === FALSE) {
		//echo("parity error(1)\n");
		return false;
	}

	$str = preg_replace($reg_pat, $rep_pat, $str);
	$dec = base64_decode($str) ^ $slt;

	if (strlen($dec) != $len) {
		//echo("length error(2)\n");
		return false;
	}

	$sum = 0;
	for ($i = 0; $i < strlen($dec); $i++) {
		$sum += ord($dec{$i}) - 40;
	}
	if (($sum % 62) != $parity_index) {
		echo("parity error(2)\n");
		//return('');
		return false;
	}
	return($dec);
}

?>