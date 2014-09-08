<?php

class Project extends RestControllerBase {

	public function get(){
		// プロジェクトの一覧を返す
		$dirs = array();
		$conName = PROJECT_NAME."Configure";
		$basedir = dirname($conName::PROJECT_ROOT_PATH);
		debug($basedir);
		if ($handle = opendir($basedir)) {
			while (false !== ($file = readdir($handle))) {
				if("." !== $file && ".." !== $file && FALSE !== is_dir($basedir."/".$file) && is_file($basedir."/".$file."/.projectpackage")){
					$dirs[] = $file;
				}
			}
			closedir($handle);
		}
		return $dirs;
	}

	public function post(){
		// このRESTは実行出来ない
		throw new RESTException(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__, 405);
	}

	public function put(){
		// このRESTは実行出来ない
		throw new RESTException(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__, 405);
	}

	public function delete(){
		// このRESTは実行出来ない
		throw new RESTException(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__, 405);
	}
}

?>