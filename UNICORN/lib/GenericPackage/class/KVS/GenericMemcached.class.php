<?php

/**
 * Memcache操作のラッパークラス(Singletonパターン実装)
 * PECL memcache >= 2.0.0を必要とします！
 * @author saimushi
 */
class GenericMemcached {

	/**
	 * Memcacheインスタンス保持用
	 * @var instance
	 */
	private static $_MemcacheInstance = NULL;

	/**
	 */
	private static function _initMemcache($argDSN = NULL){
		// 継承元クラス名を取得(静的クラス名を取るためにget_called_classを使用する)
		$calledClassName = @get_called_class();

		if(!isset(self::$_MemcacheInstance[$calledClassName])){

			if(NULL === $argDSN){
				// DSN無指定エラー
				throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
			}

			if(!class_exists('Memcache', FALSE)){
				// Memcacheが未インストール！
				throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
			}

			// memcacheインスタンスの初期化
			$MemcacheInstance = new Memcache();

			// tcp://は取り敢えず使わないのでいらない
			$DSN = $argDSN;
			if(FALSE !== strpos($DSN, 'tcp://')){
				$DSN = str_replace('tcp://', '', $DSN);
			}

			$DSNs = array();
			if(FALSE !== strpos($DSN, ',')){
				$DSNs = explode(',', $DSN);
			}
			else {
				$DSNs[0] = $DSN;
			}

			for($DSNCnt=0; count($DSNs) > $DSNCnt; $DSNCnt++) {
				$DSN = $DSNs[$DSNCnt];
				// DSN指定が有る場合の処理
				$tmp = explode(':', $DSN);
				$host = trim($tmp[0]);
				$port = ini_get("memcache.default_port");
				if(FALSE === $port){
					$port = 11211;
				}
				$pconnect = FALSE;
				$weight = 1;
				// デフォルトタイムアウトは5秒らしい・・・
				$timeout = 5;
				// デフォルトリトライインターバルは15秒らしい・・・
				$retry_interval = 15;

				// ポート指定の取得
				$matches = NULL;
				if(preg_match('/^([0-9]+)/', $tmp[1], $matches) && is_array($matches) && isset($matches[1])){
					$port = $matches[1];
				}
				if(preg_match('/persistent=1|persistent!=*|persist=1|persist!=*/', $tmp[1])){
					$pconnect = TRUE;
				}
				$matches = NULL;
				if(preg_match('/weight=([0-9\.]+)/', $tmp[1], $matches) && is_array($matches) && isset($matches[1])){
					$weight = $matches[1];
				}
				$matches = NULL;
				if(preg_match('/timeout=([0-9\.]+)/', $tmp[1], $matches) && is_array($matches) && isset($matches[1])){
					$timeout = $matches[1];
				}
				$matches = NULL;
				if(preg_match('/retry_interval=([0-9]+)/', $tmp[1], $matches) && is_array($matches) && isset($matches[1])){
					$retry_interval = $matches[1];
				}
				debug('$host, $port, $pconnect, (int)$weight, $timeout, $retry_interval=' . $host . '&' . $port . '&' . $pconnect . '&' . $weight . '&' . $timeout . '&' . $retry_interval);
				// プールに追加
				if(false === $MemcacheInstance->addServer($host, $port, $pconnect, (int)$weight, $timeout, $retry_interval)){
					// Memcacheインスタンス初期化エラー
					throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
				}
			}
			self::$_MemcacheInstance[$calledClassName] = $MemcacheInstance;
		}

		return $calledClassName;
	}

	/**
	 */
	public static function start($argDSN){
		self::_initMemcache($argDSN);
	}

	/**
	 */
	public static function get($argKey){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->get($argKey);
		$responseBool = FALSE;
		if(FALSE !== $response){
			$responseBool = TRUE;
		}
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'response'=>$responseBool), 'memcache');
		return $response;
	}

	/**
	 */
	public static function set($argKey, $argVal, $argCompressedFlag = FALSE, $argExpire = 0){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->set($argKey, $argVal, $argCompressedFlag, $argExpire = 0);
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'val'=>$argVal, 'compressed'=>$argCompressedFlag, 'expire'=>$argExpire, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function increment($argKey,$argCnt=1){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->increment($argKey,$argCnt);
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'cnt'=>$argCnt, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function decrement($argKey,$argCnt=1){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->decrement($argKey,$argCnt);
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'cnt'=>$argCnt, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function add($argKey, $argVal, $argCompressedFlag = FALSE, $argExpire = 0){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->add($argKey, $argVal, $argCompressedFlag, $argExpire);
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'val'=>$argVal, 'compressed'=>$argCompressedFlag, 'expire'=>$argExpire, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function delete($argKey, $argExpire = 0){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->delete($argKey, $argExpire = 0);
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'expire'=>$argExpire, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function replace($argKey, $argVal, $argCompressedFlag = FALSE, $argExpire = 0){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->replace($argKey, $argVal, $argCompressedFlag, $argExpire = 0);
		// logging
		logging(array('mem'=>__METHOD__, 'key'=>$argKey, 'val'=>$argVal, 'compressed'=>$argCompressedFlag, 'expire'=>$argExpire, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function flush(){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->flush();
		// logging
		logging(array('mem'=>__METHOD__, 'response'=>$response), 'memcache');
		return $response;
	}

	/**
	 */
	public static function quit(){
		$instanceIndex = self::_initMemcache();
		$response = self::$_MemcacheInstance[$instanceIndex]->quit();
		// logging
		logging(array('mem'=>__METHOD__, 'response'=>$response), 'memcache');
		return $response;
	}
}

?>
