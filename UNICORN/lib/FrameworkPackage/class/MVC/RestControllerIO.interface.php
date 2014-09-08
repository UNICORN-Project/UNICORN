<?php

interface RestControllerIO {

	/**
	 * GETメソッド
	 */
	public function get();

	/**
	 * POSTメソッド
	 */
	public function post();

	/**
	 * PUTメソッド
	 */
	public function put();

	/**
	 * DELETEメソッド
	 */
	public function delete();
}

?>