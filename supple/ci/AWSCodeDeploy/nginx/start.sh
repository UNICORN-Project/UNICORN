#!/bin/bash

isdev=/var/www/.dev
isProd=/var/www/.production

# 観葉によって設定が別れる変数の定義
# 開発環境用
devbacketname=spajam2016
devregion=ap-northeast-1
# リリース環境用
prodbacketname=spajam2016
prodregion=ap-northeast-1

# 観葉による設定の振り分け
backetname=$prodbacketname
region=$prodregion
if [  -e $isProd ]; then
   rm -rf $isdev
else
  if [ -e $isdev ]; then
    # 開発用のバケットに向ける
    backetname=$devbacketname
    region=$devregion
  fi
fi

# リバースプロキシキャッシュを全て削除
rm -rf /var/www/cache/nginx/cache/*
rm -rf /var/www/cache/nginx/tmp*

# サーバー再起動
service nginx restart
service php-fpm restart

# 起動後にPHPセッションディレクトリの書込権限を変更
chmod -R 0777 /var/lib/php/7.0/session/

# メンテナンスを開放
maintenancefile=/var/www/release/lib/FrameworkPackage/.maintenance
if [ -e $maintenancefile ]; then
   mv $maintenancefile $maintenancefile.bk
fi

# プロジェクト毎の専用のstartシェル

# UIテストを実行
if [ -e $isdev ]; then
  # 本番リリースはテストを実行しない(今のところ)
  sh /var/www/release/supple/ci/SHouldbee/uitest.sh
fi

# locateを更新
updatedb
