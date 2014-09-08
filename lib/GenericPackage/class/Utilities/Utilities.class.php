<?php

/**
 * 関数群
 * @author saimushi
 */
class Utilities {

	/**
	 * 2038年問題対応用の現在プロセス時間返却メソッド
	 * 1プロセス内では同じ値が常に返る事に注意！！
	 */
	public static function now($argTimezone=NULL){
		static $now = NULL;
		if(NULL === $now){
			$now = self:: date('Y-m-d H:i:s', null, null, $argTimezone);
		}
		return $now;
	}

	public static function gmnow(){
		static $gmnow = NULL;
		if(NULL === $gmnow){
			$gm = self:: date('P');
			$operator = substr($gm,0,1);
			if('+' === $operator){
				$operator = '-';
			}elseif('-' === $operator){
				$operator = '+';
			}
			$gm = substr($gm,1);
			$gms = explode(':', $gm);
			$gmnow = self::modifyDate($operator.$gms[0].'hour '.$operator.$gms[1].'minute', 'Y/m/d H:i:s', self::now());
		}
		return $gmnow;
	}

	/**
	 * 2038年問題対応用のdate関数互換メソッド
	 */
	public static function date($argFormat, $argDate=NULL, $argBeforeTimezone=NULL, $argAfterTimezone=NULL){
		static $DateInstance = array();
		$deftimezone = @date_default_timezone_get();
		if(strlen($deftimezone) > 0){
			$deftimezone = 'Asia/Tokyo';
			date_default_timezone_set($deftimezone);
		}
		if(NULL !== $argBeforeTimezone){
			if(strlen($deftimezone) > 0){
				date_default_timezone_set($argBeforeTimezone);
			}
		}
		if(!isset($DateInstance[$argDate.$argBeforeTimezone])){
			if(NULL === $argDate){
				$DateInstance[$argDate.$argBeforeTimezone] = new DateTime(NULL);
			}elseif(preg_match('/^[0-9]+$/',$argDate)){
				$DateInstance[$argDate.$argBeforeTimezone] = new DateTime('@'.$argDate);
				//$DateInstance[$argDate]->setTimestamp($argDate);
			}else{
				try{
					$DateInstance[$argDate.$argBeforeTimezone] = new DateTime($argDate);
				}catch (Exception $Exception){
					// NG
					return FALSE;
				}
			}
		}
		if(NULL !== $argAfterTimezone){
			$DateInstance[$argDate.$argBeforeTimezone]->setTimezone(new DateTimeZone($argAfterTimezone));
		}
		$date = $DateInstance[$argDate.$argBeforeTimezone]->format($argFormat);
		if(NULL !== $argAfterTimezone){
			$DateInstance[$argDate.$argBeforeTimezone]->setTimezone(new DateTimeZone($deftimezone));
		}
		return $date;
	}

	/**
	 * 2038年問題対応用のstrtotimeっぽいメソッド
	 */
	public static function modifyDate($argModify, $argFormat, $argDate=NULL, $argBeforeTimezone=NULL, $argAfterTimezone=NULL){
		try{
			$deftimezone = @date_default_timezone_get();
			if(strlen($deftimezone) > 0){
				$deftimezone = 'Asia/Tokyo';
				date_default_timezone_set($deftimezone);
			}
			if(NULL !== $argBeforeTimezone){
				if(strlen($deftimezone) > 0){
					date_default_timezone_set($argBeforeTimezone);
				}
			}
			if(NULL === $argDate){
				$DateInstance = new DateTime();
			}else{
				$DateInstance = new DateTime($argDate);
			}
			if(NULL !== $argAfterTimezone){
				$DateInstance->setTimezone(new DateTimeZone($argAfterTimezone));
			}
			$DateInstance->modify($argModify);
			$modifyedDate = $DateInstance->format($argFormat);
			unset($DateInstance);
			return $modifyedDate;
		}catch (Exception $Exception){
			// NG
			return FALSE;
		}
	}

	/**
	 * 有効な日付かどうかを評価する
	 * checkdateを拡張し、dateが解釈出来る全フォーマットに対応
	 */
	public static function checkDate($argDate){
		// どの書式でくるか解らないので取り敢えずDatetimeクラスに食わす
		$dateParses = date_parse($argDate);
		return checkdate($dateParses['month'],$dateParses['day'],$dateParses['year']);
	}

	/**
	 * 2038年問題対応用のdate関数互換メソッド
	 * 日付の妥当性と一緒に指定されたフォーマットとデータが一致するかどうか評価する
	 * あとDatetimeクラスが解釈出来る書式ならなんでもチェック出来るようにした
	 */
	public static function checkDateFormat($argDate,$argFormat){
		if(self::checkdate($argDate)){
			try{
				$deftimezone = @date_default_timezone_get();
				if(strlen($deftimezone) > 0){
					date_default_timezone_set('Asia/Tokyo');
				}
				$Date = new Datetime($argDate);
				if($argDate == $Date->format($argFormat)){
					return TRUE;
				}
			}catch (Exception $Exception){
				// NG
				return FALSE;
			}
		}
		return FALSE;
	}

	/**
	 * メソッド呼び出し元のエラーとしてExceptionする際の
	 * Line情報を構成する
	 */
	public static function getBacktraceExceptionLine(){
		$traces = debug_backtrace();
		$class = $traces[2]['class'];
		$method = $traces[2]['function'];
		$line = $traces[1]['line'];
		return $class.PATH_SEPARATOR.$class.'::'.$method.PATH_SEPARATOR.$line;
	}

	/**
	 * AES暗号形式でデータを暗号化し、base64encodeする
	 * @param string エンコードする文字列
	 * @param string 暗号キー
	 * @param 16進数 IV
	 * @return string base64encodeされた暗号データ
	 */
	public static function doHexEncryptAES($argValue, $argKey, $argIV = null, $argPrefix = '', $argSuffix = '') {
		return bin2hex(self::encryptAES($argValue, $argKey, $argIV, $argPrefix, $argSuffix));
	}

	/**
	 * base64decodeしてからAES暗号形式のデータを復号化する
	 * @param string デコードする文字列
	 * @param string 暗号キー
	 * @param 16進数 IV
	 * @return string 複合データ
	 */
	public static function doHexDecryptAES($argValue, $argKey, $argIV = null, $argPrefix = '', $argSuffix = '') {
		return self::decryptAES(@pack("H*", $argValue), $argKey, $argIV, $argPrefix, $argSuffix);
	}

	/**
	 * AES暗号形式でデータを暗号化し、base64encodeする
	 * @param string エンコードする文字列
	 * @param string 暗号キー
	 * @param base64 IV
	 * @return string base64encodeされｔ暗号データ
	 */
	public static function do64EncryptAES($argValue, $argKey, $argIV = null, $argPrefix = '', $argSuffix = '') {
		return base64_encode(self::encryptAES($argValue, $argKey, base64_decode($argIV), $argPrefix, $argSuffix));
	}

	/**
	 * base64decodeしてかっらAES暗号形式のデータを複合化する
	 * @param string デコードする文字列
	 * @param string 暗号キー
	 * @param base64 IV
	 * @return string 複合データ
	 */
	public static function do64DecryptAES($argValue, $argKey, $argIV = null, $argPrefix = '', $argSuffix = '') {
		return self::decryptAES(base64_decode($argValue), $argKey, base64_decode($argIV), $argPrefix, $argSuffix);
	}

	/**
	 * AES暗号形式でデータを暗号化する
	 * @param 	$argValue 	エンコードする値
	 * @param 	$argKey 	暗号キー
	 * @param 	$argIv 		IV
	 * @return 	$encrypt 	暗号化データ
	 */
	public static function encryptAES($argValue, $argKey, $argIV = null, $argPrefix = '', $argSuffix = '') {

		// パラメータセット
		// XXX パラメータは定数で可変出来るようにする
		$params = array(
				'value' 		=> $argValue,
				'key' 			=> $argKey,
				'iv' 			=> $argIV,
				'algorithm' 	=> 'rijndael-128',
				'mode' 			=> 'cbc',
				'prefix' 		=> $argPrefix,
				'suffix' 		=> $argSuffix,
		);

		// データを暗号化する
		$encrypt = Cipher :: encrypt($params);

		// エラー処理
		if (false === $encrypt || NULL === $encrypt) {
			return false;
		}
		return $encrypt;
	}

	/**
	 * AES暗号形式で暗号化されたデータを複号化する
	 * @param 	$argValue 	デコードする値
	 * @param 	$argKey 	暗号キー
	 * @param 	$argIv 		IV
	 * @return 	$encrypt 	複号化データ
	 */
	public static function decryptAES($argValue, $argKey, $argIV = null, $argPrefix = '', $argSuffix = '') {
		// パラメータセット
		// XXX パラメータは定数で可変出来るようにする
		$params = array(
				'value' 		=> $argValue,
				'key' 			=> $argKey,
				'iv' 			=> $argIV,
				'algorithm' 	=> 'rijndael-128',
				'mode' 			=> 'cbc',
				'prefix' 		=> $argPrefix,
				'suffix' 		=> $argSuffix,
		);

		// データを暗号化する
		$decrypt = Cipher :: decrypt($params);

		// エラー処理
		if (false === $decrypt || NULL === $decrypt) {
			return false;
		}
		return $decrypt;
	}

	public static function getRequestURL(){
		static $requestURL = NULL;
		if(NULL === $requestURL){
			if(strlen($_SERVER['QUERY_STRING']) > 0){
				$requestURL = substr($_SERVER["REQUEST_URI"],0,strlen($_SERVER["REQUEST_URI"]) - (strlen($_SERVER['QUERY_STRING'])+1));
			}else{
				$requestURL = $_SERVER["REQUEST_URI"];
			}
		}
		return $requestURL;
	}

	public static function getURIParams($argStartPoint=NULL){
		// パラメータ取得
		$params = array();
		$requestURL = self::getRequestURL();
		if(NULL !== $argStartPoint){
			$paramStartPoint = strpos($requestURL,$argStartPoint);
			if(FALSE === $paramStartPoint){
				// XXX エラー終了？？
				return FALSE;
			}
			$requestURL = substr($requestURL,$paramStartPoint+(strlen($argStartPoint)));
		}
		return explode('/',$requestURL);
	}

	public static function getRequestExtension(){
		static $extension = NULL;
		if(NULL === $extension){
			// アクセスされている拡張子を取っておく
			$extension = pathinfo(self::getRequestURL(),PATHINFO_EXTENSION);
		}
		return $extension;
	}

	/**
	 * 配列のkey名にstrtolowerを掛ける
	 */
	public static function lowerArrKeys($argument){
		if(is_array($argument)){
			foreach($argument as $key => $val){
				if(is_array($val)){
					$val = self::lowerArrKeys($val);
				}
				$argument[strtolower($key)] = $val;
				unset($argument[$key]);
			}
			return $argument;
		}
		return $argument;
	}

	/**
	 *
	 */
	public static function setRedirectHeader($argRedirectURL){
		header('location: '.$argRedirectURL);
	}

	/**
	 * 携帯クローラ判定
	 * @return	bool	true:クローラ、false:非クローラ
	 */
	public static function isMobileCrawler(){
		$crawler_arr = array(
				'Googlebot-Mobile',
				'moba-crawler',
				'mobile goo',
				'LD_mobile_bot',
				'froute.jp',
				'Y!J-SRD',
				'Y!J-MRD',
		);

		foreach ($crawler_arr as $val) {
			if (false !== strpos($_SERVER['HTTP_USER_AGENT'], $val)) {
				return TRUE;
			}
		}
		return FALSE;
	}
}

?>