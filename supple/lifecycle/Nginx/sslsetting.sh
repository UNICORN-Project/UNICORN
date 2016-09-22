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

# Let's Encryptのインストール(ACME対応SSL設定用)
chmod 0777 ${mepath}
if [ ! -e /etc/certbot/certbotinstalled ]; then
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
  # Let's Encryptのインストール
  mkdir -p /etc/certbot
  git clone https://github.com/certbot/certbot /etc/certbot
  # pythonのcryptographyのインストールがメモリーオーバーになる場合があるのでスワップファイルを作って対応する
  dd if=/dev/zero of=/swapfile bs=1024 count=524288
  chmod 600 /swapfile
  mkswap /swapfile
  swapon /swapfile
  # 念のためもう一回！
  pip install virtualenv --upgrade
  yum -y install augeas-libs dialog python27-tools system-rpm-config
  unset PYTHON_INSTALL_LAYOUT
  /etc/certbot/certbot-auto --debug > /tmp/certbotinstall.log
  #expect -c "
  #  spawn /etc/certbot/certbot-auto -y --debug > /tmp/certbotinstall.log
  #  expect \"s Is this ok \[y/d/N\]:\"
  #  send \"\n\"
  #"
  # スワップ外す
  swapoff /swapfile
  touch /etc/certbot/certbotinstalled
fi

# goofysでS3マウントしてSSLの設定を各Webサーバで共有する
if [ ! -e ${mountpath}/ ]; then
  mkdir -p ${mountpath}
  aws s3 mb s3://${backetname}-mnt
  sleep 1
fi
# S3をマウント
/etc/gocode/bin/goofys -o allow_other --uid "$(id -u nginx)" --gid "$(id -g nginx)" ${backetname}-mnt ${mountpath}
# 3bkの場合
#aws s3 mb s3://anlimiteddev-mnt
#/etc/gocode/bin/goofys -o allow_other --uid "$(id -u nginx)" --gid "$(id -g nginx)" anlimiteddev-mnt /s3mnt

sleep 1

# 証明書作業領域の初期化
if [ ! -e ${mountpath}/sslworkspace/ ]; then
  # 完全なる初期化
  mkdir -p ${mountpath}/sslworkspace/archive
  mkdir -p ${mountpath}/sslworkspace/accounts
  mkdir -p ${mountpath}/sslworkspace/renewal
  rm -rf /etc/letsencrypt/archive
  rm -rf /etc/letsencrypt/accounts
  rm -rf /etc/letsencrypt/renewal
  mkdir -p /etc/letsencrypt
  ln -s ${mountpath}/sslworkspace/archive /etc/letsencrypt/archive
  ln -s ${mountpath}/sslworkspace/accounts /etc/letsencrypt/accounts
  ln -s ${mountpath}/sslworkspace/renewal /etc/letsencrypt/renewal
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
  rm -rf /etc/letsencrypt/archive
  rm -rf /etc/letsencrypt/accounts
  rm -rf /etc/letsencrypt/renewal
  mkdir -p /etc/letsencrypt
  ln -s ${mountpath}/sslworkspace/archive /etc/letsencrypt/archive
  ln -s ${mountpath}/sslworkspace/accounts /etc/letsencrypt/accounts
  ln -s ${mountpath}/sslworkspace/renewal /etc/letsencrypt/renewal
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

# 取得コマンド生成と、rootドメインの特定
rootdomain=''
certcmd="/etc/certbot/certbot-auto certonly -t --webroot --agree-tos"
if [ 0 -lt ${#topdomain} ]; then
  certcmd="${certcmd} -w ${topdomaindocumentrootpath} -d ${topdomain}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${topdomain}
  fi
fi
if [ 0 -lt ${#webdomain} ]; then
  certcmd="${certcmd} -w ${webdomaindocumentrootpath} -d ${webdomain}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${webdomain}
  fi
fi
if [ 0 -lt ${#apidomain} ]; then
  certcmd="${certcmd} -w ${apidomaindocumentrootpath} -d ${apidomain}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${apidomain}
  fi
fi
if [ 0 -lt ${#mgrdomain} ]; then
  certcmd="${certcmd} -w ${mgrdomaindocumentrootpath} -d ${mgrdomain}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${mgrdomain}
  fi
fi
if [ 0 -lt ${#staticdomain} ]; then
  certcmd="${certcmd} -w ${staticdomaindocumentrootpath} -d ${staticdomain}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${staticdomain}
  fi
fi
if [ 0 -lt ${#appdomain} ]; then
  certcmd="${certcmd} -w ${appdomaindocumentrootpath} -d ${appdomain}"
  if [ 0 -ge ${#rootdomain} ]; then
    rootdomain=${appdomain}
  fi
fi
certcmd="${certcmd} -m ${notifymail} --debug"
# 3bkの例
# /etc/certbot/certbot-auto certonly -t --webroot --agree-tos -w /var/www/release/webdocs -d dev.3bk.jp -w /var/www/release/managedocs -d devfwm.3bk.jp -w /var/www/release/webdocs -d devstatic.3bk.jp -w /var/www/release/webdocs -d devapp.3bk.jp -m develop+devssl@digiq.co.jp --debug

# 証明書取得の復帰処理
if [ -e ${mountpath}/sslworkspace/live/${rootdomain}/latest ]; then
  # マウント直後に既に前回取得結果がある場合
  latest=`cat ${mountpath}/sslworkspace/live/${rootdomain}/latest`
  echo "latest=${latest}"
  if [ -e /etc/letsencrypt/archive/${rootdomain}/fullchain${latest}.pem ]; then
    # /etc/letsencrypt/liveをS3のファイルから復帰する
    mkdir -p /etc/letsencrypt/live/${rootdomain}
    ln -s /etc/letsencrypt/archive/${rootdomain}/cert${latest}.pem /etc/letsencrypt/live/${rootdomain}/cert.pem
    ln -s /etc/letsencrypt/archive/${rootdomain}/chain${latest}.pem /etc/letsencrypt/live/${rootdomain}/chain.pem
    ln -s /etc/letsencrypt/archive/${rootdomain}/fullchain${latest}.pem /etc/letsencrypt/live/${rootdomain}/fullchain.pem
    ln -s /etc/letsencrypt/archive/${rootdomain}/privkey${latest}.pem /etc/letsencrypt/live/${rootdomain}/privkey.pem
  fi
fi

# 証明書取得処理
executed_title=''
executed_body=''
if [ ! -e /etc/letsencrypt/live/${rootdomain}/fullchain.pem ]; then
  executed_title="${rootdomain}-${NAME}SSL証明書取得処理"
  # 証明書取得実行(初実行)
  echo "/etc/certbot/certbot-auto --debug > /tmp/certbotinstall.log"
  echo "${certcmd}"
  executed_body=$($certcmd)
else
  executed_title="${rootdomain}-${NAME}SSL証明書更新処理"
  # 証明書更新
  echo "/etc/certbot/certbot-auto --debug > /tmp/certbotinstall.log"
  echo "/etc/certbot/certbot-auto renew"
  # XXX 強制更新コマンドは以下 ただし、デイリーバッチではやらない事！バッチ登録処理部分も変更する事！
  #echo "/etc/certbot/certbot-auto renew --force-renew"
  executed_body=$(/etc/certbot/certbot-auto renew)
fi

# 結果をチャットワークに通知
if [ "$?" -eq 0 ]; then
  # 更新成功 or 更新不要
  executed_title="${executed_title} 成功"
  # 成功時通知不要
  members=''
  # 最後の実行結果ファイルを特定し、履歴を残す
  STR=$(realpath /etc/letsencrypt/live/${rootdomain}/fullchain.pem)
  STR=${STR##*/fullchain}
  latestnum=${STR%.*}
  mkdir -p ${mountpath}/sslworkspace/live/${rootdomain}
  if [ 0 -lt ${#projectname} ]; then
    echo ${latestnum} > ${mountpath}/sslworkspace/live/${rootdomain}/latest
  fi
  # 証明書を各ドメインのsslディレクトリにコピー
  #if [ ! "`echo $executed_body | grep -e 'No renewals'`" ]; then
  # XXX一旦毎回
    # 証明書を差し替える
    if [ 0 -lt ${#topdomain} ]; then
      rm -rf ${topdomainsslpath}/
      mkdir -p ${topdomainsslpath}/
      # 実態をちゃんとコピー
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/fullchain.pem ${topdomainsslpath}/private.pem
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/privkey.pem ${topdomainsslpath}/private.key
      chown -R $httpduser:$httpduser ${topdomainsslpath}/
      chmod -R 0600 ${topdomainsslpath}/
    fi
    if [ 0 -lt ${#webdomain} ]; then
      rm -rf ${webdomainsslpath}/
      mkdir -p ${webdomainsslpath}/
      # 実態をちゃんとコピー
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/fullchain.pem ${webdomainsslpath}/private.pem
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/privkey.pem ${webdomainsslpath}/private.key
      chown -R $httpduser:$httpduser ${webdomainsslpath}/
      chmod -R 0600 ${webdomainsslpath}/
    fi
    if [ 0 -lt ${#apidomain} ]; then
      rm -rf ${apidomainsslpath}/
      mkdir -p ${apidomainsslpath}/
      # 実態をちゃんとコピー
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/fullchain.pem ${apidomainsslpath}/private.pem
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/privkey.pem ${apidomainsslpath}/private.key
      chown -R $httpduser:$httpduser ${apidomainsslpath}/
      chmod -R 0600 ${apidomainsslpath}/
    fi
    if [ 0 -lt ${#mgrdomain} ]; then
      rm -rf ${mgrdomainsslpath}/
      mkdir -p ${mgrdomainsslpath}/
      # 実態をちゃんとコピー
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/fullchain.pem ${mgrdomainsslpath}/private.pem
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/privkey.pem ${mgrdomainsslpath}/private.key
      chown -R $httpduser:$httpduser ${mgrdomainsslpath}/
      chmod -R 0600 ${mgrdomainsslpath}/
    fi
    if [ 0 -lt ${#staticdomain} ]; then
      rm -rf ${staticdomainsslpath}/
      mkdir -p ${staticdomainsslpath}/
      # 実態をちゃんとコピー
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/fullchain.pem ${staticdomainsslpath}/private.pem
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/privkey.pem ${staticdomainsslpath}/private.key
      chown -R $httpduser:$httpduser ${staticdomainsslpath}/
      chmod -R 0600 ${staticdomainsslpath}/
    fi
    if [ 0 -lt ${#appdomain} ]; then
      rm -rf ${appdomainsslpath}/
      mkdir -p ${appdomainsslpath}/
      # 実態をちゃんとコピー
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/fullchain.pem ${appdomainsslpath}/private.pem
      \cp -rfH /etc/letsencrypt/live/${rootdomain}/privkey.pem ${appdomainsslpath}/private.key
      chown -R $httpduser:$httpduser ${appdomainsslpath}/
      chmod -R 0600 ${appdomainsslpath}/
    fi
    # XXX 勝手にhttpdをリスタートしたりはしない！
  #fi
else
  # 更新失敗
  executed_title="${executed_title} 失敗"
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

# /etc/cron.daily/に自動登録
# このシェルファイルが更新されてる場合もあるので、毎回書き換える
\cp -rf ${mepath} /etc/cron.daily/
chmod -R 0755 /etc/cron.daily/
# 行置換
sed -i -e "15 s/.*/10 5 * * * root run-parts \/etc\/cron.daily/" /etc/crontab

# 証明書を読み込み直す
echo "${restartcmd}"

exit 0
