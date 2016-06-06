#!/bin/bash

# 定数定義
# test path
testpath=/var/www/release/test/UI/
chatwork_token=abcd1234abcd1234

# テスト失敗時の通知先
all_roomid=35112178
alls_members='[To:1746639] [To:1747079] [To:1746650] [To:319360] [To:1746664] [To:1746665] [To:1746642]'

# テスト成功時の通知先
tech_roomid=40275129
techs_members='[To:1746664] [To:1746665] [To:1746642]'

# 変数の初期化(弄らないで下さい！)
test_success=0
executed_title=''
executed_body=''
room_id=0
members=''

# shouldbee cliがインストール済みか確認
if [ ! -e /usr/bin/shouldbee ]; then
  # shouldbee cli for linuxをダウンロード
  wget https://github.com/shouldbee/homebrew-shouldbee/blob/master/build/linux-amd64/shouldbee?raw=true -O shouldbee
  mv shouldbee /usr/bin
  chmod 0755 /usr/bin/shouldbee
fi

# テストケースファイルの存在チェック
if [ ! -e ${testpath}shouldbee ]; then
  exit 0
fi

# shouldbeeのID PASSをexport
export SHOULDBEE_USERNAME="shouldbee@ui.test.com"
export SHOULDBEE_PASSWORD="abcd1234"

# テスト実行
cd $testpath
execute=`shouldbee run`
echo $execute | grep 'failed 0' > /dev/null
if [ $? -eq 0 ]; then
  test_success=1
fi

# チャットワークへテスト結果を通知
if [ $test_success -eq 1 ]; then
  # 成功ケース
  executed_title='Anlimited UITest成功のお知らせ'
  executed_body="テストは正常に終了しました。テストケースに不足が無いかもう一度確認してから、テストチケットを終了にして下さい。"
  room_id=$tech_roomid
  members=$techs_members
else
  # 失敗しているケース
  executed_title='Anlimited UITest失敗のお知らせ'
  executed_body="・html上の属性や、クラス名に齟齬が発生していないかどうか確認して下さい。・決済APIなど、外部サービスが正しく稼働しているかどうか確認して下さい。"
  room_id=$all_roomid
  members=$alls_members
fi

cat << _EOT_ > /tmp/msg.txt
[info]
[title]
${executed_title}
[/title]
${members}
詳しいテスト結果を確認して下さい。
${executed_body}

テスト結果：
${execute}

テスト結果詳細の参照：
https://shouldbee.at/app
[/info]

_EOT_

_body=`cat /tmp/msg.txt`
curl -X POST -H "X-ChatWorkToken: ${chatwork_token}" -d "body=${_body}" "https://api.chatwork.com/v1/rooms/${room_id}/messages" > /dev/null

exit 0
