<?php

/**
 * Storeクラス
 * @author saimushi
 */
class ParamStore {

	/**
	 * ストア本体
	 * @var 連想配列
	 */
	private static $__store = array();

	/**
	 * セッター
	 * @param unknown_type $argKey
	 * @param unknown_type $argment
	*/
	public static function set($argKey,$argument){
		self::$__store[$argKey] = $argument;
	}

	/**
	 * キーを探し、ヒットしたものを可能する、getメソッドのコールバック関数
	 */
	private static function _search($argHint){
		$tmpArr = array();
		foreach(self::$__store as $key => $val){
			if(preg_match('/'.$argHint.'/',$key)){
				$tmpArr[$key] = $val;
			}
		}
		if(count($tmpArr)>0){
			return $tmpArr;
		}
		return NULL;
	}

	/**
	 * ゲッターs
	 * @param string $argKey
	 */
	public static function get($argKey,$argSearchFlag = FALSE){
		if(FALSE !== $argSearchFlag){
			// 検索
			$tmpArr = array('hint'=>$argKey,'data'=>array());
			array_walk(self::$__store,'ParamStore::_search',&$tmpArr);
			if(count($tmpArr['data'])>0){
				return $tmpArr['data'];
			}
		}elseif(true === self::is($argKey)){
			return self::$__store[$argKey];
		}
		return NULL;
	}

	/**
	 * 存在チェック
	 * @param string $argKey
	 */
	public static function is($argKey){
		if(isset(self::$__store[$argKey])){
			return true;
		}else{
			return false;
		}
	}
}

?>
