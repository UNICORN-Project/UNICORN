#!/bin/bash

# 自分自身のファイルパス
mepath=${0}

# Letsencrypt(certbot)によるSSL証明書自動設定・自動更新登録
# (!!!) EC2専用です！
# ※ デフォルトは毎日朝5時10分に自動更新が走るように設定されます。
# ※ もしかしたら https://github.com/Neilpang/acme.sh の方が簡単で確実かも知れません！
# ※ このShellはオートスケール・複数Frontサーバーでの動作が可能です。
# 
# Create by S.Ohno
# Create at 2016-09-15

# ------------------------------- 取扱説明書ココから (必読) ------------------------------- 
# やってること(ざっくり)
#   サーバにLetEncryt(Python2.7)環境を自動インストールします。
#   サーバにGoofys(golang)環境を自動インストールします。
#   s3にマウント用のバケットを自動生成します。
#   s3を自動マウントします。
#   証明書の自動取得・更新を行います。
#   s3に履歴を自動保存します。
#   証明書の自動取得・更新完了後の結果をチャットワークに通知します。
#   ついでにPFS(Perfect Forward Security)対応用のファイルを/etc/ssl/dhparam.pemに吐き出します。
#   /etc/cron.daily/にcronを自動登録します。(なので/etc/cron.daily/配下にファイルが自動生成される事に注意して下さい)
#
# 注意)
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
projectname='SPAJAMProject'
# httpd実行時の権限ユーザー名
httpduser=nginx
# Nginxの設定再読み込みコマンドの定義(最後に自動実行してくれる)
restartcmd="service nginx restart && service php-fpm restart"
# Apacheの場合は↓を使って下さい
#restartcmd="service httpd restart"
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
topdomaindocumentrootpath=/var/www/release/lib/SPAJAMProjectPackage/webdocs
webdomaindocumentrootpath=/var/www/release/lib/SPAJAMProjectPackage/webdocs
apidomaindocumentrootpath=/var/www/release/lib/SPAJAMProjectPackage/apidocs
mgrdomaindocumentrootpath=/var/www/release/lib/FrameworkManager/template/managedocs
staticdomaindocumentrootpath=/var/www/release/lib/SPAJAMProjectPackage/webdocs
appdomaindocumentrootpath=/var/www/release/lib/SPAJAMProjectPackage/webdocs
# ログパス
logpath=/var/www/logs
isdev=/var/www/.dev
isProd=/var/www/.production

# 環境によって設定が別れる変数の定義
# 開発環境用
devtopdomain=devspajam2016.otkr.net
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
devwebdomain=''
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
devapidomain=devapispajam2016.otkr.net
devmgrdomain=devfwmspajam2016.otkr.net
devstaticdomain=''
devappdomain=''
devbacketname=spajam2016dev
devregion=ap-northeast-1
# リリース環境用
prodtopdomain=spajam2016.otkr.net
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
prodwebdomain=''
# SSLが不要なドメインの場合は、このようにドメイン設定を空文字にして下さい。documentrootpathは仮想で構わないので設定しておいて下さい
prodapidomain=apispajam2016.otkr.net
prodmgrdomain=fwmspajam2016.otkr.net
prodstaticdomain=''
prodappdomain=''
prodbacketname=spajam2016
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

# Let's Encrypt(sh版のacme.sh)のインストール(ACME対応SSL設定用)
chmod 0777 ${mepath}
if [ ! -e /root/.acme.sh ]; then
  # Pyhonのアップデートから
  yum -y install gcc libffi-devel openssl-devel expect
  yum -y install python27 python27-devel
  rm -rf /usr/bin/python
  ln -s /usr/bin/python2.7 /usr/bin/python
  cp /usr/bin/yum /usr/bin/_yum_before_27
  sed -i s/python/python2.6/g /usr/bin/yum
  sed -i s/python2.6/python2.6/g /usr/bin/yum
  sed -i s/python2.62.7/python2.7/g /usr/bin/yum
  #curl -o /tmp/ez_setup.py https://bootstrap.pypa.io/ez_setup.py
  #/usr/bin/python27 /tmp/ez_setup.py
  /usr/bin/python27 -m easy_install setuptools==26.0.0
  rm -rf /usr/sbin/pip
  rm -rf /usr/local/bin/pip
  ln -s /usr/bin/pip /usr/sbin/pip
  ln -s /usr/bin/pip /usr/local/bin/pip
  /usr/bin/easy_install-2.7 pip
  pip install --upgrade pip
  pip install virtualenv --upgrade
  # Pythonが27系になるのでawscliも27系用に更新しておく
  pip install --upgrade awscli
  yum -y install augeas-libs dialog python27-tools system-rpm-config
  # Let's Encrypt sh版のacme.shのインストール
  curl https://get.acme.sh | sh
fi

# acmeのアップデート
/root/.acme.sh/acme.sh --upgrade

# goofysでS3マウントしてSSLの設定を各Webサーバで共有する
if [ ! -e ${mountpath}/ ]; then
  mkdir -p ${mountpath}
  aws s3 mb s3://${backetname}-mnt
  sleep 1
fi
# S3をマウント
/etc/gocode/bin/goofys -o allow_other --uid "$(id -u nginx)" --gid "$(id -g nginx)" ${backetname}-mnt ${mountpath}

# s3マウントを待つ
sleep 1

# 取得コマンド生成と、rootドメインの特定
rootdomain=''
certcmd="/root/.acme.sh/acme.sh --issue"
if [ 0 -lt ${#topdomain} ]; then
  certcmd="${certcmd} -d ${topdomain} -w ${topdomaindocumentrootpath}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${topdomain}
  fi
fi
if [ 0 -lt ${#webdomain} ]; then
  certcmd="${certcmd} -d ${webdomain} -w ${webdomaindocumentrootpath}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${webdomain}
  fi
fi
if [ 0 -lt ${#apidomain} ]; then
  certcmd="${certcmd} -d ${apidomain} -w ${apidomaindocumentrootpath}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${apidomain}
  fi
fi
if [ 0 -lt ${#mgrdomain} ]; then
  certcmd="${certcmd} -d ${mgrdomain} -w ${mgrdomaindocumentrootpath}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${mgrdomain}
  fi
fi
if [ 0 -lt ${#staticdomain} ]; then
  certcmd="${certcmd} -d ${staticdomain} -w ${staticdomaindocumentrootpath}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${staticdomain}
  fi
fi
if [ 0 -lt ${#appdomain} ]; then
  certcmd="${certcmd} -d ${appdomain} -w ${appdomaindocumentrootpath}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${appdomain}
  fi
fi
# spajam2016の例
# /root/.acme.sh/acme.sh --issue -d devapispajam2016.otkr.net -w /var/www/release/lib/SPAJAMProjectPackage/apidocs -d devfwmspajam2016.otkr.net -w /var/www/release/lib/FrameworkManager/template/managedocs

# 証明書作業領域の初期化
if [ ! -e ${mountpath}/sslworkspace/${rootdomain} ]; then
  mkdir -p ${mountpath}/sslworkspace
  # 各種SSL取得用ドキュメントルートの設定
  mkdir -p ${mountpath}/sslworkspace/topdomaindocumentroot/.well-known
  mkdir -p ${mountpath}/sslworkspace/webdomaindocumentroot/.well-known
  mkdir -p ${mountpath}/sslworkspace/apidomaindocumentroot/.well-known
  mkdir -p ${mountpath}/sslworkspace/mgrdomaindocumentroot/.well-known
  mkdir -p ${mountpath}/sslworkspace/staticdomaindocumentroot/.well-known
  mkdir -p ${mountpath}/sslworkspace/appdomaindocumentroot/.well-known
  rm -rf ${topdomaindocumentrootpath}/.well-known
  rm -rf ${webdomaindocumentrootpath}/.well-known
  rm -rf ${apidomaindocumentrootpath}/.well-known
  rm -rf ${mgrdomaindocumentrootpath}/.well-known
  rm -rf ${staticdomaindocumentrootpath}/.well-known
  rm -rf ${appdomaindocumentrootpath}/.well-known
  if [ ! -e ${topdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/topdomaindocumentroot/.well-known ${topdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${webdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/webdomaindocumentroot/.well-known ${webdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${apidomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/apidomaindocumentroot/.well-known ${apidomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${mgrdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/mgrdomaindocumentroot/.well-known ${mgrdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${staticdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/staticdomaindocumentroot/.well-known ${staticdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${appdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/appdomaindocumentroot/.well-known ${appdomaindocumentrootpath}/.well-known
  fi
else
  # 既存の作業領域で初期化
  rm -rf /root/.acme.sh/${rootdomain}
  cp -Rf ${mountpath}/sslworkspace/${rootdomain} /root/.acme.sh/
  # 各種SSL取得用ドキュメントルートの設定
  rm -rf ${topdomaindocumentrootpath}/.well-known
  rm -rf ${webdomaindocumentrootpath}/.well-known
  rm -rf ${apidomaindocumentrootpath}/.well-known
  rm -rf ${mgrdomaindocumentrootpath}/.well-known
  rm -rf ${staticdomaindocumentrootpath}/.well-known
  rm -rf ${appdomaindocumentrootpath}/.well-known
  if [ ! -e ${topdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/topdomaindocumentroot/.well-known ${topdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${webdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/webdomaindocumentroot/.well-known ${webdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${apidomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/apidomaindocumentroot/.well-known ${apidomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${mgrdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/mgrdomaindocumentroot/.well-known ${mgrdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${staticdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/staticdomaindocumentroot/.well-known ${staticdomaindocumentrootpath}/.well-known
  fi
  if [ ! -e ${appdomaindocumentrootpath}/.well-known/ ]; then
    ln -s ${mountpath}/sslworkspace/appdomaindocumentroot/.well-known ${appdomaindocumentrootpath}/.well-known
  fi
fi

# 証明書取得・更新の実行
executed_body=''
executed_title="${rootdomain}-${NAME}SSL証明書取得・更新処理"
# 実行
echo "${certcmd}"
# XXX 強制更新コマンドは以下 ただし、デイリーバッチではやらない事！バッチ登録処理部分も変更する事！
#echo "${certcmd} --force"
executed_body=$($certcmd)

# 成功時の書換処理諸々
if [ "$?" -eq 0 ]; then
  # 取得成功 or 更新成功
  executed_title="${executed_title} 成功"
  # 成功時メンション通知は不要
  members=''
  # 一旦削除
  rm -rf /s3mnt/sslworkspace/${rootdomain}
  # まるっとコピーし直し
  cp -Rf /root/.acme.sh/${rootdomain} /s3mnt/sslworkspace/
else
  if [ ! "`echo $executed_body | grep -e 'Skip, Next renewal'`" ]; then
    # 更新失敗
    executed_title="${executed_title} 失敗"
  else
    # 更新不要
    executed_title="${executed_title} スキップ"
    members=''
  fi
fi

# 証明書を差し替える
if [ 0 -lt ${#topdomain} -a -e /root/.acme.sh/${rootdomain}/fullchain.cer ]; then
  rm -rf ${topdomainsslpath}/
  mkdir -p ${topdomainsslpath}/
  # 実態をちゃんとコピー
  \cp -rfH /root/.acme.sh/${rootdomain}/fullchain.cer ${topdomainsslpath}/private.pem
  \cp -rfH /root/.acme.sh/${rootdomain}/${rootdomain}.key ${topdomainsslpath}/private.key
  chown -R $httpduser:$httpduser ${topdomainsslpath}/
  chmod -R 0600 ${topdomainsslpath}/
fi
if [ 0 -lt ${#webdomain} -a -e /root/.acme.sh/${rootdomain}/fullchain.cer ]; then
  rm -rf ${webdomainsslpath}/
  mkdir -p ${webdomainsslpath}/
  # 実態をちゃんとコピー
  \cp -rfH /root/.acme.sh/${rootdomain}/fullchain.cer ${webdomainsslpath}/private.pem
  \cp -rfH /root/.acme.sh/${rootdomain}/${rootdomain}.key ${webdomainsslpath}/private.key
  chown -R $httpduser:$httpduser ${webdomainsslpath}/
  chmod -R 0600 ${webdomainsslpath}/
fi
if [ 0 -lt ${#apidomain} -a -e /root/.acme.sh/${rootdomain}/fullchain.cer ]; then
  rm -rf ${apidomainsslpath}/
  mkdir -p ${apidomainsslpath}/
  # 実態をちゃんとコピー
  \cp -rfH /root/.acme.sh/${rootdomain}/fullchain.cer ${apidomainsslpath}/private.pem
  \cp -rfH /root/.acme.sh/${rootdomain}/${rootdomain}.key ${apidomainsslpath}/private.key
  chown -R $httpduser:$httpduser ${apidomainsslpath}/
  chmod -R 0600 ${apidomainsslpath}/
fi
if [ 0 -lt ${#mgrdomain} -a -e /root/.acme.sh/${rootdomain}/fullchain.cer ]; then
  rm -rf ${mgrdomainsslpath}/
  mkdir -p ${mgrdomainsslpath}/
  # 実態をちゃんとコピー
  \cp -rfH /root/.acme.sh/${rootdomain}/fullchain.cer ${mgrdomainsslpath}/private.pem
  \cp -rfH /root/.acme.sh/${rootdomain}/${rootdomain}.key ${mgrdomainsslpath}/private.key
  chown -R $httpduser:$httpduser ${mgrdomainsslpath}/
  chmod -R 0600 ${mgrdomainsslpath}/
fi
if [ 0 -lt ${#staticdomain} -a -e /root/.acme.sh/${rootdomain}/fullchain.cer ]; then
  rm -rf ${staticdomainsslpath}/
  mkdir -p ${staticdomainsslpath}/
  # 実態をちゃんとコピー
  \cp -rfH /root/.acme.sh/${rootdomain}/fullchain.cer ${staticdomainsslpath}/private.pem
  \cp -rfH /root/.acme.sh/${rootdomain}/${rootdomain}.key ${staticdomainsslpath}/private.key
  chown -R $httpduser:$httpduser ${staticdomainsslpath}/
  chmod -R 0600 ${staticdomainsslpath}/
fi
if [ 0 -lt ${#appdomain} -a -e /root/.acme.sh/${rootdomain}/fullchain.cer ]; then
  rm -rf ${appdomainsslpath}/
  mkdir -p ${appdomainsslpath}/
  # 実態をちゃんとコピー
  \cp -rfH /root/.acme.sh/${rootdomain}/fullchain.cer ${appdomainsslpath}/private.pem
  \cp -rfH /root/.acme.sh/${rootdomain}/${rootdomain}.key ${appdomainsslpath}/private.key
  chown -R $httpduser:$httpduser ${appdomainsslpath}/
  chmod -R 0600 ${appdomainsslpath}/
fi

# チャットワークに結果送信
if [ 0 -lt ${#apitoken} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/sslsetting-chatwork.txt
[info]
[title]
${executed_title}
[/title]
${members}
詳しい実行結果を確認して下さい。
${executed_body}
[/info]

_EOT_
  _body=`cat /tmp/sslsetting-chatwork.txt`
  _body=${_body//\\/\\\\}
  _body=${_body//\"/\\\"}
  echo ${_body}
  curl -X POST -H "X-ChatWorkToken: ${apitoken}" -d "body=${_body}" "https://api.chatwork.com/v1/rooms/${roomid}/messages" > /dev/null
fi

# Slackに結果送信
if [ 0 -lt ${#webhookurl} ]; then
  # 送信メッセージの成形
cat << _EOT_ > /tmp/sslsetting-slack.txt
${executed_title}
詳しい実行結果を確認して下さい。
${executed_body}

_EOT_
  _body=`cat /tmp/sslsetting-slack.txt`
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

# もののついででPerfect Forward Securityの初期化
if [ ! -e /etc/ssl/dhparam.pem ]; then
  if [ ! -e ${mountpath}/sslworkspace/dhparam.pem ]; then
    # PFSを生成
    openssl dhparam 2048 -out /etc/ssl/dhparam.pem
    \cp -rf /etc/ssl/dhparam.pem ${mountpath}/sslworkspace/dhparam.pem
  else
    # 既存のPFSをコピー
    \cp -rf ${mountpath}/sslworkspace/dhparam.pem /etc/ssl/dhparam.pem
  fi
fi

# /etc/crondaily/に自動登録
mkdir -p /etc/crondaily
# このシェルファイルが更新されてる場合もあるので、毎回書き換える
\cp -rf ${mepath} /etc/crondaily/
chmod -R 0755 /etc/crondaily/
# 行置換
#sed -i -e "15 s/.*/10 20 * * * root run-parts \/etc\/crondaily/" /etc/crontab
# JST時間でcronが実行される場合は以下の行を使って下さい
sed -i -e "15 s/.*/10 5 * * * root run-parts \/etc\/crondaily/" /etc/crontab
yum install -y cronie-noanacron
yum remove -y cronie-anacron

# 証明書を読み込み直す
echo "${restartcmd}"

exit 0
