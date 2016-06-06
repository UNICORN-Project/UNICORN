# UIテストツール「Shouldbee」をCLIから実行してテストする手順

* 参考URL: http://docs.shouldbee.at/cli/

## shouldbeeの認証情報を設定する
export SHOULDBEE_USERNAME="shouldbee@ui.test.com"
export SHOULDBEE_PASSWORD="abcd1234"

## テストコードを変換する
##### iMacrosから、Shouldbee用テストコードの雛形を作成する
/supple/convertshouldbee.php
##### 雛形テストコードを、公式のテストコードと照らしあわせつつ、場合によってはHtmlに属性を追加したりなどする
http://docs.shouldbee.at/steps/

## テストを実行する
cd test/UI
shouldbee run
