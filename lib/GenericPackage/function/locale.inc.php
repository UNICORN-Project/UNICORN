<?php
/**
 * locale.inc.php
 *
 * ローカライズ用関数
 * gettextの設定を行う
 *
 * 条件
 * conf.phpをinculudeしていること
 *
 * @author t.matsuba
 * @version $Id$
 * @copyright cybird.co.jp
 */

if (! extension_loaded('gettext')) {
	require('gettext.php');
} else {
	function N_($message) {
		return $message;
	}
	if (! function_exists('bind_textdomain_codeset')) {
		function bind_textdomain_codeset($domain, $codeset) {
			return;
		}
	}
}

/**
 * setLocalize
 * 翻訳したい場合は本関数をコールしてね
 *
 * param null
 * return boolean
 */
function setLocalize($argLanguage){
	//// ローカライズ設定
	// 言語設定
	$language = $argLanguage;

	// jaの場合はja_JPに変換
	if( 0 == strcmp($language, 'ja') ){
		$language = 'ja_JP';
	} elseif( 0 == strcmp($language, 'en') ){
		$language = 'en_US';
	} elseif( 0 == strcmp($language, 'zh') ){
		$language = 'zh_CN';
	}

	// gettext設定
	putenv("LANG={$language}");
	setlocale(LC_ALL, $language);
	bindtextdomain(LOCALE_DOMAIN,LOCALE_PATH);
	bind_textdomain_codeset(LOCALE_DOMAIN, 'utf-8');
	textdomain(LOCALE_DOMAIN);
}

// ローカル日付の取得
function _date($argDate, $argTargetLocal, $argFormat=null){
	if("JP" == strtoupper($argTargetTimezone) || "ja_jp" == strtolower($argTargetTimezone) || "ja-jp" == strtolower($argTargetTimezone)){
		if(null === $argFormat){
			$argFormat="Y-m-d H:i:s";
		}
		return Utilities::date($argFormat, $argDate, "GMT", "Asia/Tokyo");
	}
	else {
		if(null === $argFormat){
			$argFormat="Y-m-d H:i:s";
		}
		return Utilities::date($argFormat, $argDate, "GMT");
	}
}

// エイリアス
function _getdate($argDate, $argTargetLocal, $argFormat=null){
	return _date($argDate, $argTargetLocal, $argFormat);
}

// ローカライズ処理
function initLocalize($argLanguage=null){
	$language = 'en_US';
	// 適用言語判定
	if(null === $argLanguage && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
		// 指定がなければHTTP_ACCEPT_LANGUAGEから判定する
		$argLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
	if (false !== strpos($argLanguage, "ja") || false !== strpos(strtolower($argLanguage), "jp")) {
		// 強制日本語
		$argLanguage = 'ja_JP';
	}
	if (false !== strpos($argLanguage, "zh") || false !== strpos(strtolower($argLanguage), "cn") || false !== strpos(strtolower($argLanguage), "tw")) {
		// 強制中国語(簡体)
		$argLanguage = 'zh_CN';
	}
	if (false !== strpos($argLanguage, "ja_JP") || false !== strpos($argLanguage, "zh_CN")) {
		// 言語を決定
		$language = $argLanguage.".UTF-8";
	}
	debug("lng=".$language);
	// 言語処理
	putenv("LANG=$language");
	setlocale(LC_ALL, $language);
	$domain = "messages";
	bindtextdomain($domain, Configure::ROOT_PATH."/projectcore/locale");
	textdomain($domain);
}

// ローカライズを実行
$lang = initLocalize();

// デフォルトランゲージを定義して置く
define("DEFAULT_INIT_LOCAL_LANGUAGE", $lang);

// 指定言語のメッセージを返す
function getmsg($msgid, $argLanguage=null){
	// 受け取った言語情報でinitし直す
	if(null !== $argLanguage){
		initLocalize($argLanguage);
	}
	$msg = _($msgid);
	if(null !== $argLanguage){
		// 元に戻す
		initLocalize(DEFAULT_INIT_LOCAL_LANGUAGE);
	}
	return $msg;
}

?>