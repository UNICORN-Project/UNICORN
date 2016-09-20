#!/bin/bash

isdev=/var/www/.dev
isProd=/var/www/.production

# SSL設定を実行
# Nginx用
sh /var/www/release/supple/lifecycle/Nginx/sslsetting.sh >> /tmp/sslsetting.log

# ウィルススキャンの定期実行をインストール
#if [ ! -e /tmp/virusscaned ]; then
#  # ウィルススキャンを実行
#  sh /var/www/release/bin/job/virusscan.sh
  # ウィルススキャンインストール済みにする
#  touch /tmp/virusscaned
#else
  # cron自動登録・更新だけして、スキャンはしない
  # このシェルファイルが更新されてる場合もあるので、毎回書き換える
#  \cp -rf /var/www/release/supple/lifecycle/virusscan.sh /etc/cron.daily/
#  chmod -R 0755 /etc/cron.daily/
  # 行置換
#  sed -i -e "15 s/.*/10 5 * * * root run-parts \/etc\/cron.daily/" /etc/crontab
#fi

# logrotateを実行
# Nginx用
sh /var/www/release/supple/lifecycle/Nginx/logrotate.sh

# UIテストを実行
#if [ -e $isdev -a -e /tmp/uitestinit ]; then
  # 本番リリースはテストを実行しない(今のところ)
#  sh /var/www/release/supple/ci/Shouldbee/uitest.sh
#fi
#touch /tmp/uitestinit

# ココまで来たら監視に登録実行
#sh /var/www/release/supple/lifecycle/ec2monitoring.sh
