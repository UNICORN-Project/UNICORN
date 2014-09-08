<?php

class RESTException extends Exception
{
	public function __construct($argError, $argCode=NULL, $argPrevius=NULL){
		// 書き換える前のエラーをロギングしておく
		logging($argError.PATH_SEPARATOR.var_export(debug_backtrace(),TRUE), 'exception');
		debug($argError);
		// 通常は500版のインターナルサーバエラー
		$msg = 'Internal Server Error';
		// RESTfulエラーコード＆メッセージ定義
		if(400 === $argCode){
			// バリデーションエラー等、必須パラメータの有無等の理由によるリクエスト自体の不正
			$msg = 'Bad Request';
		}
		elseif(401 === $argCode){
			// ユーザー認証の失敗
			$msg = 'Unauthorized';
		}
		elseif(404 === $argCode){
			// 許可されていない(もしくは未定義の)リソース(モデル)へのアクセス
			$msg = 'Not Found';
		}
		elseif(405 === $argCode){
			// 許可されていない(もしくは未定義の)リソースメソッドの実行
			$msg = 'Method Not Allowed';
		}
		elseif(503 === $argCode){
			// メンテナンスや制限ユーザー等の理由による一時利用の制限中
			$msg = 'Service Unavailable';
		}
		parent::__construct($msg, $argCode, $argPrevius);
	}
}

?>