<?php

class Rest extends RestControllerBase {

	/**
	 * リソースの参照
	 * @return mixed 成功時は最新のリソース配列 失敗時はFALSE
	 */
	public function get(){
		return parent::get();
	}

	/**
	 * リソースの作成・更新・インクリメント・デクリメント
	 * @return mixed 成功時は最新のリソース配列 失敗時はFALSE
	 */
	public function post($argRequestParams = NULL){
		return parent::post($argRequestParams);
	}

	/**
	 * リソースの作成・更新
	 * @return mixed 成功時は最新のリソース配列 失敗時はFALSE
	 */
	public function put($argRequestParams = NULL){
		return parent::put($argRequestParams);
	}

	/**
	 * リソースの削除
	 * @return boolean
	 */
	public function delete(){
		return parent::delete();
	}

	/**
	 * リソースの情報の取得
	 * @return boolean
	 */
	public function head(){
		return parent::head();
	}
}

?>