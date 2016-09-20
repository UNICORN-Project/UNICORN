#!/bin/bash

# 自分自身のファイルパス
mepath=${0}

# ClamAVによる自動ウィルスチェック設定
# (!!!) EC2専用です！
# ※ デフォルトは毎日朝5時10分に自動ウィルスチェックが走るように設定されます。
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

# ClamAV
if [ ! -e /opt/scripts/clamav/ ]; then
  # ClamAVのインストール
  yum install clamav clamav-scanner-sysvinit clamav-update -y
  # ClamAVの設定ファイルを修正
  sed -i -e "s/Example/#Example/" /etc/freshclam.conf
  sed -i -e "s:#DatabaseDirectory /var/lib/clamav:DatabaseDirectory /var/lib/clamav:" /etc/freshclam.conf
  sed -i -e "s:#UpdateLogFile /var/log/freshclam.log:UpdateLogFile /var/log/freshclam.log:" /etc/freshclam.conf
  sed -i -e "s/#DatabaseOwner clamupdate/DatabaseOwner clamupdate/" /etc/freshclam.conf
  sed -i -e "s/FRESHCLAM_DELAY=disabled-warn/#FRESHCLAM_DELAY=disabled-warn/" /etc/sysconfig/freshclam
  # ウィルス定義の更新
  freshclam
  # クローンスキャン定義の更新
  sed -i -e "s/Example/#Example/" /etc/clamd.d/scan.conf
  sed -i -e "s:#LocalSocket /var/run/clamd.scan/clamd.sock:LocalSocket /var/run/clamd.scan/clamd.sock:" /etc/clamd.d/scan.conf
  sed -i -e "s/#FixStaleSocket yes/FixStaleSocket yes/" /etc/clamd.d/scan.conf
  sed -i -e "s/#TCPSocket 3310/TCPSocket 3310/" /etc/clamd.d/scan.conf
  sed -i -e "s/#TCPAddr 127.0.0.1/TCPAddr 127.0.0.1/" /etc/clamd.d/scan.conf  freshclam
  ln -s /etc/clamd.d/scan.conf /etc/clamd.conf 
  # ウィルス検知サービスを起動(サービス起動しただけではスキャンはされない！)
  service clamd.scan start
  # サービスの自動起動を設定
  chkconfig clamd.scan on
  chkconfig
  # バッチで使う定義の更新
  /usr/bin/freshclam
  # 自動スキャン設定
  mkdir -p /opt/scripts/clamav
  #cp -rf $currentdir/bin/job/virusscan.sh /opt/scripts/clamav/
  #chmod -R 0755 /opt/scripts/clamav/virusscan.sh
  # スキャン除外場所の追加
  echo "/proc/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/sys/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/boot/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/home/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/selinux/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/usr/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/dev/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/srv/" >> /opt/scripts/clamav/clamscan.exclude
fi

# /etc/cron.daily/に自動登録
# このシェルファイルが更新されてる場合もあるので、毎回書き換える
\cp -rf ${mepath} /etc/cron.daily/
chmod -R 0755 /etc/cron.daily/
# 行置換
sed -i -e "15 s/.*/10 5 * * * root run-parts \/etc\/cron.daily/" /etc/crontab

# 初回はインストールだけ！
if [ ! -e /tmp/virusscaned ]; then
  touch /tmp/virusscaned
  exit 0
fi

# ウィルススキャン開始
# XXX インストールが完了している2度目以降から実行
executed_title="${NAME}ウィルススキャン処理"
executed_body=''

logger "[Info] ClamAV Scan Start" 

fromAddr="alert@${NAME}.${topdomain}"
toAddr="${notifymail}"
subjString="[ALERT] Virus Found in ${topdomain}"
bodyString=""

logger "[Info] ${fromAddr}"
logger "[Info] ${toAddr}"
logger "[Info] ${subjString}"

# clamd update
yum update clamav clamav-scanner-sysvinit clamav-update -y > /dev/null 2>&1
freshclam > /dev/null 2>&1

# excludeopt setup
excludelist=/opt/scripts/clamav/clamscan.exclude
if [ -s $excludelist ]; then
    for i in `cat $excludelist`
    do
        if [ $(echo "$i"|grep \/$) ]; then
            i=`echo $i|sed -e 's/^\([^ ]*\)\/$/\1/p' -e d`
            excludeopt="${excludeopt} --exclude-dir=^$i"
        else
            excludeopt="${excludeopt} --exclude=^$i"
        fi
    done
fi 

# virus scan
logger "[Info] scan now"
CLAMSCANTMP=`mktemp`
clamscan --recursive --remove ${excludeopt} / > $CLAMSCANTMP 2>&1
# report mail send
[ ! -z "$(grep FOUND$ $CLAMSCANTMP)" ] && \
bodyString="`grep FOUND$ $CLAMSCANTMP`"
[ ! -z "$(grep FOUND$ $CLAMSCANTMP)" ] && \
echo -e "From: ${fromAddr}\nTo: ${toAddr}\nSubject:${subjString}\n\n${bodyString}" | /usr/sbin/sendmail -f ${fromAddr} -t ${toAddr}
[ ! -z "$(grep FOUND$ $CLAMSCANTMP)" ] && \
executed_body="ウィルスを検知しました！\n${bodyString}"

logger "[Info] scan end"
logger "[Info] ${bodyString}"
logger "[Info] From: ${fromAddr}\nTo: ${toAddr}\nSubject:${subjString}\n\n${bodyString}"

#ログ出力 ウィルス検知
grep FOUND$ $CLAMSCANTMP | logger 

rm -f $CLAMSCANTMP

# ログ出力 終了
logger "[Info] ClamAV Scan Finish"

# ウィルス未検知判定
if [ 0 -ge ${#executed_body} ]; then
  members=''
  executed_body="ウィルスは検知されませんでした。\n"
fi

# チャットワークに結果送信
if [ 0 -lt ${#apitoken} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/virusscan-chatwork.txt
[info]
[title]
${executed_title}
[/title]
${members}
詳しい実行結果を確認して下さい。
${executed_body}
[/info]

_EOT_
  _body=`cat /tmp/virusscan-chatwork.txt`
  _body=${_body//\\/\\\\}
  _body=${_body//\"/\\\"}
  echo ${_body}
  curl -X POST -H "X-ChatWorkToken: ${apitoken}" -d "body=${_body}" "https://api.chatwork.com/v1/rooms/${roomid}/messages" > /dev/null
fi

# Slackに結果送信
if [ 0 -lt ${#webhookurl} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/virusscan-slack.txt
${executed_title}
詳しい実行結果を確認して下さい。
${executed_body}

_EOT_
  _body=`cat /tmp/virusscan-slack.txt`
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
