#!/bin/bash

# 定数定義
# shouldbeeのID PASSをexport
export SHOULDBEE_USERNAME="shouldbee@ui.test.com"
export SHOULDBEE_PASSWORD="abcd1234"
# test path
testpath=/var/www/release/test/UI/

# 変数定義
# Slack設定(webhookurlがある場合のみ処理されます)
webhookurl=https://hooks.slack.com/services/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
# サーバー監視通知チャンネル
channelname=projects
# 通知先(@xxx @xxx...形式で指定)
users='@saimushi '

# チャットワーク設定(apitokenが在る場合のみ処理されます)
apitoken=''
# サーバー監視通知ルーム
roomid=''
# 通知先([To:1234] [To:1234]...形式で指定)
members='[To:1234] [To:1234]'

isdev=/var/www/.dev
isProd=/var/www/.production
# 環境によって設定が別れる変数の定義
devbacketname=unicorndev
prodbacketname=unicorn

# 環境による設定の振り分け
backetname=$prodbacketname
if [ -e $isProd ]; then
   rm -rf $isdev
else
  if [ -e $isdev ]; then
    # 開発用のバケットに向ける
    backetname=$devbacketname
  fi
fi

# タイムゾーンの設定変更
\cp -f /usr/share/zoneinfo/Japan /etc/localtime

# 時刻合わせ
chkconfig ntpd on

# メモリー解放
free
sync
echo 3 > /proc/sys/vm/drop_caches
free

# 変数の初期化(弄らないで下さい！)
test_success=0
executed_title=''
executed_body=''

# テストケースファイルの存在チェック
if [ ! -e ${testpath}shouldbee ]; then
  exit 0
fi

# shouldbee cliがインストール済みか確認
if [ ! -e /usr/bin/shouldbee ]; then
  # shouldbee cli for linuxをダウンロード
  wget https://github.com/shouldbee/homebrew-shouldbee/blob/master/build/linux-amd64/shouldbee?raw=true -O shouldbee
  mv shouldbee /usr/bin
  chmod 0755 /usr/bin/shouldbee
fi

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
  executed_title="${backetname} UITest成功のお知らせ"
  executed_body="テストは正常に終了しました。テストケースに不足が無いかもう一度確認してから、テストチケットを終了にして下さい。"
else
  # 失敗しているケース
  executed_title="${backetname} UITest失敗のお知らせ"
  executed_body="・html上の属性や、クラス名に齟齬が発生していないかどうか確認して下さい。・決済APIなど、外部サービスが正しく稼働しているかどうか確認して下さい。"
fi

# チャットワークに結果送信
if [ 0 -lt ${#apitoken} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/uitest-chatwork.txt
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
  _body=`cat /tmp/logrotate-chatwork.txt`
  _body=${_body//\\/\\\\}
  _body=${_body//\"/\\\"}
  echo ${_body}
  curl -X POST -H "X-ChatWorkToken: ${apitoken}" -d "body=${_body}" "https://api.chatwork.com/v1/rooms/${roomid}/messages" > /dev/null
fi

# Slackに結果送信
if [ 0 -lt ${#webhookurl} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/uitest-slack.txt
${executed_title}
詳しいテスト結果を確認して下さい。
${executed_body}

テスト結果：
${execute}

テスト結果詳細の参照：
https://shouldbee.at/app

_EOT_
  _body=`cat /tmp/logrotate-slack.txt`
  _body=${_body//\\/\\\\}
  _body=${_body//\"/\\\"}
  echo ${_body}
  #slack 送信チャンネル
  CHANNEL=${CHANNEL:-"${channelname}"}
  #slack 送信名
  BOTNAME=${BOTNAME:-"servermonitor"}
  #slack アイコン
  FACEICON=${FACEICON:-":tophat:"}
  #見出しとなるようなメッセージ
  MESSAGE=${MESSAGE:-"${users} "}
  WEBMESSAGE='```'${_body}'```'
  echo ${WEBMESSAGE}
  #Incoming WebHooks送信
  curl -s -S -X POST --data-urlencode "payload={\"channel\": \"${CHANNEL}\", \"username\": \"${BOTNAME}\", \"icon_emoji\": \"${FACEICON}\", \"text\": \"${MESSAGE}${WEBMESSAGE}\" }" ${webhookurl} >/dev/null
fi

exit 0
