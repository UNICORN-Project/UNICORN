#!/bin/bash

projectName=FrameworkManager
isdev=/var/www/.dev
isProd=/var/www/.production
logdir=/var/www/logs
tmpdir=/var/www/tmp
currentdir=/var/www/current
backupdir=/var/www/backup
releasedir=/var/www/release

# 環境によって設定が別れる変数の定義
# 開発環境用
devbacketname=unicorndev
devregion=ap-northeast-1
devloadbalancer=developmen-elbaslb-xxxxxxx
# リリース環境用
prodbacketname=unicornprod
prodregion=ap-northeast-1
prodloadbalancer=production-elbaslb-xxxxxxx

# 環境による設定の振り分け
backetname=$prodbacketname
region=$prodregion
loadbalancer=$prodloadbalancer
if [  -e $isProd ]; then
   rm -rf $isdev
else
  if [ -e $isdev ]; then
    # 開発用のバケットに向ける
    backetname=$devbacketname
    region=$devregion
    loadbalancer=$devloadbalancer
  fi
fi

# ProxyProtocolを有効にする
export AWS_DEFAULT_REGION=$region
aws elb create-load-balancer-policy --load-balancer-name $loadbalancer --policy-name EnableProxyProtocol  --policy-type-name ProxyProtocolPolicyType --policy-attributes AttributeName=ProxyProtocol,AttributeValue=True
aws elb set-load-balancer-policies-for-backend-server --load-balancer-name $loadbalancer --instance-port 443 --policy-names EnableProxyProtocol

# メンテナンス中継続
touch $currentdir/lib/FrameworkPackage/.maintenance
chown nginx:nginx $currentdir/lib/FrameworkPackage/.maintenance
chmod 0666 $currentdir/lib/FrameworkPackage/.maintenance

# logdirが存在するかチェック
if [ ! -e $logdir ]; then
  mkdir $logdir
fi
# 所有権の統一
chown -R nginx:nginx $logdir
# 参照権の統一
chmod -R 0755 $logdir

# tmpdirが存在するかチェック
if [ ! -e $tmpdir ]; then
  mkdir $tmpdir
fi
# 所有権の統一
chown -R nginx:nginx $tmpdir
# 参照権の統一
chmod -R 0755 $tmpdir

# releasedirが存在するかチェック
if [  -e $releasedir ]; then
   rm -rf $releasedir
fi
mkdir $releasedir

# currentdirからreleasedirに全コピー
cp -rf $currentdir/* $releasedir/
cp $currentdir/.htpasswd $releasedir/

# 環境設定ファイルを配置する
if [ ! -e $isProd ]; then
  if [ -e $isdev ]; then
    touch $releasedir/lib/${projectName}/.dev
    touch $releasedir/lib/${projectName}/.debug
    touch $releasedir/lib/FrameworkManager/.dev
    touch $releasedir/lib/FrameworkManager/.debug
  fi
fi

# 自動環境判別を無効化
rm -rf $releasedir/lib/${projectName}/.autostagecheck
rm -rf $releasedir/lib/FrameworkManager/.autostagecheck
# 自動マイグレーションを無効化
rm -rf $releasedir/lib/${projectName}/.automigration
rm -rf $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
rm -rf $releasedir/lib/FrameworkManager/.automigration
rm -rf $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations

# 所有権の統一
chown -R nginx:nginx $releasedir

# nginx設定を書き換える
cp -rf $releasedir/docs/setting/NginxWithPHPFPM/nginx-project-web.conf /etc/nginx/conf.d/
# SSL設定
if [ ! -e /var/www/.ssl ]; then
  mkdir /var/www/.ssl
fi
cp -rf $releasedir/docs/key/ssl/private.* /var/www/.ssl/
# 所有権の統一
chown -R nginx:nginx /var/www/.ssl/
# 参照権の統一
chmod -R 0600 /var/www/.ssl/

#PHP-FPMの設定を書き換える
sed -i -e "s/;request_terminate_timeout = 0/request_terminate_timeout = 180/" /etc/php-fpm-5.6.d/www.conf
sed -i -e "s/request_terminate_timeout = 180/request_terminate_timeout = 600/" /etc/php-fpm-5.6.d/www.conf

#PHP-MyAdminの設定を書き換える
rm -rf $releasedir/managedocs/supple/myadm/config.inc.php
if [  -e $isProd ]; then
   rm -rf $releasedir/managedocs/supple/myadm/config.inc.dev.php
   mv $releasedir/managedocs/supple/myadm/config.inc.prod.php $releasedir/managedocs/supple/myadm/config.inc.php
else
  if [ -e $isdev ]; then
     rm -rf $releasedir/managedocs/supple/myadm/config.inc.prod.php
     mv $releasedir/managedocs/supple/myadm/config.inc.dev.php $releasedir/managedocs/supple/myadm/config.inc.php
  fi
fi

# ミドルウェアの追加
# ClamAV
if [ ! -e /opt/scripts/clamav/virusscan.sh ]; then
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
  cp -rf $currentdir/bin/job/virusscan.sh /opt/scripts/clamav/
  chmod -R 0755 /opt/scripts/clamav/virusscan.sh
  # スキャン除外場所の追加
  echo "/proc/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/sys/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/boot/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/home/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/selinux/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/usr/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/dev/" >> /opt/scripts/clamav/clamscan.exclude
  echo "/srv/" >> /opt/scripts/clamav/clamscan.exclude
  # 毎日実行のクローンに登録
  ln -s /opt/scripts/clamav/virusscan.sh /etc/cron.daily/
fi
# 自動スキャンスクリプトの更新
cp -rf $currentdir/bin/job/virusscan.sh /opt/scripts/clamav/
chmod -R 0755 /opt/scripts/clamav/virusscan.sh

# 危ないので設定ファイルは削除
rm -rf ./docs/setting/cloudformation
rm -rf ./docs/setting/conf
rm -rf ./docs/setting/install
rm -rf ./docs/key
# コピー後の危ない設定ファイルを更に削除
rm -rf $currentdir/docs/setting/cloudformation
rm -rf $currentdir/docs/setting/install
rm -rf $currentdir/docs/key
rm -rf $releasedir/docs/setting/cloudformation
rm -rf $releasedir/docs/setting/conf
rm -rf $releasedir/docs/setting/install
rm -rf $releasedir/docs/key

# リバースプロキシ用のディレクトリを作成
if [ ! -e /var/www/cache ]; then
  mkdir /var/www/cache
fi
if [ ! -e /var/www/cache/nginx ]; then
  mkdir /var/www/cache/nginx
fi
if [ ! -e /var/www/cache/nginx/cache ]; then
  mkdir /var/www/cache/nginx/cache
fi
if [ ! -e /var/www/cache/nginx/tmp ]; then
  mkdir /var/www/cache/nginx/tmp
fi
chown -R nginx:nginx /var/www/cache
chmod -R 0777 /var/www/cache

# マイグレーション
# マイグレーションが他のインスタンスで実行中かどうか
if aws s3 ls s3://$backetname/automigration/.migration
then
  # 他のインスタンスがやってくれているので無視する
  echo 'skip'
else
  # リバースプロキシ対象のスタティックファイルをS3にアップする
  aws s3 cp $currentdir/webdocs/static/ s3://$backetname/static/ --acl public-read --recursive
  aws s3 cp $currentdir/webdocs/index.html s3://$backetname/static/index.html --acl public-read
  aws s3 cp $currentdir/webdocs/home.html s3://$backetname/static/home.html --acl public-read
  aws s3 cp $currentdir/webdocs/sp/static/ s3://$backetname/static/sp/ --acl public-read --recursive
  aws s3 cp $currentdir/webdocs/sp/index.html s3://$backetname/static/sp/index.html --acl public-read
  aws s3 cp $currentdir/webdocs/sp/home.html s3://$backetname/static/sp/home.html --acl public-read

  # マイグレーションを開始する準備
  touch .migration
  # 他のインスタンスからのマイグレーションの割り込みを防止する
  aws s3 cp .migration s3://$backetname/automigration/ 
  # 前回のマイグレーション結果を取得
  if aws s3 ls s3://$backetname/automigration/default.dispatched.migrations
  then
    aws s3 cp s3://$backetname/automigration/default.dispatched.migrations $releasedir/lib/${projectName}/automigration/ 
    chown nginx:nginx $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
    chmod 0777 $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
  fi
  if aws s3 ls s3://$backetname/automigration/fwm/default.dispatched.migrations
  then
    aws s3 cp s3://$backetname/automigration/fwm/default.dispatched.migrations $releasedir/lib/FrameworkManager/automigration/ 
    chown nginx:nginx $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations
    chmod 0777 $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations
  fi

  # マイグレーション実行
  php $releasedir/bin/job/migration.php
  chown nginx:nginx $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
  chmod 0777 $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
  php $releasedir/bin/job/migration.php FrameworkManager
  chown nginx:nginx $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations
  chmod 0777 $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations
  # マイグレーション結果を更新
  aws s3 cp $releasedir/lib/${projectName}/automigration/default.dispatched.migrations s3://$backetname/automigration/
  aws s3 cp $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations s3://$backetname/automigration/fwm/
  aws s3 rm s3://$backetname/automigration/.migration
  rm -rf .migration
fi
