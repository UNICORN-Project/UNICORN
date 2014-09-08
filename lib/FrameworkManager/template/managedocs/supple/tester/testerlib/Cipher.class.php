<?php

/**
 * 暗号化クラス
 * @author T.Morita
 * @version $Id$
 * @copyright cybird.co.jp
 */
class Cipher {

	/**
	 * Initialize Vectolの処理時の設定値を格納
	 * @var binary string
	 */
	static protected $iv = NULL;

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		//
	}

	/**
	 * デストラクタ
	 */
	public function __destruct() {
		//
	}

	/**
	 * データを暗号化する
	 * @param 配列キー：value, key, iv, algorithm, mode, prefix, suffix
	 * 								  パディング防止が必要な場合は、'prefix'と'suffix'を指定する。
	 * 								  'prefix'と'suffix'には、メタ文字( . \ + * ? [ ^ ] ( $ ) )のみ指定できます。
	 * @return 配列キー：encrypted, iv
	 */
	public static function encrypt($arguments) {
		// 引数のチェック
		if (false === isset($arguments['value']) || 0 === strlen($arguments['value']) || false === isset($arguments['key']) || 0 === strlen($arguments['key'])) {
			return false;
		}

		if (false === isset($arguments['algorithm']) || 0 === strlen($arguments['algorithm'])) {
			$arguments['algorithm'] = 'rijndael-256';
		}

		if (false === isset($arguments['mode']) || 0 === strlen($arguments['mode'])){
			$arguments['mode'] = 'cbc';
		}

		// XXX log処理入れる
		if (isset($arguments['prefix']) && isset($arguments['suffix'])) {
			$value = $arguments['prefix'].$arguments['value'].$arguments['suffix'];
		} elseif (false === isset($arguments['prefix']) && false === isset($arguments['suffix'])) {
			$value = $arguments['value'];
		} else {
			return false;
		}

		// 暗号モジュールをオープン
		$cipherHandler = mcrypt_module_open($arguments['algorithm'], $algorithm_directory = '', $arguments['mode'], $mode_directory = '');

		// IVの初期化処理
		self::$iv = NULL;
		if(isset($arguments['iv'])){
			self::$iv = $arguments['iv'];
		}
		// IVが指定されていない場合、IV を作成
		if (NULL === self::$iv) {
			self::$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipherHandler), MCRYPT_DEV_RANDOM);
		}

		// 暗号化処理を初期化
		mcrypt_generic_init($cipherHandler, $arguments['key'], self::$iv);

		// データを暗号化
		$encryptedData = mcrypt_generic($cipherHandler, $value);

		// 暗号化ハンドラを終了
		mcrypt_generic_deinit($cipherHandler);

		// モジュールをクローズ
		mcrypt_module_close($cipherHandler);

		return $encryptedData;
	}

	/**
	 * データを複号化する
	 * @param 配列キー：value, key, ,iv, algorithm, mode, prefix, suffix
	 * 								  パディング防止が必要な場合は、'prefix'と'suffix'を指定する。
	 * 								  'prefix'と'suffix'には、メタ文字( . \ + * ? [ ^ ] ( $ ) )のみ指定できます。
	 * @return string
	 */
	public static function decrypt($arguments) {
		// 引数のチェック
		if (false === isset($arguments['value']) || 0 === strlen($arguments['value']) || false === isset($arguments['key']) || 0 === strlen($arguments['key'])) {
			return false;
		}

		if (false === isset($arguments['algorithm']) || 0 === strlen($arguments['algorithm'])) {
			$arguments['algorithm'] = 'rijndael-256';
		}

		if (false === isset($arguments['mode']) || 0 === strlen($arguments['mode'])){
			$arguments['mode'] = 'cbc';
		}

		// 暗号モジュールをオープン
		$cipherHandler = mcrypt_module_open($arguments['algorithm'], $algorithm_directory = '', $arguments['mode'], $mode_directory = '');

		// 暗号化処理を初期化
		mcrypt_generic_init($cipherHandler, $arguments['key'], $arguments['iv']);

		// データを複号化
		$decryptedData = mdecrypt_generic($cipherHandler, $arguments['value']);

		// 暗号化ハンドラを終了
		mcrypt_generic_deinit($cipherHandler);

		// モジュールをクローズ
		mcrypt_module_close($cipherHandler);

		// XXX log処理入れる
		if (isset($arguments['prefix']) && isset($arguments['suffix'])) {
			//$decryptedData = trim($decryptedData);
			if (preg_match('/'.quotemeta($arguments['prefix']).'(.*)'.quotemeta($arguments['suffix']).'/', $decryptedData, $matches)) {
				$decryptedData = $matches[1];
			}
			return $decryptedData;
		} elseif (false === isset($arguments['prefix']) && false === isset($arguments['suffix'])) {
			return $decryptedData;
		} else {
			return false;
		}
	}

	/**
	 * 現在設定されている、最後に使用されたIVを返す
	 * @return binary string
	 */
	public static function getNowIV(){
		return self::$iv;
	}
}

?>