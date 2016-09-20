#!/bin/bash

# AWS Linuxの初期化設定用
# ami-29160d47で検証しています

# 変数定義
currentdir=/var/www/current
backupdir=/var/www/backup
releasedir=/var/www/release

# メモリー解放
free
sync
echo 3 > /proc/sys/vm/drop_caches
free

# 時刻合わせ
chkconfig ntpd on

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
# MySQLのインストール(不要な場合はコメントアウトして下さい)
if [ ! -e /etc/my.cnf ]; then
  rpm -ihv http://dev.mysql.com/get/mysql-community-release-el6-5.noarch.rpm
  yum install -y mysql mysql-server
  # サービス起動設定
  service mysqld start
  # 自動起動設定
  chkconfig mysqld on
fi
# Nginx+PHP-FPMのインストール(不要な場合はコメントアウトして下さい)
if [ ! -e /etc/nginx/nginx.conf ]; then
  # opensslとlibsshを先ずは入れて置く
  yum install -y openssl openssl-devel libssh2 libssh2-devel
  rpm -Uvh ftp://ftp.scientificlinux.org/linux/scientific/6.4/x86_64/updates/fastbugs/scl-utils-20120927-8.el6.x86_64.rpm
  rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
  yum -y install libwebp --disablerepo=amzn-main --enablerepo=epel
  yum -y install libmcrypt libtool-ltdl libtidy libXpm libtiff tcl gd-last autoconf automake
  yum install --enablerepo=epel,remi,remi-php70 nginx php70 php70-php-devel php70-php-fpm php70-php-mbstring php70-php-mysqlnd php70-php-xml php70-php-xmlrpc php70-php-soap php70-php-opcache php70-php-mcrypt php70-php-gd php70-php-pecl-apcu php70-php-pecl-apcu-bc php70-php-pecl-apcu-devel php70-php-pecl-memcache php70-php-pecl-memcached -y
  # phpコマンドで実行出来るようにリンクを貼る
  ln -s /usr/bin/php70 /usr/bin/php
  ln -s /etc/rc.d/init.d/php70-php-fpm /etc/rc.d/init.d/php-fpm
  # Nginxを1.9にアップデートするために、一度削除
  yum remove -y nginx
  rpm -ivh http://nginx.org/packages/centos/6/noarch/RPMS/nginx-release-centos-6-0.el6.ngx.noarch.rpm
  sed -i -e "s/baseurl=http:\/\/nginx.org\/packages\/centos\/6\/\$basearch\//baseurl=http:\/\/nginx.org\/packages\/mainline\/centos\/6\/\$basearch\//" /etc/yum.repos.d/nginx.repo 
  sed -i -e "7a priority=1" /etc/yum.repos.d/nginx.repo
  yum install -y nginx
  # 設定ファイルの修正
  sed -i -e "s/user = apache/user = nginx/" /etc/opt/remi/php70/php-fpm.d/www.conf
  sed -i -e "s/group = apache/group = nginx/" /etc/opt/remi/php70/php-fpm.d/www.conf
  sed -i -e "s/listen = 127.0.0.1:9000/listen =  \/var\/run\/php-fpm\/php-fpm.sock/" /etc/opt/remi/php70/php-fpm.d/www.conf
  sed -i -e "s/;listen.owner = nobody/listen.owner = nginx/" /etc/opt/remi/php70/php-fpm.d/www.conf
  sed -i -e "s/;listen.group = nobody/listen.group = nginx/" /etc/opt/remi/php70/php-fpm.d/www.conf
  sed -i -e "s/;request_terminate_timeout = 0/request_terminate_timeout = 600/" /etc/opt/remi/php70/php-fpm.d/www.conf
  if [ ! -e /var/run/php-fpm/ ]; then
    mkdir /var/run/php-fpm/
  fi
  # サービス起動設定
  service nginx restart
  service php-fpm restart
  # 自動起動設定
  chkconfig nginx on
  chkconfig php-fpm on
fi
# Nginx用のバーチャルホスト用confの置き場所を初期化
if [ ! -e /etc/nginx/conf.d/ ]; then
  mkdir /etc/nginx/conf.d/
fi
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

# 必要なミドルウェアの自動アップデート
# OpenSSLアップデート
yum update openssl -y

# デプロイ前処理
# currentdirが存在する場合は削除
if [ -e $currentdir ]; then
   rm -rf $currentdir
fi

# backupdirが存在するかチェック
if [ ! -e $backupdir ]; then
   mkdir $backupdir
fi

# 所有権の統一
chown -R nginx:nginx  $backupdir

# releaseにファイルが存在するかチェック
if [ -e $releasedir/lib/FrameworkPackage/ ]; then
   # メンテナンス中に移行する
   touch $releasedir/lib/FrameworkPackage/.maintenance
   chown nginx:nginx $releasedir/lib/FrameworkPackage/.maintenance
   chmod 0666 $releasedir/lib/FrameworkPackage/.maintenance
   # 存在ればbackupdirに全コピー
   cp -rf $releasedir/* $backupdir/
fi

# locateDBを更新
updatedb
