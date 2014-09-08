<?php 

if(!function_exists('_')) {
	function _($msgid) {
		return $msgid;
	}
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
	bindtextdomain($domain, dirname(__FILE__));
	textdomain($domain);

	return $language;
}

// ローカライズを実行
$lang = initLocalize();

// デフォルトランゲージを定義して置く
define("DEFAULT_INIT_LOCAL_LANGUAGE", $lang);
debug(DEFAULT_INIT_LOCAL_LANGUAGE);

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


// XXX ローカライズに使用するテキストはココに列挙
// CiaoPicローカライズ実行コマンド
// POファイル(翻訳元ファイル)の生成
// cd current/server/
// xgettext --language=PHP --from-code=UTF-8 --add-comments=NOTE --output=./projectcore/locale/base_messages.po projectcore/locale/locale.php
// MOファイル(翻訳コンパイルファイル)の生成
// cd current/server/
// cd ./projectcore/locale/en_US/LC_MESSAGES/
// msgfmt messages.po 

// 定型エラーメッセージ定義 localizeの為にココで定数定義する
define("API_ERROR_MSG_MAINTENANCE", _("During maintenance."));
define("API_ERROR_MSG_DBCONNECT", _("Error has occurred when connecting to DB."));
define("API_ERROR_MSG_EXCEPTION", _("An unexpected error has occured. Please try again."));
define("API_ERROR_MSG_NOTMUST_APPVERSION", _("Application version is old.\nPlease update to the lastest version."));
define("API_ERROR_MSG_FATAL", _("System error"));
debug(API_ERROR_MSG_MAINTENANCE);

define("API_ERROR_MSG_NOAUTH", _("Failed to login."));
define("API_ERROR_MSG_NOUUID", _("%d:Critical error.\nThis error is seriously. Please contact to\n+813-6746-3123\nfor more details."));
define("API_ERROR_MSG_MISSMATCH_AUTHCODE", _("Your authentication code could not be verified.\nIn case you don't understand the correct authentication code.\nPlease try again by 'Resend'."));
define("API_ERROR_MSG_MISS_UPLOAD", _("Failed to upload file. Please try again."));
define("API_ERROR_MSG_EXISTS_UNIQID", _("Specified ID has been used already ."));
define("API_ERROR_MSG_BLOCKED", _("Can not view because it has been blocked.\nPlease unblock and try again."));
define("API_ERROR_MSG_NOT_CREATE_MYSELF_ALBUM", _("You can't create album as friend."));
define("API_ERROR_MSG_DIFFERENT_FORMAT_MAILADDRESS", _("The format of email address is incorrect."));
define("API_ERROR_MSG_EXISTS_MAILADDRESS", _("Can not register by your specified email address.\nPlease try it again or use another email address."));
define("API_ERROR_MSG_EXISTS_TELEPHONE", _("Specified tel number has  been used already.\nPlease widthdraw it from the previous device and then register again."));
define("API_ERROR_MSG_NOT_EXISTS_USER_DONT_ADD_GROUP", _("You can't choose user has withdrawn to set member of existing group."));
define("API_ERROR_MSG_TYPE_MISSMATCH_ALBUMNAME", _("Please input group name between 3 and 20 characters."));
define("API_ERROR_MSG_NOTFOUND_SERIAL_CODE", _("Please input the correct serial code."));
define("API_ERROR_MSG_EXPIRATION_STAMP", _("Stamp has expired so you can't use it."));
define("API_ERROR_MSG_SECRET_MISSMATCH", _("The answer is incorrect!\nIf you don't understand anyway, please ask your friend directly!"));
define("API_ERROR_MSG_INSENTIVMODE_EXISTS", _("This tutorial is already finished."));

// デフォルトのグループ名
define("DELETE_GROUP_NAME", _("%s' group %s"));

// 退会者名
define("DELETE_USER_NAME", _("unknown"));

// 氏名者名
define("BLANK_USER_NAME", _("no name"));

// 通知メッセージ
define("NOTIFY_MSG_POST_IMAGE_ID", "%s uploaded photos");
define("NOTIFY_MSG_POST_COMMENT_ID", "%s wrote comments");
define("NOTIFY_MSG_POST_STAMP_ID", "%s used stamps");
define("NOTIFY_MSG_MATCH_FRIENTD_ID", "Became friend with %s");
define("NOTIFY_MSG_REQUEST_FRIEND_ID", "Received friend request from %s");
define("NOTIFY_MSG_JOIN_ALBUM_ID", "%s joined the album");
define("NOTIFY_MSG_JOIN_GROUP_ID", "%s joined the group");
define("NOTIFY_MSG_OUT_GROUP_ID", _("%s has left group"));
define("NOTIFY_MSG_POST_IMAGE", _(NOTIFY_MSG_POST_IMAGE_ID));
define("NOTIFY_MSG_POST_COMMENT", _(NOTIFY_MSG_POST_COMMENT_ID));
define("NOTIFY_MSG_POST_STAMP", _(NOTIFY_MSG_POST_STAMP_ID));
define("NOTIFY_MSG_MATCH_FRIENTD", _(NOTIFY_MSG_MATCH_FRIENTD_ID));
define("NOTIFY_MSG_REQUEST_FRIEND", _(NOTIFY_MSG_REQUEST_FRIEND_ID));
define("NOTIFY_MSG_JOIN_ALBUM", _(NOTIFY_MSG_JOIN_ALBUM_ID));
define("NOTIFY_MSG_JOIN_GROUP", _(NOTIFY_MSG_JOIN_GROUP_ID));
define("NOTIFY_MSG_OUT_GROUP", _(NOTIFY_MSG_OUT_GROUP_ID_ID));
define("NOTIFY_MSG_JOIN_GROUP_ME", _("joined the group"));

// 招待メッセージ
define("INVITE_MSG_SMS", _("Let's use Unpublish talk album CiaoPic!\n"));
define("INVITE_MSG_EMAIL", _("Let's use Unpublished talk album CiaoPic!\nPlease confirm album is created by %d from URL below♪\n\n※Expiration time is one week!\n"));

// 引き継ぎメール関連
define("RELAY_MSG_EMAIL_PREFINISH_SUBJECT", _("Relay PreFinish Subject\n"));
define("RELAY_MSG_EMAIL_PREFINISH", _("Relay PreFinish\n"));
define("RELAY_MSG_EMAIL_FINISH_SUBJECT", _("Relay Finish Subject\n"));
define("RELAY_MSG_EMAIL_FINISH", _("Relay Finish\n"));

// 招待WEB関連
define("INVITE_WEB_TITLE", _("CiaoPic - Unpublished talk album: This is private albums (free) for secret couple showing photos about their good memories (friends, couple, family, group)"));
define("INVITE_WEB_FROM_MSG", _("from"));
define("INVITE_WEB_TO_MSG", _("to"));
define("INVITE_WEB_INVITATION_MSG", _("invitation of %s♪"));

// PZL用
define("API_ERROR_MSG_NOQUESTION", _("アップデートを待ってね！"));

?>