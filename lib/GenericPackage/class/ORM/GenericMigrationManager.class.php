<?php

class GenericMigrationManager {

	private static $_lastMigrationHash;

	/**
	 * 適用されていないマイグレーションを探して、あれば実行する。なければそのまま終了する
	 * @param instance $argDBO
	 * @return boolean
	 */
	public static function dispatchAll($argDBO, $argTblName=NULL){
		static $executed = FALSE;
		// 1プロセス内で2度も処理しない
		if(FALSE === $executed){
			// 適用差分を見つける
			self::$_lastMigrationHash = NULL;
			$diff = self::_getDiff($argDBO, $argTblName);
			debug('diff=');
			debug($diff);
			if(count($diff) > 0){
				// 差分の数だけマイグレーションを適用
				for($diffIdx=0; $diffIdx < count($diff); $diffIdx++){
					$migrationFilePath = getAutoMigrationPath().$argDBO->dbidentifykey.'.'.$diff[$diffIdx].'.migration.php';
					if(TRUE === file_exists($migrationFilePath) && TRUE === is_file($migrationFilePath)){
						@include_once $migrationFilePath;
						// migrationの実行
						$migration = new $diff[$diffIdx]();
						if(TRUE === $migration->up($argDBO)){
							debug('migration up! '.$diff[$diffIdx]);
							// マイグレーション済みに追加
							@file_put_contents_e(getAutoMigrationPath().$argDBO->dbidentifykey.'.dispatched.migrations', $diff[$diffIdx].PHP_EOL, FILE_APPEND);
						}
					}
				}
			}
			$executed = TRUE;
			if(NULL !== self::$_lastMigrationHash){
				return self::$_lastMigrationHash;
			}
		}
		return TRUE;
	}

	/**
	 * テーブルマイグレートを自動解決する
	 * @param unknown $argDBO
	 * @param unknown $argTable
	 * @return boolean
	 */
	public static function resolve($argDBO, $argTblName, $argLastMigrationHash=NULL){
		static $executed = array();
		// 1プロセス内で同じテーブルに対してのマイグレーションを2度も処理しない
		if(FALSE === (isset($executed[$argTblName]) && TRUE === $executed[$argTblName])){
			$firstMigration = TRUE;
			if(!isset(ORMapper::$modelHashs[$argTblName])){
				// コンソールから強制マイグレーションされる時に恐らくココを通る
				$nowModel = ORMapper::getModel($argDBO, $argTblName);
			}
			// XXX ORMapperとMigrationManagerは循環しているのでいじる時は気をつけて！
			$modelHash = ORMapper::$modelHashs[$argTblName];
			// modelハッシュがmigrationハッシュに含まれていないかどうか
			$migrationHash = $argLastMigrationHash;
			if(NULL === $migrationHash){
				// 既に見つけているマイグレーションハッシュから定義を取得する
				$diff = self::_getDiff($argDBO, $argTblName);
				if(NULL !== self::$_lastMigrationHash){
					$migrationHash = self::$_lastMigrationHash;
				}
			}
			debug('$migrationHash='.$migrationHash);
			debug('$modelHash='.$modelHash);
			// マイグレーションハッシュがある場合は
			if(NULL !== $migrationHash){
				if(FALSE !== strpos($migrationHash, $modelHash)){
					// このテーブルはマイグレーション済み
					$executed[$argTblName] = TRUE;
					// 現在のテーブル定義と最新のマイグレーションファイル上のテーブルハッシュに差分が無いので何もしない
					debug('exists migration! '.$migrationHash);
					return TRUE;
				}
				// 最後に適用している該当テーブルに対してのマイグレーションクラスを読み込んでmodelハッシュを比較する
				$migrationFilePath = getAutoMigrationPath().$argDBO->dbidentifykey.'.'.$migrationHash.'.migration.php';
				if(TRUE === file_exists($migrationFilePath) && TRUE === is_file($migrationFilePath)){
					// 既にテーブルはあるとココで断定
					$firstMigration = FALSE;
					// 直前のマイグレーションクラスをインスタンス化
					@include_once $migrationFilePath;
					// モデルハッシュが変わっているかどうかを比較
					if($modelHash == $migrationHash::$migrationHash){
						// このテーブルはマイグレーション済み
						$executed[$argTblName] = TRUE;
						// 現在のテーブル定義と最新のマイグレーションファイル上のテーブルハッシュに差分が無いので何もしない
						return TRUE;
					}
				}
			}

			// テーブル定義を取得
			$tableDefs = ORMapper::getModelPropertyDefs($argDBO, $argTblName);
			$describeDef = $tableDefs['describeDef'];

			$migrationClassDef = PHP_EOL;
			$migrationClassDef .= PHP_EOL . PHP_TAB . 'public function __construct(){' . PHP_EOL . PHP_TAB . PHP_TAB . str_replace('; ', ';' . PHP_EOL . PHP_TAB . PHP_TAB, $describeDef) . 'return;' . PHP_EOL . PHP_TAB . '}'. PHP_EOL;
			if(TRUE === $firstMigration){
				// create指示を生成
				$migrationClassDef .= PHP_EOL . PHP_TAB . 'public function up($argDBO){' . PHP_EOL . PHP_TAB . PHP_TAB . 'return $this->create($argDBO);' . PHP_EOL . PHP_TAB . '}'. PHP_EOL;
				// drop指示を生成
				$migrationClassDef .= PHP_EOL . PHP_TAB . 'public function down($argDBO){' . PHP_EOL . PHP_TAB . PHP_TAB . 'return $this->drop($argDBO);' . PHP_EOL . PHP_TAB . '}'. PHP_EOL;
			}
			else {
				// ALTERかDROP指示を生成
				$upAlterDef = '$alter = array(); ';
				$downAlterDef = '$alter = array(); ';
				// 差分をフィールドを走査して特定する
				$lastModel = new $migrationHash();
				$beforeDescribes = $lastModel->describes;
				$describes = array();
				$beforeFieldKey = NULL;
				eval(str_replace('$this->', '$', $describeDef));
				// 増えてる減ってるでループの起点を切り替え
				if(count($describes) >= count($beforeDescribes)) {
					// フィールドが増えている もしくは数は変わらない
					foreach($describes as $feldKey => $propary){
						// 最新のテーブル定義に合わせて
						$alter = NULL;
						if(!array_key_exists($feldKey, $beforeDescribes)){
							// 増えてるフィールドを単純に増やす
							$alter = 'ADD';
							$downAlterDef .= '$alter["'.$feldKey.'"] = array(); ';
							$downAlterDef .= '$alter["'.$feldKey.'"]["alter"] = "DROP"; ';
						}
						// 新旧フィールドのハッシュ値比較
						elseif(sha1(serialize($propary)) != sha1(serialize($beforeDescribes[$feldKey]))){
							// ハッシュ値が違うので新しいフィールド情報でAlterする
							$alter = 'MODIFY';
							// 元に戻すMODYFI
							$alterDefs = ORMapper::getModelPropertyDefs($argDBO, $argTblName, array($feldKey=>$beforeDescribes[$feldKey]));
							$downAlterDef .= str_replace('$this->describes = array(); ', '', $alterDefs['describeDef']);
							$downAlterDef .= '$alter["'.$feldKey.'"]["alter"] = "' . $alter . '"; ';
						}
						if(NULL === $alter){
							// 処理をスキップして次のループへ
							$beforeFieldKey = $feldKey;
							continue;
						}
						// up生成
						$alterDefs = ORMapper::getModelPropertyDefs($argDBO, $argTblName, array($feldKey=>$propary));
						$upAlterDef .= str_replace('$this->describes = array(); ', '', $alterDefs['describeDef']);
						$upAlterDef .= '$alter["'.$feldKey.'"]["alter"] = "' . $alter . '"; ';
						if('ADD' === $alter){
							if(NULL === $beforeFieldKey){
								// 先頭にフィールドが増えている
								$upAlterDef .= '$alter["'.$feldKey.'"]["first"] = TRUE;';
							}
							else {
								// ADDする箇所の指定
								$upAlterDef .= '$alter["'.$feldKey.'"]["after"] = "' . $beforeFieldKey . '";';
							}
						}
						$beforeFieldKey = $feldKey;
					}
				}
				else{
					// フィールドが減っている
					// XXX upとdownがただ増えている時と逆なだけ
					foreach($beforeDescribes as $feldKey => $propary){
						// 前のテーブル定義に合わせて
						$alter = NULL;
						if(!array_key_exists($feldKey, $describes)){
							// 減ってるフィールドを単純にARTER DROPする
							$alter = 'ADD';
							$upAlterDef .= '$alter["'.$feldKey.'"] = array(); ';
							$upAlterDef .= '$alter["'.$feldKey.'"]["alter"] = "DROP"; ';
						}
						// 新旧フィールドのハッシュ値比較
						elseif(sha1(serialize($propary)) != sha1(serialize($describes[$feldKey]))){
							// ハッシュ値が違うので新しいフィールド情報でAlterする
							$alter = 'MODIFY';
							// 元に戻すMODYFI
							$alterDefs = ORMapper::getModelPropertyDefs($argDBO, $argTblName, array($feldKey=>$describes[$feldKey]));
							$upAlterDef .= str_replace('$this->describes = array(); ', '', $alterDefs['describeDef']);
							$upAlterDef .= '$alter["'.$feldKey.'"]["alter"] = "' . $alter . '"; ';
						}
						if(NULL === $alter){
							// 処理をスキップして次のループへ
							$beforeFieldKey = $feldKey;
							continue;
						}
						// down生成
						$alterDefs = ORMapper::getModelPropertyDefs($argDBO, $argTblName, array($feldKey=>$propary));
						$downAlterDef .= str_replace('$this->describes = array(); ', '', $alterDefs['describeDef']);
						$downAlterDef .= '$alter["'.$feldKey.'"]["alter"] = "' . $alter . '"; ';
						if('ADD' === $alter){
							if(NULL === $beforeFieldKey){
								// 先頭にフィールドが増えている
								$downAlterDef .= '$alter["'.$feldKey.'"]["first"] = TRUE;';
							}
							else {
								// ADDする箇所の指定
								$downAlterDef .= '$alter["'.$feldKey.'"]["after"] = "' . $beforeFieldKey . '";';
							}
						}
						$beforeFieldKey = $feldKey;
					}
				}
				// alter指示を生成
				$migrationClassDef .= PHP_EOL . PHP_TAB . 'public function up($argDBO){' . PHP_EOL . PHP_TAB . PHP_TAB . str_replace('$this->describes', '$alter', str_replace('; ', ';' . PHP_EOL . PHP_TAB . PHP_TAB, $upAlterDef)) . PHP_EOL . PHP_TAB . PHP_TAB . 'return $this->alter($argDBO, $alter);' . PHP_EOL . PHP_TAB . '}'. PHP_EOL;
				$migrationClassDef .= PHP_EOL . PHP_TAB . 'public function down($argDBO){' . PHP_EOL . PHP_TAB . PHP_TAB . str_replace('$this->describes', '$alter', str_replace('; ', ';' . PHP_EOL . PHP_TAB . PHP_TAB, $downAlterDef)) . PHP_EOL . PHP_TAB . PHP_TAB . 'return $this->alter($argDBO, $alter);' . PHP_EOL . PHP_TAB . '}'. PHP_EOL;
			}

			// 現在の定義でマイグレーションファイルを生成する
			$migrationClassName = self::_createMigrationClassName($argTblName).'_'.$modelHash;
			$migrationClassDef = 'class '.$migrationClassName.' extends MigrationBase {' . PHP_EOL . PHP_EOL . PHP_TAB . 'public $tableName = "' . strtolower($argTblName) . '";' . PHP_EOL . PHP_EOL . PHP_TAB . 'public static $migrationHash = "' . $modelHash . '";' . $migrationClassDef . '}';
			$path = getAutoMigrationPath().$argDBO->dbidentifykey.'.'.$migrationClassName.'.migration.php';
			@file_put_contents($path, '<?php' . PHP_EOL . PHP_EOL . $migrationClassDef . PHP_EOL . PHP_EOL . '?>');
			@chmod($path, 0777);

			// 生成した場合は、生成環境のマイグレーションが最新で、適用済みと言う事になるので
			// マイグレーション済みファイルを生成し、新たにマイグレーション一覧に追記する
			@file_put_contents_e(getAutoMigrationPath().$argDBO->dbidentifykey.'.all.migrations', $migrationClassName.PHP_EOL, FILE_APPEND);
			@file_put_contents_e(getAutoMigrationPath().$argDBO->dbidentifykey.'.dispatched.migrations', $migrationClassName.PHP_EOL, FILE_APPEND);
			$executed[$argTblName] = TRUE;
			debug('migration! '.$migrationClassName);
		}
		return TRUE;
	}

	private static function _getDiff($argDBO, $argTblName){
		// 実行可能なmigrationの一覧を取得
		$migrationes = array();
		$migrationesFilePath = getAutoMigrationPath().$argDBO->dbidentifykey.'.all.migrations';
		if(TRUE === file_exists($migrationesFilePath) && TRUE === is_file($migrationesFilePath)){
			// 適用済みのmigratione一覧を取得
			$handle = fopen($migrationesFilePath, 'r');
			while(($line = fgets($handle, 4096)) !== false){
				$migrationes[] = trim($line);
			}
		}
		debug('dispatche all migrations=');
		debug($migrationesFilePath);
		debug($migrationes);
		$dispatchedMigrationesFilePath = getAutoMigrationPath().$argDBO->dbidentifykey.'.dispatched.migrations';
		$dispatchedMigrationes = array();
		if(TRUE === file_exists($dispatchedMigrationesFilePath) && TRUE === is_file($dispatchedMigrationesFilePath)){
			// 適用済みのmigratione一覧を取得
			$handle = fopen($dispatchedMigrationesFilePath, 'r');
			while(($line = fgets($handle, 4096)) !== false){
				$dispatchedMigrationes[] = trim($line);
			}
		}
		$dispatchedMigrationesStr = implode(':', $dispatchedMigrationes);
		debug('dispatched migrations='.$dispatchedMigrationesStr);
		self::$_lastMigrationHash = NULL;
		$diff = array();
		// 未適用の差分を探す
		for($migIdx=0; $migIdx < count($migrationes); $migIdx++){
			if(strlen($migrationes[$migIdx]) > 0){
				if('' === $dispatchedMigrationesStr){
					$diff[] = $migrationes[$migIdx];
				}
				elseif(FALSE === strpos($dispatchedMigrationesStr, $migrationes[$migIdx])){
					// 数が足りていないので、実行対象
					$diff[] = $migrationes[$migIdx];
				}
				// テーブル指定があった場合は、最後の該当テーブルに対するマイグレーションファイルを特定しておく
				if(NULL !== $argTblName){
					$migrationName = strtolower(ORMapper::getGeneratedModelName($argTblName));
					debug('check exists migration='. strtolower($migrationes[$migIdx]) . ' & ' . $migrationName.'migration_');
					if(FALSE !== strpos(strtolower($migrationes[$migIdx]), $migrationName.'migration_')){
						self::$_lastMigrationHash = $migrationes[$migIdx];
						debug('self::$_lastMigrationHash='.$migrationes[$migIdx]);
					}
				}
			}
		}
		return $diff;
	}

	private static function _createMigrationClassName($argTblName){
		$migrationName = ORMapper::getGeneratedModelName($argTblName);
		if((strlen($migrationName) - (strlen('migration'))) === strpos(strtolower($migrationName), 'migration')){
			// 何もしない
		}
		else{
			$migrationName = $migrationName."Migration";
		}
		return $migrationName;
	}

// 	/**
// 	 * 定義の存在チェック
// 	 * 現存する、最新のバージョン番号を返却する
// 	 * @param unknown $argDBO
// 	 * @param unknown $argTable
// 	 * @return mixied 正常終了時はint、以上の場合はFALSEを返す
// 	 */
// 	public static function is($argDBO, $argTable){
// 		if(class_exists('Configure') && NULL !== Configure::constant('LIB_DIR')){
// 			$dirPath = Configure::LIB_DIR . 'automigrate/' . $argTable;
// 			$isdir = file_exists($dirPath);
// 			if(TRUE === $isdir && $handle = opendir($dirPath)) {
// 				/* ディレクトリをループする際の正しい方法です */
// 				$version = 1;
// 				while(false !== ($entry = readdir($handle))) {
// 					if(0 < strpos($entry, $argTable)){
// 						$nowVersion = (int)substr($entry, 0, strpos($entry, $argTable) - 1);
// 						if($version < $nowVersion){
// 							$version = $nowVersion;
// 						}
// 					}
// 				}
// 				closedir($handle);
// 				return $version;
// 			}
// 		}
// 		return FALSE;
// 	}

	/**
	 * 定義の新規作成
	 * @param unknown $argDBO
	 * @param unknown $argTable
	 * @return boolean
	 */
	public static function create($argDBO, $argTable){
		$describes = $argDBO->getTableDescribes($argTable);
		if(is_array($describes) && count($describes) > 0){
			foreach($describes as $colName => $describe){
				$describe;
				// ちょっとこの続きは今度に・・・
				// $describeをそのまま書き出してしまう。後で差分チェックに配列をそのまま流用する為
				// create文と、$describeを同時に補完するクラスファイルを自動生成して終わらせる
			}
		}
		return FALSE;
	}

	/**
	 * 定義の更新
	 * @param unknown $argDBO
	 * @param unknown $argTable
	 * @param unknown $argVersion
	 */
	public static function modify($argDBO, $argTable, $argVersion){
		// 差分を作成し、バージョン番号をインクリメントして行く
		return FALSE;
	}

	/**
	 * 定義の破棄
	 * @param unknown $argDBO
	 * @param unknown $argTable
	 * @return boolean
	 */
	public static function remove($argDBO, $argTable){
		return FALSE;
	}

	/**
	 * 定義の適用
	 * @param unknown $argDBO
	 * @param unknown $argTable
	 * @return boolean
	 */
	public static function apply($argDBO, $argTable, $version){
		return FALSE;
	}
}

?>