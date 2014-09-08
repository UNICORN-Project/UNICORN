<?php

/**
 * モデルクラスの親クラス
 */
class GenericORMapper {

	private static $_models;
	public static $modelHashs = array();

	/**
	 * コンストラクタ
	*/
	private function __construct(){
	}

	/**
	 * エイリアス
	 */
	public static function getModel($argDBO, $argModelName, $argExtractionCondition=NULL, $argBinds=NULL, $argSeqQuery=NULL){
		return self::getAutoGenerateModel($argDBO, $argModelName, $argExtractionCondition, $argBinds, $argSeqQuery);
	}

	/**
	 * モデルクラスを自動生成して返す
	 * 2.0よりオートマイグレーションが付きました！
	 */
	public static function getAutoGenerateModel($argDBO, $argModelName, $argExtractionCondition=NULL, $argBinds=NULL, $argSeqQuery=NULL){

		// モデルクラス名とテーブル名を特定する
		$tableName = $argModelName;
		$modelName = self::getGeneratedModelName($tableName);

		// テーブル名末尾の数値は、ナンバリングテーブル名だと過程して、外す
		$matches = NULL;
		$unNumberingModelName = NULL;
		preg_match('/^([^0-9]+)[0-9]+$/', $modelName, $matches);
		if(is_array($matches) && isset($matches[1]) && strlen($matches[1]) > 0){
			$unNumberingModelName = $matches[1];
		}

		// モデルクラス名と、テーブル名の最終調整
		if((strlen($modelName) -5) === strpos(strtolower($modelName), "model")){
			$tableName = substr($tableName, 0, strlen($tableName)-5);
		}else{
			$modelName = $modelName."Model";
		}
		$tableName = ucfirst($tableName);

		// オートマイグレートその1
		$lastMigrationHash = NULL;
		if(function_exists('getAutoMigrationEnabled') && TRUE === getAutoMigrationEnabled()){
			// 未適用のmigrationがあれば、実行する
			$lastMigrationHash = MigrationManager::dispatchAll($argDBO, $tableName);
			if(TRUE === $lastMigrationHash){
				$lastMigrationHash = NULL;
			}
		}

		// モデルがまだ未生成ならモデルをテーブル定義から生成する
		if(!isset(self::$_models[$tableName])){
			// 親クラスを決める
			$superModelName = "ModelBase";
			if(class_exists($modelName."Extension")){
				$superModelName = $modelName."Extension";
			}
			elseif(NULL !== $unNumberingModelName && class_exists($unNumberingModelName."Extension")){
				$superModelName = $unNumberingModelName."Extension";
			}
			// 上で見つからなければdefault.modelmainも探してみる
			if("ModelBase" === $superModelName){
				loadModule("default.modelmain.".$modelName."Extension", TRUE);
				if(class_exists($modelName."Extension")){
					$superModelName = $modelName."Extension";
				}
				elseif(NULL !== $unNumberingModelName){
					loadModule("default.modelmain.".$unNumberingModelName."Extension", TRUE);
					if(class_exists($unNumberingModelName."Extension")){
						$superModelName = $unNumberingModelName."Extension";
					}
				}
			}

			// テーブル定義を取得
			$tableDefs = self::getModelPropertyDefs($argDBO, $tableName);
			$varDef = $tableDefs['varDef'];
			$describeDef = $tableDefs['describeDef'];

			// モデルクラスの自動生成
			$varDef .= "public \$sequenceSelectQuery = \"" . $argSeqQuery . "\"; ";
			// InterfaceはフレームワークのmodelクラスでI/Oの実装を強制する
			$baseModelClassDefine = "class " . $modelName . " extends " . $superModelName . " implements Model { %vars% public function __construct(\$argDBO, \$argExtractionCondition=NULL, \$argBinds=NULL){ %describes% parent::__construct(\$argDBO, \$argExtractionCondition, \$argBinds); } }";
			$baseModelClassDefine = str_replace("%vars%", $varDef, $baseModelClassDefine);
			$baseModelClassDefine = str_replace("%describes%", $describeDef, $baseModelClassDefine);

			// モデルクラス定義からクラス生成
			eval($baseModelClassDefine);

			// 生成したクラスを取っておく
			self::$_models[$tableName] = $modelName;

			// オートマイグレーションが有効だった場合、定義の更新が無いか確認
			if(function_exists('getAutoMigrationEnabled') && TRUE === getAutoMigrationEnabled()){
				// あれば新しいマイグレーションファイルを生成
				MigrationManager::resolve($argDBO, $tableName, $lastMigrationHash);
			}
		}
		$model = new self::$_models[$tableName]($argDBO, $argExtractionCondition, $argBinds);
		$model->className = $modelName;

		// テーブル定義のハッシュ値を取っておく
		self::$modelHashs[$tableName] = sha1(serialize($model->describes));

		return $model;
	}

	public static function getModelPropertyDefs($argDBO, $tableName, $argDescribes=NULL){
		$describes = $argDescribes;
		if(NULL === $describes){
			// テーブル定義を取得
			$describes = $argDBO->getTableDescribes($tableName);
		}
		$describeDef = "\$this->describes = array(); ";
		$varDef = NULL;
		$pkeysVarDef = "public \$pkeys = array(";
		$pkeyCnt = 0;
		if(is_array($describes) && count($describes) > 0){
			foreach($describes as $colName => $describe){
				// 小文字で揃える(Oracle向けの対応)
				$colName = strtolower($colName);
				$escape = "";
				if("int" !== $describe["type"] && "bool" !== $describe["type"]){
					$escape = "\"";
				}
				if(isset($describe["type"]) && "bool" === $describe["type"] && isset($describe["default"])){
					if(TRUE === $describe["default"]){
						$describe["default"] = "TRUE";
					}
					elseif(FALSE === $describe["default"]){
						$describe["default"] = "FALSE";
					}
				}
				if(isset($describe["default"]) && NULL === $describe["default"]){
					$describe["default"] = "NULL";
				}
				if(TRUE === $describe["null"]){
					$describe["null"] = "TRUE";
				}
				elseif(FALSE === $describe["null"]){
					$describe["null"] = "FALSE";
				}
				if(TRUE === $describe["pkey"]){
					$describe["pkey"] = "TRUE";
				}
				elseif(FALSE === $describe["pkey"]){
					$describe["pkey"] = "FALSE";
				}
				if(TRUE === $describe["autoincrement"]){
					$describe["autoincrement"] = "TRUE";
				}
				elseif(FALSE === $describe["autoincrement"]){
					$describe["autoincrement"] = "FALSE";
				}
				$describeDef .= "\$this->describes[\"" . $colName . "\"] = array(); ";
				$describeDef .= "\$this->describes[\"" . $colName . "\"][\"type\"] = \"" . $describe["type"] . "\"; ";
				if(isset($describe["default"]) && FALSE !== $describe["default"]){
					if("NULL" !== $describe["default"]){
						$describeDef .= "\$this->describes[\"" . $colName . "\"][\"default\"] = " . $escape . $describe["default"] . $escape . "; ";
					}
					else{
						$describeDef .= "\$this->describes[\"" . $colName . "\"][\"default\"] = " . $describe["default"] . "; ";
					}
				}
				$describeDef .= "\$this->describes[\"" . $colName . "\"][\"null\"] = " . $describe["null"] . "; ";
				$describeDef .= "\$this->describes[\"" . $colName . "\"][\"pkey\"] = " . $describe["pkey"] . "; ";
				if(isset($describe["length"])){
					$describeDef .= "\$this->describes[\"" . $colName . "\"][\"length\"] = \"" . $describe["length"] . "\"; ";
				}
				if(isset($describe["min-length"])){
					$describeDef .= "\$this->describes[\"" . $colName . "\"][\"min-length\"] = " . $describe["min-length"] . "; ";
				}
				$describeDef .= "\$this->describes[\"" . $colName . "\"][\"autoincrement\"] = " . $describe["autoincrement"] . "; ";
				if(isset($describe["comment"])){
					$describeDef .= "\$this->describes[\"" . $colName . "\"][\"comment\"] = \"" . $describe["comment"] . "\"; ";
				}
				$varDef .= "public \$" . $colName;
				if(isset($describe["default"]) && strlen($describe["default"]) > 0){
					$varDef .= " = " . $escape . $describe["default"] . $escape;
				}
				elseif(isset($describe["null"]) && "TRUE" === $describe["null"]){
					$varDef .= " = NULL";
				}
				$varDef .= "; ";
				if(0 === $pkeyCnt && isset($describe["pkey"]) && "TRUE" === $describe["pkey"]){
					$varDef .= "public \$pkeyName = \"" . $colName . "\"; ";
					$pkeyCnt++;
				}
				if(isset($describe["pkey"]) && "TRUE" === $describe["pkey"]){
					$pkeysVarDef .= "\"" . $colName . "\", ";
				}
			}
			$pkeysVarDef .= "); ";
			$varDef .= $pkeysVarDef;
			$varDef .= "public \$tableName = \"" . $tableName . "\"; ";
			return array('varDef' => $varDef, 'describeDef' => $describeDef);
		}
		else {
			throw new Exception(__CLASS__.PATH_SEPARATOR.__METHOD__.PATH_SEPARATOR.__LINE__);
		}
	}

	/**
	 * テーブル名をモデル名に変換する
	 * @param unknown $argTableName
	 * @return unknown
	 */
	public static function getGeneratedModelName($argTableName){
		// モデルクラス名とテーブル名を特定する
		$tableName = $argTableName;
		$modelName = ucfirst($tableName);
		$modelName = str_replace("_", " ", $modelName);
		$modelName = ucwords($modelName);
		$modelName = str_replace(" ", "", $modelName);
		return $modelName;
	}
}

?>
