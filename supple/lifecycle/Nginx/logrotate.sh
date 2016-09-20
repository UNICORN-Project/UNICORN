#!/bin/bash

# 自分自身のファイルパス
mepath=${0}

# Fluentdによるサーバーログ収集設定
# (!!!) EC2&S3専用です！
# ※ デフォルトは毎日朝5時10分に自動ローテートが走るように設定されます。
# 
# Create by S.Ohno
# Create at 2016-09-15

# ------------------------------- 取扱説明書ココから (必読) ------------------------------- 
# ルート権限で実行して下さい。
# パスは変えていいですが、ファイル名は変えないで下さい。スクリプト上で利用しています。
# 環境に合わせて変数を適宜書き換えて下さい。
# developとproductionで動作を分けたい場合は「isdev」「isProd」変数のファイルパス設定通りにファイルを作成・配置して下さい。
# オートスケール時も動作するようになっているので、インスタンスの初期化時に実行するか
# 実行済みのインスタンスイメージをオートスケールに登録して下さい。
# デフォルトはProductionモードで動作します。
#
# ------------------------------- 取扱説明書ココまで (必読) ------------------------------- 


# ------------------------------- 変数定義ココから(適宜変更) ------------------------------- 

# 環境変数
# AWS IAMのS3リードライト権限のあるKEY情報を定義
export AWS_ACCESS_KEY_ID=AKIAJ24CTQHWUMKUE7CQ
export AWS_SECRET_ACCESS_KEY=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
export AWS_DEFAULT_REGION=ap-northeast-1

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

# メール設定(メール通知が在る場合は通知先としても使われる)
notifymail=saimushi@gmail.com

# プロジェクト名(UNICORN以外の場合は''(空文字)にして下さい)
projectname='Project'
# httpd実行時の権限ユーザー名
httpduser=nginx
# Nginxの設定再読み込みコマンドの定義(最後に自動実行してくれる)
restartcmd="service nginx reload"
# Apacheの場合は↓を使って下さい
#restartcmd="service httpd reload"
# s3をマウントするパス設定(パスは無い自動生成されます)
mountpath=/s3mnt
# TOPドメイン用SSL証明書配置パス(パスは無い自動生成されます・httpdの設定・変更は自動ではしません)
topdomainsslpath=/var/www/.ssl
# Webドメイン用SSL証明書配置パス(パスは無い自動生成されます・httpdの設定・変更は自動ではしません)
webdomainsslpath=/var/www/.ssl
# APIドメイン用SSL証明書配置パス(パスは無い自動生成されます・httpdの設定・変更は自動ではしません)
apidomainsslpath=/var/www/.ssl
# 管理ページドメイン用SSL証明書配置パス(パスは無い自動生成されます・httpdの設定・変更は自動ではしません)
mgrdomainsslpath=/var/www/.ssl
# 静的ファイルドメイン用SSL証明書配置パス(パスは無い自動生成されます・httpdの設定・変更は自動ではしません)
staticdomainsslpath=/var/www/.ssl
# アプリドメイン用SSL証明書配置パス(パスは無い自動生成されます・httpdの設定・変更は自動ではしません)
appdomainsslpath=/var/www/.ssl
topdomaindocumentrootpath=/var/www/release/lib/ProjectPackage/webdocs
webdomaindocumentrootpath=/var/www/release/lib/ProjectPackage/webdocs
apidomaindocumentrootpath=/var/www/release/lib/ProjectPackage/apidocs
mgrdomaindocumentrootpath=/var/www/release/lib/FrameworkManager/template/managedocs
staticdomaindocumentrootpath=/var/www/release/lib/ProjectPackage/webdocs
appdomaindocumentrootpath=/var/www/release/lib/ProjectPackage/webdocs
# ログパス
logpath=/var/www/logs
isdev=/var/www/.dev
isProd=/var/www/.production

# 環境によって設定が別れる変数の定義
# 開発環境用
devtopdomain=devproject.domain
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
devwebdomain=''
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
devapidomain=devapiproject.domain
devmgrdomain=devfwmproject.domain
devstaticdomain=''
devappdomain=''
devbacketname=projectbacuketdev
devregion=ap-northeast-1
# リリース環境用
prodtopdomain=project.domain
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
prodwebdomain=''
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
prodapidomain=apiproject.domain
prodmgrdomain=fwmproject.domain
prodstaticdomain=''
prodappdomain=''
prodbacketname=projectbacuket
prodregion=ap-northeast-1

# ------------------------------- 変数定義ココまで(適宜変更) ------------------------------- 

# 環境による設定の振り分け
backetname=$prodbacketname
region=$prodregion
topdomain=$prodtopdomain
webdomain=$prodwebdomain
apidomain=$prodapidomain
mgrdomain=$prodmgrdomain
staticdomain=$prodstaticdomain
appdomain=$prodappdomain
if [ -e $isProd ]; then
   rm -rf $isdev
else
  if [ -e $isdev ]; then
    # 開発用のバケットに向ける
    backetname=$devbacketname
    region=$devregion
    topdomain=$devtopdomain
    webdomain=$devwebdomain
    apidomain=$devapidomain
    mgrdomain=$devmgrdomain
    staticdomain=$devstaticdomain
    appdomain=$devappdomain
  fi
fi

# メモリー解放
free
sync
echo 3 > /proc/sys/vm/drop_caches
free

# タイムゾーンの設定変更
\cp -f /usr/share/zoneinfo/Japan /etc/localtime

# 時刻合わせ
chkconfig ntpd on

# ホスト名をアプリケーションプレフィックス-LocalIPｂの形に変更する
EC2_PREFIX=$backetname
LOCAL_IPV4=`curl -s http://169.254.169.254/latest/meta-data/local-ipv4`
NAME=${EC2_PREFIX}-${LOCAL_IPV4//./-}
echo "NAME: " ${NAME}
hostname ${NAME}

# 必要なミドルウェアの自動インストール
# リポジトリの向け先を一度latestに向ける
if ! grep "releasever=latest" /etc/yum.conf > /dev/null 2>&1; then
  # repoの向け先をlatesに向ける
  sed -i -e "14a releasever=latest" /etc/yum.conf
  yum clean all
fi

# mlocateのインストール
if [ ! -e /var/lib/mlocate ]; then
  yum install mlocate -y
fi

# OpenSSLのインストール
if [ ! -e /usr/bin/openssl ]; then
  yum install openssl -y
fi
# OpenSSLのアップデート
yum update openssl -y

# gitのインストール
if [ ! -e /usr/share/git-core/ ]; then
  yum install -y git
fi

# golangのインストール
if [ ! -e /usr/bin/go ]; then
  yum install golang fuse -y
  mkdir -p /etc/gocode/
fi

# goofysのインストール
export GOPATH=/etc/gocode
if [ ! -e /etc/gocode/bin/goofys ]; then
  go get github.com/kahing/goofys
  go install github.com/kahing/goofys
fi

# goofysでS3マウントしてSSLの設定を各Webサーバで共有する
if [ ! -e ${mountpath}/ ]; then
  mkdir -p ${mountpath}
  aws s3 mb s3://${backetname}-mnt
  # S3をマウント
  /etc/gocode/bin/goofys -o allow_other --uid "$(id -u nginx)" --gid "$(id -g nginx)" ${backetname}-mnt ${mountpath}
fi
# 3bkの場合
#aws s3 mb s3://anlimiteddev-mnt
#/etc/gocode/bin/goofys -o allow_other --uid "$(id -u nginx)" --gid "$(id -g nginx)" anlimiteddev-mnt /s3mnt

# fluentdのインストール
if [ ! -e /opt/td-agent/embedded/bin/ ]; then
  curl -L http://toolbelt.treasuredata.com/sh/install-redhat-td-agent2.sh | sudo sh
  /opt/td-agent/embedded/bin/fluent-gem install fluent-plugin-ec2-metadata
  /opt/td-agent/embedded/bin/fluent-gem install fluent-plugin-s3
  /opt/td-agent/embedded/bin/fluent-gem install fluent-plugin-forest
  /opt/td-agent/embedded/bin/fluent-gem install fluent-plugin-parser
  /opt/td-agent/embedded/bin/fluent-gem install fluent-plugin-config-expander
  chkconfig --add td-agent
  service td-agent start
fi

# fluentdの設定変更
sed -i -e "s/TD_AGENT_USER=td-agent/TD_AGENT_USER=root/" /etc/init.d/td-agent
sed -i -e "s/TD_AGENT_GROUP=td-agent/TD_AGENT_GROUP=root/" /etc/init.d/td-agent

# UNICRONのプロジェクトのログ用設定
projectpath='';
if [ 0 -lt ${#projectname} ]; then
  # UNICORNのログパス用にprojectpathを設定
  projectpath="/${projectname}"
fi

# fluentdのbufferはどっちのサーバーからも見えるように共通ディレクトリを使う
#if [ ! -e /var/log/td-agent/buffer/ ]; then
#  mkdir -p ${mountpath}/logworkspace/buffer
#  rm -rf /var/log/td-agent/buffer
#  ln -s ${mountpath}/logworkspace/buffer /var/log/td-agent/buffer
#fi

# Nginxのログフォーマット設定
httpdaccesslogformat='apache'
httpdaccesstimeformat=''
httpderrorlogformat='apache_error'
httpderrortimeformat=''
if [ "`echo $restartcmd | grep -e 'nginx'`" ]; then
  httpdaccesslogformat='/^(?<remote>[^ ]*) (?<host>[^ ]*) (?<user>[^ ]*) \[(?<time>[^\]]*)\] "(?<method>\S+)(?: +(?<path>[^ ]*) +\S*)?" (?<code>[^ ]*) (?<size>[^ ]*)(?: "(?<referer>[^\"]*)" "(?<agent>[^\"]*)" "(?<forwarder>[^\"]*)")?/'
  httpdaccesstimeformat='time_format %d/%b/%Y:%H:%M:%S %z'
  httpderrorlogformat='/^(?<time>[^\]]*) \[(?<level>[^\]]*)\] (?<message>.*)$/'
  httpderrortimeformat='time_format %Y/%m/%d %H:%M:%S'
fi

# オリジナルの設定を取っておく
if [ ! -e /etc/td-agent/td-agent.conf.org ]; then
  cp /etc/td-agent/td-agent.conf /etc/td-agent/td-agent.conf.org
fi
# ログ設定
# XXX UNICORN用になってるのでFuel用は誰か追加して！ format作成は http://fluentular.herokuapp.com/ この辺使うと作りやすいよ！
cat << _EOT_ > /etc/td-agent/td-agent.conf.tmp
<source>
  type config_expander
  <config>
    tag log.httpdaccess.${NAME}
    type tail
    format ${httpdaccesslogformat}
    ${httpdaccesstimeformat}
    path ${logpath}/*access.log
    pos_file /var/log/td-agent/httpdaccess.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.httpderror.${NAME}
    type tail
    format ${httpderrorlogformat}
    ${httpderrortimeformat}
    path ${logpath}/*error.log
    pos_file /var/log/td-agent/httpderror.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.backtrace.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/backtrace.log
    pos_file /var/log/td-agent/backtrace.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.cookie.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/cookie.log
    pos_file /var/log/td-agent/cookie.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.exception.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/exception.log
    pos_file /var/log/td-agent/exception.log.pos
  </config>
</source>

#<source>
#  type config_expander
#  <config>
#    tag log.memcache.${NAME}
#    type tail
#    format none
#    path ${logpath}${projectpath}/memcache.log
#    pos_file /var/log/td-agent/memcache.log.pos
#  </config>
#</source>

<source>
  type config_expander
  <config>
    tag log.dynamodb.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/dynamodb.log
    pos_file /var/log/td-agent/dynamodb.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.post.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/post.log
    pos_file /var/log/td-agent/post.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.process.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/process.log
    pos_file /var/log/td-agent/process.log.pos
  </config>
</source>

#<source>
#  type config_expander
#  <config>
#    tag log.push.${NAME}
#    type tail
#    format none
#    path ${logpath}${projectpath}/push.log
#    pos_file /var/log/td-agent/push.log.pos
#  </config>
#</source>

<source>
  type config_expander
  <config>
    tag log.query.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/query.log
    pos_file /var/log/td-agent/query.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.responce.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/responce.log
    pos_file /var/log/td-agent/responce.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.server.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/server.log
    pos_file /var/log/td-agent/server.log.pos
  </config>
</source>

<source>
  type config_expander
  <config>
    tag log.session.${NAME}
    type tail
    format none
    path ${logpath}${projectpath}/session.log
    pos_file /var/log/td-agent/session.log.pos
  </config>
</source>

#<source>
#  type config_expander
#  <config>
#    tag log.other.${NAME}
#    type tail
#    format none
#    path ${logpath}${projectpath}/*.log
#    pos_file /var/log/td-agent/other.log.pos
#  </config>
#</source>

<match log.**>
  type forest
  subtype s3
   <template>
    aws_key_id ${AWS_ACCESS_KEY_ID}
    aws_sec_key ${AWS_SECRET_ACCESS_KEY}
    s3_bucket ${backetname}-mnt
    s3_region ${AWS_DEFAULT_REGION}
    path logworkspace/\${tag}/
    store_as text
    buffer_path /var/log/td-agent/s3\${tag}
    time_slice_format %Y-%m-%d/%H
    time_slice_wait 10m
    retry_wait 30s
    retry_limit 5
    flush_at_shutdown true
   </template>
</match>

_EOT_

# 設定が変わっていたら置き換え
diffcnt=$(diff /etc/td-agent/td-agent.conf.tmp /etc/td-agent/td-agent.conf | grep -c -e "^<")
if [ 0 -lt ${diffcnt} ]; then
  rm -rf /etc/td-agent/td-agent.conf
  mv /etc/td-agent/td-agent.conf.tmp /etc/td-agent/td-agent.conf
  chkconfig --add td-agent
  service td-agent restart
  # 2度再起動するのが肝！
  service td-agent restart
fi

# ローテート処理実行(日時バッチ用)
members=''
# XXX fluentdされてるのでログファイルはとっとく必要がない
executed_title="${NAME}ログローテート処理"
rm -rf ${logpath}/*
executed_body=$(df)
${restartcmd}

# チャットワークに結果送信
if [ 0 -lt ${#apitoken} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/logrotate-chatwork.txt
[info]
[title]
${executed_title}
[/title]
${members}
詳しい実行結果を確認して下さい。
${executed_body}
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
cat << _EOT_ > /tmp/logrotate-slack.txt
${executed_title}
詳しい実行結果を確認して下さい。
${executed_body}

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

# /etc/cron.daily/に自動登録
# このシェルファイルが更新されてる場合もあるので、毎回書き換える
\cp -rf ${mepath} /etc/cron.daily/
chmod -R 0755 /etc/cron.daily/
# 行置換
sed -i -e "15 s/.*/10 5 * * * root run-parts \/etc\/cron.daily/" /etc/crontab

exit 0
