<?php

/**
 * Validate関数群
 *
 * 使用者様へのルールとお願い:
 *  ・エラーがあったらthrowされるので、try〜catchして下さい
 *  ・is〜 エラーがあった時点でExceptionを発生させ、処理を終了します。
 *  ・check〜 状態をすべてチェックし、エラーを貯めこみます。最後まで処理出来ればTRUEを返しますが、途中に起きたエラーは貯めこまれます。
 *    → なので、使用者「何処で・いくつ」エラーが起きていたかを別メソッドを通してエラーを取得し、自身でハンドリングする必要があります。
 *
 * 開発者様へのルールとお願い:
 *  ・上記使用者の意図にそって実装を追加して下さい。
 *  ・このクラスの中にあるメソッドは全てstaticでなければなりません。
 *  ・RFCは必ず確認して下さい。
 *  ・messageは(何エラーか,何処で)の順番で記述して下さい。備考は最後に追加して下さい。
 *
 * @author saimushi
 */
class GenericValidations {

	/**
	 * 直前のエラーのメッセージを格納しておく
	 */
	private static $_message = NULL;

	/**
	 * messageのアクセサ
	 */
	public static function getMessage(){
		return self::$_message;
	}

	/**
	 * messageのアクセサ
	 * ただし、セットは継承した子クラスしか出来ないように縛ってある
	 */
	protected static function _setMessage($argMsg){
		self::$_mseeage = $argMsg;
	}

	/**
	 * メールアドレスがRFC2822(+DoCoMoの拡張)に従っているかをチェックする
	 */
	public static function isEmail($argEmail){

		// メアドは全体で256文字まで
		if(strlen($argEmail) > 256){
			self::$_message = 'lengthover,email';
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
		}

		$localpart = NULL;
		$domainpart = NULL;
		$mailParts = explode('@', $argEmail);
		if(count($mailParts) < 2){
			self::$_message = 'countshort,email';
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
		}

		$domainPart = $mailParts[count($mailParts)-1];
		unset($mailParts[count($mailParts)-1]);
		$localPart = implode('@',$mailParts);

		// local-partのチェック
		if(strlen($localPart) > 0){
			try {
				self::isEmailLocalPart($mailParts[0]);
			}catch (Exception $Exception){
				//self::$_message = 'missmatch,localpart';
				throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__.PATH_SEPARATOR.$Exception->__toString());
			}
		}

		// domain-partのチェック
		try {
			self::isDomain($domainPart);
		}catch (Exception $Exception){
			// エラー部位識別に「part」を追記
			self::$_message .= 'part';
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__.PATH_SEPARATOR.$Exception->__toString());
		}

		return TRUE;
	}

	/**
	 * メールのローカルパートがRFC2822(+DoCoMoの拡張)に従っているかをチェックする
	 * RFC2822では 末尾の '.' は許されていないが, DoCoMoは許すので
	 * ここでも許している.
	 */
	public static function isEmailLocalPart ($argLocalPart){
		// '.' を除く利用してもよい文字
		$atext = "a-z0-9@!#\$%&'\"*+\-\/=?^_`{|}~";
		if (!preg_match('/^[' . $atext . '][\.' . $atext . ']*$/iD', $argLocalPart)) {
			self::$_message = 'missmatch,localpart';
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
		}
		return TRUE;
	}

	/**
	 * ドメイン名がRFC1035に従っているかをチェックする
	 */
	public static function isDomain($argDomain){

		// domainの長さは255文字まで
		if (strlen($argDomain) > 255) {
			self::$_message = 'lengthover,domain';
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
		}

		$domainLabels = explode('.', $argDomain);

		// 2つ以上の部分にわかれることを確認
		if ($domainLabels === FALSE || count($domainLabels) < 2) {
			self::$_message = 'countshort,domain';
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
		}

		foreach ($domainLabels as $label) {
			//ラベルの長さは1文字から63文字まで
			if (strlen($label) < 1){
				self::$_message = 'lengthshort,domain';
				throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
			}
			elseif (strlen($label) > 63) {
				self::$_message = 'lengthover,domain';
				throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
			}
			/*
			 * ラベルに用いてよい文字は [a-z0-9-]だが
			 * 最初と末尾は '-' は禁止されている
			 * ここでは, 1文字の場合を特別扱いしている.
			 */
			if (strlen($label) === 1) {
				if (preg_match('/^[a-z0-9]$/iD', $label) === 0) {
					self::$_message = 'missmatch,domain';
					throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
				}
			} else {
				if (preg_match('/^([a-z0-9][a-z0-9-]*[a-z0-9])$/iD', $label) === 0) {
					self::$_message = 'missmatch,domain';
					throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
				}
			}
		}
		return TRUE;
	}
}

?>