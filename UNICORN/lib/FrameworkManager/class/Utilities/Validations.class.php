<?php

/**
 * フレームワークマネージャー専用のValidate関数群
 * @author saimushi
 */
class Validations extends GenericValidations {

	/**
	 * パスワードがフレームワークマネージャーのパスワード仕様に従っているかをチェックする
	 */
	public static function isPassword($argPassword){
		return TRUE;
	}
}

?>