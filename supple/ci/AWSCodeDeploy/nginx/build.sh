#!/bin/bash

projectName=ProjectPackage
devdomain=project.domain
proddomain=project.domain
isdev=/var/www/.dev
isProd=/var/www/.production
logdir=/var/www/logs
tmpdir=/var/www/tmp
currentdir=/var/www/current
backupdir=/var/www/backup
releasedir=/var/www/release

# 環境によって設定が別れる変数の定義
# 開発環境用
devbacketname=projectbacuketdev
devregion=ap-northeast-1
devloadbalancer=developmen-elbaslb-KEEUWWOJMQRE
# リリース環境用
prodbacketname=projectbacuket
prodregion=ap-northeast-1
prodloadbalancer=production-elbaslb-KEEUWWOJMQRE

# 環境による設定の振り分け
backetname=$prodbacketname
region=$prodregion
loadbalancer=$prodloadbalancer
domain=$proddomain
if [ -e $isProd ]; then
   rm -rf $isdev
else
  if [ -e $isdev ]; then
    # 開発用のバケットに向ける
    backetname=$devbacketname
    region=$devregion
    loadbalancer=$devloadbalancer
    domain=$devdomain
  fi
fi

# ProxyProtocolを有効にする(HTTP2用)
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

# htpasswdの移動
if [ ! -e $currentdir/.htpasswd ]; then
  cp $currentdir/.htpasswd $releasedir/
fi

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
if [ ! -e /etc/nginx/conf.d/nginx-${projectName}.conf ]; then
  cp -rf $releasedir/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf /etc/nginx/conf.d/nginx-${projectName}.conf
  sed -i -e "s/service.domain/${domain}/" /etc/nginx/conf.d/nginx-${projectName}.conf
  sed -i -e "s/development.domain/${domain}/" /etc/nginx/conf.d/nginx-${projectName}.conf
  sed -i -e "s/production.domain/${domain}/" /etc/nginx/conf.d/nginx-${projectName}.conf
fi

# 自己署名SSLを仮設定
if [ ! -e /var/www/.ssl ]; then
  mkdir /var/www/.ssl
fi
if [ -e $releasedir/supple/setting/NginxWithPHPFPM/.ssl/server.key ]; then
  cp -rf $releasedir/supple/setting/NginxWithPHPFPM/.ssl/server.* /var/www/.ssl/
fi
# 所有権の統一
chown -R nginx:nginx /var/www/.ssl/
# 参照権の統一
chmod -R 0600 /var/www/.ssl/

# PHP-MyAdminの設定を書き換える
if [ -e $releasedir/lib/FrameworkPackage/ ]; then
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
fi

# 危ないので設定ファイルは削除
rm -rf ./docs/setting
rm -rf ./supple/setting
# コピー後の危ない設定ファイルを更に削除
rm -rf $currentdir/docs/setting
rm -rf $currentdir/supple/setting
rm -rf $releasedir/docs/setting
rm -rf $releasedir/supple/setting

# マイグレーション
# マイグレーションが他のインスタンスで実行中かどうか
if aws s3 ls s3://$backetname/automigration/.migration
then
  # 他のインスタンスがやってくれているので無視する
  echo 'skip'
else
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
  php $releasedir/supple/ci/Framework/migration.php
  chown nginx:nginx $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
  chmod 0777 $releasedir/lib/${projectName}/automigration/default.dispatched.migrations
  php $releasedir/supple/ci/Framework/migration.php FrameworkManager
  chown nginx:nginx $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations
  chmod 0777 $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations
  # マイグレーション結果を更新
  aws s3 cp $releasedir/lib/${projectName}/automigration/default.dispatched.migrations s3://$backetname/automigration/
  aws s3 cp $releasedir/lib/FrameworkManager/automigration/default.dispatched.migrations s3://$backetname/automigration/fwm/
  aws s3 rm s3://$backetname/automigration/.migration
  rm -rf .migration
fi
