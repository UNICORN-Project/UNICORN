<?php

// 4行目と5行目はインストーラによって自動で書き換えられる事に注意して下さい！
$projectpkgName = "Project";
$fwpath = dirname(dirname(dirname(dirname(__FILE__))))."/lib/FrameworkPackage";
// ※ココより上はインストーラーから自動で書き換えられるので、行を移動しないで下さい！内容は変えてもいいです。


// 出力エンコードの明示指定
mb_http_output("UTF-8");

// 内部文字エンコードの明示指定
mb_internal_encoding("UTF-8");

// フレームワーク利用を開始する
// PROJECT_NAMEは任意指定のパラメータ
// コレの値とプロジェクト用のメインConfigureの名前を合わせておくと、色々な設定の自動走査・解決をしてくれる
define("PROJECT_NAME", $projectpkgName);
// フレームワークのコアファイルを読み込み
require_once $fwpath."/core/UNICORN";

// フレームワークのMVCフレームワーク機能(REST)を使う
Core::webmain();

?>