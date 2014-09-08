<?php

abstract class GenericMigrationBase {

	public static $migrationHash = '';
	public $tableName = '';
	public $describes = array();

	private function _getFieldPropatyQuery($argDescribe){
		// create文を生成する
		$fieldDef = '';
		$pkeyDef = '';
		// XXX まだMySQLにしか対応してません！ゴメンナサイ！！
		foreach($argDescribe as $field => $propaty){
			if(strlen($fieldDef) > 0){
				// 2行目以降は頭に「,」付ける
				$fieldDef .= ', ';
			}
			$fieldDef .= '`' . $field . '`';
			if('string' === $propaty['type'] && isset($propaty['min-length'])){
				$fieldDef .= ' VARCHAR(' . $propaty['length'] . ')';
			}
			elseif('string' === $propaty['type']){
				$fieldDef .= ' CHAR(' . $propaty['length'] . ')';
			}
			elseif('int' === $propaty['type']){
				if(FALSE !== strpos($propaty['length'], ',')){
					// 小数点が在る場合
					$fieldDef .= ' DECIMAL(' . $propaty['length'] . ')';
				}
				else{
					$fieldDef .= ' INT(' . $propaty['length'] . ')';
				}
			}
			elseif('date' === $propaty['type']){
				$fieldDef .= ' DATETIME';
			}
			else {
				$fieldDef .= ' '.$propaty['type'];
			}
			if(FALSE === $propaty['null']){
				$fieldDef .= ' NOT NULL';
			}
			if(isset($propaty['default'])){
				$default = '\'' . $propaty['default'] . '\'';
				if('FALSE' === $default){
					$default = '\'0\'';
				}
				elseif('TRUE' === $default) {
					$default = '\'1\'';
				}
				elseif('NULL' === $default) {
					$default = 'NULL';
				}
				$fieldDef .= ' DEFAULT ' . $default;
			}
			if(isset($propaty['autoincrement']) && TRUE === $propaty['autoincrement']){
				$fieldDef .= ' AUTO_INCREMENT';
			}
			if(isset($propaty['comment'])){
				$fieldDef .= ' COMMENT \''. $propaty['comment'] .'\' ';
			}
			if(isset($propaty['pkey']) && TRUE === $propaty['pkey']){
				$pkeyDef .= ', PRIMARY KEY(`' . $field . '`)';
			}
		}
		if(FALSE !== strpos($pkeyDef, '), PRIMARY KEY(')){
			// 複合主キーが設定されていたらそれに従う
			$pkeyDef = str_replace('), PRIMARY KEY(', ',', $pkeyDef);
		}
		return array('fieldDef'=>$fieldDef, 'pkeyDef'=>$pkeyDef);
	}

	/**
	 * createのマイグレーションを適用する
	 * @param instance $argDBO
	 * @return boolean
	 */
	public function create($argDBO){
		$sql = '';
		$fielPropatyQuerys = $this->_getFieldPropatyQuery($this->describes);
		$pkeyDef = $fielPropatyQuerys['pkeyDef'];
		$fieldDef = $fielPropatyQuerys['fieldDef'];
		if(strlen($fieldDef) > 0){
			$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->tableName . '` (' . $fieldDef . $pkeyDef . ')';
			debug('migration create sql='.$sql);
			$argDBO->execute($sql);
			$argDBO->commit();
		}
		return TRUE;
	}

	/**
	 * dropのマイグレーションを適用する
	 * @param instance $argDBO
	 * @return boolean
	 */
	public function drop($argDBO){
		$sql = 'DROP TABLE `' . $this->tableName . '`';
		$argDBO->execute($sql);
		$argDBO->commit();
		return TRUE;
	}

	/**
	 * alterのマイグレーションを適用する
	 * @param instance $argDBO
	 * @return boolean
	 */
	public function alter($argDBO, $argDescribes){
		$executed = FALSE;
		// ALTERは一行づつ処理
		foreach($argDescribes as $field => $propaty){
			$sql = '';
			if('DROP' === $propaty['alter']){
				$sql = 'ALTER TABLE `' . $this->tableName . '` DROP COLUMN `' . $field . '`';
			}
			else{
				$fielPropatyQuerys = $this->_getFieldPropatyQuery(array($field => $propaty));
				$fieldDef = $fielPropatyQuerys['fieldDef'];
				if(strlen($fieldDef) > 0){
					$sql = 'ALTER TABLE `' . $this->tableName . '` ' . $propaty['alter'] . ' COLUMN ' . $fieldDef;
					if(isset($propaty['first']) && TRUE === $propaty['first']){
						$sql .= ' FIRST ';
					}
					else if(isset($propaty['after']) && 0 < strlen($propaty['after'])){
						$sql .= ' AFTER `'.$propaty['after'].'`';
					}
				}
			}
			if(strlen($sql) > 0){
				try {
					debug('migration alter sql='.$sql);
					$argDBO->execute($sql);
					$executed = TRUE;
				}
				catch (Exception $Exception){
					logging($Exception->getMessage(), 'exception');
					// ALTERのADDは、2重実行でエラーになるので、ここでのExceptionは無視してModfyを実行してみる
					$sql = str_replace('ALTER TABLE `' . $this->tableName . '` ' . $propaty['alter'] . ' COLUMN ', 'ALTER TABLE `' . $this->tableName . '` MODIFY COLUMN ', $sql);
					// MODIFYに変えて実行しなおし
					$argDBO->execute($sql);
					$executed = TRUE;
					// XXX それでもダメならException！
				}
			}
		}
		if(TRUE === $executed){
			$argDBO->commit();
		}
		return TRUE;
	}
}

?>