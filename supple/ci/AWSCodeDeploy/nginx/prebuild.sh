#!/bin/bash

# 時刻合わせ
chkconfig ntpd on

# OpenSSLアップデート
if ! grep "releasever=latest" /etc/yum.conf > /dev/null 2>&1; then
  # repoの向け先をlatesに向ける
  sed -i -e "14a releasever=latest" /etc/yum.conf
  yum clean all
fi
yum update openssl -y

currentdir=/var/www/current
backupdir=/var/www/backup
releasedir=/var/www/release

# currentdirが存在する場合は削除
if [  -e $currentdir ]; then
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
