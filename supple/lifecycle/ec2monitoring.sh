#!/bin/bash

# 自分自身のファイルパス
mepath=${0}

# NewrelicによるEC2監視設定
# (!!!) EC2専用です！
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
newrelicLicenseKey=abcd1234
isdev=/var/www/.dev
isProd=/var/www/.production
# 環境によって設定が別れる変数の定義
devbacketname=unicorndev
prodbacketname=unicorn

# ------------------------------- 変数定義ココまで(適宜変更) ------------------------------- 

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

# ホスト名をアプリケーションプレフィックス-LocalIPｂの形に変更する
EC2_PREFIX=$backetname
LOCAL_IPV4=`curl -s http://169.254.169.254/latest/meta-data/local-ipv4`
NAME=${EC2_PREFIX}-${LOCAL_IPV4//./-}
echo "NAME: " ${NAME}
hostname ${NAME}

# NewRelicクライアントのインストール
if [ ! -e /etc/init.d/newrelic-sysmond ]; then
  rpm -Uvh http://download.newrelic.com/pub/newrelic/el5/i386/newrelic-repo-5-3.noarch.rpm
  yum install newrelic-sysmond -y
  nrsysmond-config --set license_key=${newrelicLicenseKey}
  /etc/init.d/newrelic-sysmond start
  chkconfig newrelic-sysmond on
fi

exit 0
