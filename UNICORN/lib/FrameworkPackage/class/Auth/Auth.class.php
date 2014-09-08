<?php

class Auth
{
	protected static $_sessionCryptKey = NULL;
	protected static $_sessionCryptIV = NULL;
	protected static $_authCryptKey = NULL;
	protected static $_authCryptIV = NULL;
	protected static $_DBO = NULL;
	protected static $_initialized = FALSE;
	public static $authTable = 'user_table';
	public static $authPKeyField = 'id';
	public static $authIDField = 'mailaddress';
	public static $authPassField = 'password';
	public static $authCreatedField = 'create_date';
	public static $authModifiedField = 'modify_date';
	public static $authIDEncrypted = 'AES128CBC';
	public static $authPassEncrypted = 'SHA256';

	protected static function _init($argDSN=NULL){
		if(FALSE === self::$_initialized){

			$DSN = NULL;

			if(class_exists('Configure') && NULL !== Configure::constant('DB_DSN')){
				// 定義からセッションDBの接続情報を特定
				$DSN = Configure::DB_DSN;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_DB_DSN')){
				// 定義からセッションDBの接続情報を特定
				$DSN = Configure::AUTH_DB_DSN;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_TBL_NAME')){
				// 定義からuserTable名を特定
				self::$authTable = Configure::AUTH_TBL_NAME;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_PKEY_FIELD_NAME')){
				// 定義からuserTable名を特定
				self::$authPKeyField = Configure::AUTH_PKEY_FIELD_NAME;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_ID_FIELD_NAME')){
				// 定義からuserTable名を特定
				self::$authIDField = Configure::AUTH_ID_FIELD_NAME;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_PASS_FIELD_NAME')){
				// 定義からuserTable名を特定
				self::$authPassField = Configure::AUTH_PASS_FIELD_NAME;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_CREATE_DATE_KEY_NAME')){
				// 定義からuserTable名を特定
				self::$authCreatedField = Configure::AUTH_CREATE_DATE_KEY_NAME;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_MODIFY_DATE_KEY_NAME')){
				// 定義からuserTable名を特定
				self::$authModifiedField = Configure::AUTH_MODIFY_DATE_KEY_NAME;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_ID_ENCRYPTED')){
				// 定義からuserTable名を特定
				self::$authIDEncrypted = Configure::AUTH_ID_ENCRYPTED;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_PASS_ENCRYPTED')){
				// 定義からuserTable名を特定
				self::$authPassEncrypted = Configure::AUTH_PASS_ENCRYPTED;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('CRYPT_KEY')){
				// 定義から暗号化キーを設定
				self::$_sessionCryptKey = Configure::CRYPT_KEY;
				self::$_authCryptKey = Configure::CRYPT_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('NETWORK_CRYPT_KEY')){
				// 定義から暗号化キーを設定
				self::$_sessionCryptKey = Configure::NETWORK_CRYPT_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('SESSION_CRYPT_KEY')){
				// 定義から暗号化キーを設定
				self::$_sessionCryptKey = Configure::SESSION_CRYPT_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('DB_CRYPT_KEY')){
				// 定義から暗号化キーを設定
				self::$_authCryptKey = Configure::DB_CRYPT_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_CRYPT_KEY')){
				// 定義から暗号化キーを設定
				self::$_authCryptKey = Configure::AUTH_CRYPT_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('CRYPT_IV')){
				// 定義から暗号化IVを設定
				self::$_sessionCryptIV = Configure::CRYPT_IV;
				self::$_authCryptIV = Configure::CRYPT_KEY;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('NETWORK_CRYPT_IV')){
				// 定義から暗号化IVを設定
				self::$_sessionCryptIV = Configure::NETWORK_CRYPT_IV;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('SESSION_CRYPT_IV')){
				// 定義から暗号化IVを設定
				self::$_sessionCryptIV = Configure::SESSION_CRYPT_IV;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('DB_CRYPT_IV')){
				// 定義から暗号化キーを設定
				self::$_authCryptKIV = Configure::DB_CRYPT_IV;
			}
			if(class_exists('Configure') && NULL !== Configure::constant('AUTH_CRYPT_IV')){
				// 定義から暗号化キーを設定
				self::$_authCryptIV = Configure::AUTH_CRYPT_IV;
			}
			if(defined('PROJECT_NAME') && strlen(PROJECT_NAME) > 0 && class_exists(PROJECT_NAME . 'Configure')){
				$ProjectConfigure = PROJECT_NAME . 'Configure';
				if(NULL !== $ProjectConfigure::constant('DB_DSN')){
					// 定義からセッションDBの接続情報を特定
					$DSN = $ProjectConfigure::DB_DSN;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_DB_DSN')){
					// 定義からセッションDBの接続情報を特定
					$DSN = $ProjectConfigure::AUTH_DB_DSN;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_TBL_NAME')){
					// 定義からuserTable名を特定
					self::$authTable = $ProjectConfigure::AUTH_TBL_NAME;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_PKEY_FIELD_NAME')){
					// 定義からuserTable名を特定
					self::$authPKeyField = $ProjectConfigure::AUTH_PKEY_FIELD_NAME;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_ID_FIELD_NAME')){
					// 定義からuserTable名を特定
					self::$authIDField = $ProjectConfigure::AUTH_ID_FIELD_NAME;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_PASS_FIELD_NAME')){
					// 定義からuserTable名を特定
					self::$authPassField = $ProjectConfigure::AUTH_PASS_FIELD_NAME;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_CREATE_DATE_KEY_NAME')){
					// 定義からuserTable名を特定
					self::$authCreatedField = $ProjectConfigure::AUTH_CREATE_DATE_KEY_NAME;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_MODIFY_DATE_KEY_NAME')){
					// 定義からuserTable名を特定
					self::$authModifiedField = $ProjectConfigure::AUTH_MODIFY_DATE_KEY_NAME;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_ID_ENCRYPTED')){
					// 定義からuserTable名を特定
					self::$authIDEncrypted = $ProjectConfigure::AUTH_ID_ENCRYPTED;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_PASS_ENCRYPTED')){
					// 定義からuserTable名を特定
					self::$authPassEncrypted = $ProjectConfigure::AUTH_PASS_ENCRYPTED;
				}
				if(NULL !== $ProjectConfigure::constant('CRYPT_KEY')){
					// 定義から暗号化キーを設定
					self::$_sessionCryptKey = $ProjectConfigure::CRYPT_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('NETWORK_CRYPT_KEY')){
					// 定義から暗号化キーを設定
					self::$_sessionCryptKey = $ProjectConfigure::NETWORK_CRYPT_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('SESSION_CRYPT_KEY')){
					// 定義から暗号化キーを設定
					self::$_sessionCryptKey = $ProjectConfigure::SESSION_CRYPT_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('DB_CRYPT_KEY')){
					// 定義から暗号化キーを設定
					self::$_authCryptKey = $ProjectConfigure::DB_CRYPT_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_CRYPT_KEY')){
					// 定義から暗号化キーを設定
					self::$_authCryptKey = $ProjectConfigure::AUTH_CRYPT_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('CRYPT_IV')){
					// 定義から暗号化IVを設定
					self::$_sessionCryptIV = $ProjectConfigure::CRYPT_IV;
					self::$_authCryptIV = $ProjectConfigure::CRYPT_KEY;
				}
				if(NULL !== $ProjectConfigure::constant('NETWORK_CRYPT_IV')){
					// 定義から暗号化IVを設定
					self::$_sessionCryptIV = $ProjectConfigure::NETWORK_CRYPT_IV;
				}
				if(NULL !== $ProjectConfigure::constant('SESSION_CRYPT_IV')){
					// 定義から暗号化IVを設定
					self::$_sessionCryptIV = $ProjectConfigure::SESSION_CRYPT_IV;
				}
				if(NULL !== $ProjectConfigure::constant('DB_CRYPT_IV')){
					// 定義から暗号化キーを設定
					self::$_authCryptKIV = $ProjectConfigure::DB_CRYPT_IV;
				}
				if(NULL !== $ProjectConfigure::constant('AUTH_CRYPT_IV')){
					// 定義から暗号化キーを設定
					self::$_authCryptIV = $ProjectConfigure::AUTH_CRYPT_IV;
				}
			}

			// DBOを初期化
			if(NULL === self::$_DBO){
				if(NULL !== $argDSN){
					// セッションDBの接続情報を直指定
					$DSN = $argDSN;
				}
				self::$_DBO = DBO::sharedInstance($DSN);
			}

			// 初期化済み
			self::$_initialized = TRUE;
		}
	}

	/**
	 */
	public static function getEncryptedAuthIdentifier($argIdentifier=NULL){
		if(NULL === $argIdentifier){
			$argIdentifier = Session::sessionID();
		}
		return Utilities::doHexEncryptAES($argIdentifier, self::$_sessionCryptKey, self::$_sessionCryptIV);

	}

	/**
	 */
	public static function getDecryptedAuthIdentifier($argIdentifier=NULL){
		if(NULL === $argIdentifier){
			$argIdentifier = Session::sessionID();
		}
		return Utilities::doHexDecryptAES($argIdentifier, self::$_sessionCryptKey, self::$_sessionCryptIV);
	}

	/**
	 * 認証が証明済みのユーザーモデルを返す
	 * @param string DB接続情報
	 */
	public static function getCertifiedUser($argDSN = NULL){
		if(FALSE === self::$_initialized){
			self::_init($argDSN);
		}
		Session::start();
		$sessionIdentifier = Session::sessionID();
		debug( self::$_sessionCryptKey . ':' . self::$_sessionCryptIV);
		debug("session identifier".$sessionIdentifier);
		$userID = self::getDecryptedAuthIdentifier($sessionIdentifier);
		debug("decrypted userID=".$userID);
		if(strlen($userID) > 0){
			$User = ORMapper::getModel(self::$_DBO, self::$authTable, $userID);
			debug("DBGet userID=".$User->{self::$authPKeyField});
			if(isset($User->{self::$authPKeyField}) && NULL !== $User->{self::$authPKeyField} && FALSE === is_object($User->{self::$authPKeyField}) && strlen((string)$User->{self::$authPKeyField}) > 0){
				// UserIDが特定出来た
				debug("Authlized");
				return $User;
			}
		}
		// 認証出来ない！
		debug("Auth failed");
		return FALSE;
	}

	/**
	 * 認証が証明済みかどうか(セッションが既にあるかどうか)
	 * @param string DB接続情報
	 */
	public static function isCertification($argDSN = NULL){
		if(FALSE === self::$_initialized){
			self::_init($argDSN);
		}
		if(FALSE === self::getCertifiedUser()){
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 認証を証明する(ログインしてセッションを発行する)
	 * @param string 認証ID
	 * @param string 認証パスワード
	 * @param string DB接続情報
	 * @param string 強制再認証
	 */
	public static function certify($argID = NULL, $argPass = NULL, $argDSN = NULL, $argExecut = FALSE){
		debug('start certify auth');
		if(TRUE === $argExecut || FALSE === self::isCertification($argDSN)){
			// ログインセッションが無かった場合に処理を実行
			$id = $argID;
			$pass = $argPass;
			if(NULL === $id){
				if(TRUE === class_exists('Flow', FALSE) && isset(Flow::$params) && isset(Flow::$params['post']) && TRUE === is_array(Flow::$params['post']) && isset(Flow::$params['post'][self::$authIDField])){
					// Flowに格納されているPOSTパラメータを自動で使う
					$id = Flow::$params['post'][self::$authIDField];
				}
				if(isset($_REQUEST) && isset($_REQUEST[self::$authIDField])){
					// リクエストパラメータから直接受け取る
					$id = $_REQUEST[self::$authIDField];
				}
			}
			if(NULL === $pass){
				if(TRUE === class_exists('Flow', FALSE) && isset(Flow::$params) && isset(Flow::$params['post']) && TRUE === is_array(Flow::$params['post']) && isset(Flow::$params['post'][self::$authPassField])){
					// Flowに格納されているPOSTパラメータを自動で使う
					$pass = Flow::$params['post'][self::$authPassField];
				}
				if(isset($_REQUEST) && isset($_REQUEST[self::$authPassField])){
					// リクエストパラメータから直接受け取る
					$pass = $_REQUEST[self::$authPassField];
				}
			}
			// ユーザーモデルを取得
			$User = self::getRegisteredUser($id, $pass);
			if(FALSE === $User){
				// 証明失敗
				return FALSE;
			}
			// セッションを発行
			Session::start();
			debug('self::$authPKeyField='.self::$authPKeyField);
			$sessionIdentifier = self::getEncryptedAuthIdentifier($User->{self::$authPKeyField});
			debug('new identifier='.$sessionIdentifier);
			Session::sessionID($sessionIdentifier);
			// ログインした固有識別子をSessionに保存して、Cookieの発行を行う
			Session::set('identifier', $User->{self::$authPKeyField});
		}
		debug('end certify auth');
		return TRUE;
	}

	/**
	 * 認証を非証明する(ログアウトする)
	 * @param string DB接続情報
	 */
	public static function unCertify($argDSN = NULL){
		if(FALSE === self::$_initialized){
			self::_init($argDSN);
		}
		debug('is logout??');
		Session::clear();
		debug('is logout!!');
		return TRUE;
	}

	/**
	 * 登録済みかどうか
	 * @param string $argDSN
	 */
	public static function getRegisteredUser($argID = NULL, $argPass = NULL, $argDSN = NULL){
		if(FALSE === self::$_initialized){
			self::_init($argDSN);
		}
		$id = $argID;
		$pass = $argPass;
		if(NULL === $id){
			if(TRUE === class_exists('Flow', FALSE) && isset(Flow::$params) && isset(Flow::$params['post']) && TRUE === is_array(Flow::$params['post']) && isset(Flow::$params['post'][self::$authIDField])){
				// Flowに格納されているPOSTパラメータを自動で使う
				$id = Flow::$params['post'][self::$authIDField];
			}
			if(isset($_REQUEST) && isset($_REQUEST[self::$authIDField])){
				// リクエストパラメータから直接受け取る
				$id = $_REQUEST[self::$authIDField];
			}
		}
		if(NULL === $pass){
			if(TRUE === class_exists('Flow', FALSE) && isset(Flow::$params) && isset(Flow::$params['post']) && TRUE === is_array(Flow::$params['post']) && isset(Flow::$params['post'][self::$authPassField])){
				// Flowに格納されているPOSTパラメータを自動で使う
				$pass = Flow::$params['post'][self::$authPassField];
			}
			if(isset($_REQUEST) && isset($_REQUEST[self::$authPassField])){
				// リクエストパラメータから直接受け取る
				$pass = $_REQUEST[self::$authPassField];
			}
		}
		debug($id.':'.$pass);
		$query = '`' . self::$authIDField . '` = :' . self::$authIDField . ' AND `' . self::$authPassField . '` = :' . self::$authPassField . ' ';
		$binds = array(self::$authIDField => self::_resolveEncrypted($id, self::$authIDEncrypted), self::$authPassField => self::_resolveEncrypted($pass, self::$authPassEncrypted));
		$User = ORMapper::getModel(self::$_DBO, self::$authTable, $query, $binds);
		if(isset($User->{self::$authPKeyField}) && NULL !== $User->{self::$authPKeyField} && FALSE === is_object($User->{self::$authPKeyField}) && strlen((string)$User->{self::$authPKeyField}) > 0){
			// 登録済みのユーザーIDを返す
			return $User;
		}
		// ユーザー未登録
		return FALSE;
	}

	/**
	 * 登録済みかどうか
	 * @param string $argDSN
	 */
	public static function isRegistered($argID = NULL, $argPass = NULL, $argDSN = NULL){
		if(FALSE === self::$_initialized){
			self::_init($argDSN);
		}
		if(FALSE === self::getRegisteredUser($argID, $argPass)){
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 登録する
	 * @param string DB接続情報
	 */
	public static function registration($argID = NULL, $argPass = NULL, $argDSN = NULL){
		if(FALSE === self::$_initialized){
			self::_init($argDSN);
		}
		$id = $argID;
		$pass = $argPass;
		if(NULL === $id){
			if(TRUE === class_exists('Flow', FALSE) && isset(Flow::$params) && isset(Flow::$params['post']) && TRUE === is_array(Flow::$params['post']) && isset(Flow::$params['post'][self::$authIDField])){
				// Flowに格納されているPOSTパラメータを自動で使う
				$id = Flow::$params['post'][self::$authIDField];
			}
			if(isset($_REQUEST) && isset($_REQUEST[self::$authIDField])){
				// リクエストパラメータから直接受け取る
				$id = $_REQUEST[self::$authIDField];
			}
		}
		if(NULL === $pass){
			if(TRUE === class_exists('Flow', FALSE) && isset(Flow::$params) && isset(Flow::$params['post']) && TRUE === is_array(Flow::$params['post']) && isset(Flow::$params['post'][self::$authPassField])){
				// Flowに格納されているPOSTパラメータを自動で使う
				$pass = Flow::$params['post'][self::$authPassField];
			}
			if(isset($_REQUEST) && isset($_REQUEST[self::$authPassField])){
				// リクエストパラメータから直接受け取る
				$pass = $_REQUEST[self::$authPassField];
			}
		}
		$id = self::_resolveEncrypted($id, self::$authIDEncrypted);
		$pass = self::_resolveEncrypted($pass, self::$authPassEncrypted);
		$gmtDate = Utilities::date('Y-m-d H:i:s', NULL, NULL, 'GMT');
		$query = '`' . self::$authIDField . '` = :' . self::$authIDField . ' AND `' . self::$authPassField . '` = ' . self::$authPassField . ' ';
		$binds = array(self::$authIDField => $id, self::$authPassField => $pass);
		$User = ORMapper::getModel(self::$_DBO, self::$authTable, $query, $binds);
		$User->{'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', self::$authIDField)))}($id);
		$User->{'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', self::$authPassField)))}($pass);
		$User->{'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', self::$authCreatedField)))}($gmtDate);
		$User->{'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', self::$authModifiedField)))}($gmtDate);
		if(TRUE === $User->save()){
			// ユーザーの登録は完了とみなし、コミットを行う！
			self::$_DBO->commit();
		}
		return $User;
	}

	/**
	 * 登録済みかどうか
	 * @param string $argDSN
	 */
	protected static function _resolveEncrypted($argString, $argAlgorism = NULL){
		debug('EncryptAlg='.$argAlgorism);
		$string = $argString;
		if('sha1' === strtolower($argAlgorism)){
			$string = sha1($argString);
		}
		elseif('sha256' === strtolower($argAlgorism)){
			$string = sha256($argString);
		}
		elseif(FALSE !== strpos(strtolower($argAlgorism), 'aes')){
			$string = Utilities::doHexEncryptAES($argString, self::$_authCryptKey, self::$_authCryptIV);
		}
		return $string;
	}
}
?>