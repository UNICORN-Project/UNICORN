<?php

$projectpkgName = "FrameworkManager";
if (isset($argv) && isset($argv[1])){
	$projectpkgName = $argv[1];
}
$fwpath = dirname(dirname(dirname(dirname(__FILE__))))."/lib/FrameworkPackage";

// フレームワーク利用を開始する
// PROJECT_NAMEは任意指定のパラメータ
// コレの値とプロジェクト用のメインConfigureの名前を合わせておくと、色々な設定の自動走査・解決をしてくれる
define("PROJECT_NAME", $projectpkgName);
// フレームワークのコアファイルを読み込み
require_once $fwpath."/core/UNICORN";

// マイグレーションを実行
MigrationManager::dispatchAll ( DBO::sharedInstance () );

?>