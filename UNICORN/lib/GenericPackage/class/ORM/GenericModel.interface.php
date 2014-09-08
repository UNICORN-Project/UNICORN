<?php

/**
 * モデルクラスの親クラス
 */
interface GenericModel{

	/**
	 * レコードの読み込み
	 */
	public function load($argExtractionCondition=NULL, $argBinds=NULL);

	public function __call($argMethodName, $arguments);

	/**
	 * 連想配列で値をセット
	 * replace属性が書き変わらない値のセット
	 * @param array $argments
	*/
	public function init($argments);

	/**
	 * 連想配列で値をセット
	 * @param array $argments
	*/
	public function sets($argments);

	/**
	 * 割り当て
	 * @param string $argKey
	 * @param mixid $argVal
	*/
	public function set($argKey,&$argVal);

	public function save($argments, $argReplaced=TRUE);

	public function remove();

	public function next();

	public function getNextModel();

	/**
	 * NULL値の許容設定に合致しているかどうかを判定する
	 * @param string $argKey
	 * @param mixid $argValue
	 * @throws Exception
	*/
	public function validateNULL($argKey, $argValue);

	public function validateType($argKey,$argValue);

	public function validateLength($argKey, $argValue);

	public function validate($argKey,$argValue);
}