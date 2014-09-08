<?php

/**
 * Sessionクラスのインターフェース定義
 * @author saimushi
 */
Interface SessionIO {

	/**
	 * Cookieからトークンを出し入れする時のキー名を変えられるようにする為のアクセサ
	 * @param string トークンキー名
	 */
	public static function setTokenKey($argTokenKey);

	/**
	 * 新しいトークンを指定のトークンキー名で払い出しcookieにセットする
	 * @param string トークンキー名
	*/
	public static function setTokenToCookie($argTokenKey);

	/**
	 * セッションIDを明示的に指定する
	 * @param string identifier
	*/
	public static function sessionID($argIdentifier=NULL);

	/**
	 * セッションを開始する
	 * @param string cookieの対象ドメイン指定
	*/
	public static function start($argDomain=NULL, $argExpiredtime=NULL, $argDSN=NULL);

	/**
	 * セッションにしまわれているデータの数を返す
	*/
	public static function count();

	/**
	 * セッションにしまわれているデータのキーの一覧を返す
	*/
	public static function keys();

	/**
	 * 指定されたキー名のセッションデータを返す
	 * @param string キー名
	*/
	public static function get($argKey = NULL);

	/**
	 * セッションデータに指定されたキー名で指定された値を格納する
	 * @param string キー名
	 * @param mixed 値
	*/
	public static function set($argKey, $argment);

	/**
	 * 不要になっているハズのセッションを全てクリーンする
	*/
	public static function clean();
}

?>