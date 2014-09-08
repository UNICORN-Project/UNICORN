<?php

class SessionsMigration_b8db598aec6558c2ad3b94dfd8f6cfb0b5921504 extends MigrationBase {

	public $tableName = "sessions";

	public static $migrationHash = "b8db598aec6558c2ad3b94dfd8f6cfb0b5921504";

	public function __construct(){
		$this->describes = array();
		$this->describes["token"] = array();
		$this->describes["token"]["type"] = "string";
		$this->describes["token"]["null"] = FALSE;
		$this->describes["token"]["pkey"] = TRUE;
		$this->describes["token"]["length"] = "255";
		$this->describes["token"]["min-length"] = 1;
		$this->describes["token"]["autoincrement"] = FALSE;
		$this->describes["token"]["comment"] = "ワンタイムトークン";
		$this->describes["created"] = array();
		$this->describes["created"]["type"] = "date";
		$this->describes["created"]["null"] = FALSE;
		$this->describes["created"]["pkey"] = FALSE;
		$this->describes["created"]["min-length"] = 1;
		$this->describes["created"]["autoincrement"] = FALSE;
		$this->describes["created"]["comment"] = "トークン作成日時";
		return;
	}

	public function up($argDBO){
		return $this->create($argDBO);
	}

	public function down($argDBO){
		return $this->drop($argDBO);
	}
}

?>