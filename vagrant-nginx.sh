#!/bin/bash
# MacOSのsedで動くように設計されています

cmd=${1}
imageFilePath=${2}

# ディレクトリ名をデフォルトのプロジェクト名とする
fpath=$(cd $(dirname $0); pwd)
fdir="${fpath##*/}"
echo ''
echo 'Vagrant '${fdir}' box'
echo ''

# 引数の存在チェック
if [ ! 0 -lt ${#cmd} ]; then
  echo 'Error :コマンドが無指定です。以下のどれかのコマンドを指定して下さい'
  echo 'start :VMを作成起動します。'
  echo 'login :VMにログインします。'
  echo 'reload :VMを再起動し、VMにログインします。その際、新しい設定の読み込みを試みます。'
  exit;
fi

# 削除は即
if [ ${cmd} = 'remove' ]; then
  # 停止
  echo 'remove VM'
  cd ~/VM/${fdir} && vagrant box remove ${fdir} && vagrant destroy
  exit
fi

# 必要なパッケージのインストール
# Command Line Toolsの存在チェック
if [ ! "`which xcode-select | grep -e 'xcode-select'`" ]; then
  echo 'Command Line Toolsがインストールされていません。'
  echo '処理を中断します。'
  echo 'Command Line Toolsの最新版をインストールしてから再度実行してみて下さい。'
  exit 0;
fi

# Rubyの存在チェック
if [ ! "`which ruby | grep -e 'ruby'`" ]; then
  echo 'sudo chown -R $USER /usr/local'
  sudo chown -R $USER /usr/local
  # Command Line Tools for XcodeをインストールしてRubyを使えるようにする
  echo 'install ruby'
  xcode-select --install
fi

# Homebrewの存在チェック
if [ ! "`which brew | grep -e 'brew'`" ]; then
  echo 'sudo chown -R $USER /usr/local'
  sudo chown -R $USER /usr/local
  echo 'install brew'
  /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
  # brewを一旦アンインストールしたい場合は以下を実行
  # ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/uninstall)"
  if [ ! "`brew -v | grep -e 'brew'`" ]; then
    echo 'brewのインストールに失敗しました。'
    echo '処理を中断します。'
    echo 'Command Line Toolsの最新版をインストールしてから再度実行してみて下さい。'
    exit 0;
  fi
fi

# brew caskの存在チェック
if [ ! -e /usr/local/Homebrew/Library/Taps/caskroom/homebrew-cask ]; then
  echo 'sudo chown -R $USER /usr/local'
  sudo chown -R $USER /usr/local
  # brewの更新
  echo 'brew update'
  brew update
  # brew caskのインストール
  brew tap caskroom/cask
  echo 'brew installed & updated'
fi

# virtualboxの存在チェック
if [ ! "`which virtualbox | grep -e 'virtualbox'`" ]; then
  echo 'sudo chown -R $USER /usr/local'
  sudo chown -R $USER /usr/local
  # virtualboxのインストール
  brew cask install virtualbox
fi

# vagrantの存在チェック
if [ ! "`which vagrant | grep -e 'vagrant'`" ]; then
  echo 'sudo chown -R $USER /usr/local'
  sudo chown -R $USER /usr/local
  brew cask install vagrant
fi

# gsed入れとく
if [ ! -e /usr/local/opt/gnu-sed/ ]; then
  echo 'sudo chown -R $USER /usr/local'
  sudo chown -R $USER /usr/local
  brew install gnu-sed
fi

# VM置き場のディレクトリを生成
if [ ! -e ~/VM ]; then
  mkdir -p ~/VM
fi

# プロジェクトディレクトリを~/VMにlnする
if [ ! -e ~/VM/${fdir}/ ]; then
  ln -s ${fpath} ~/VM/
fi

# boxの数に応じてローカルIPを変える
localip=10
vmlist=`vagrant box list`
vmlist=`echo ${vmlist} | sed -e "s/) /)@/g"`
vmlist=`echo $vmlist | tr -s ' ' '+'`
arr=( `echo $vmlist | tr -s '@' ' '`)
localip=`expr $localip + ${#arr[@]}`

# Vagrantfileを環境に合わせて書き換える
sed -i '' -e "s/config.vm.box = \"base\"/config.vm.box = \"${fdir}\"/" ${fpath}/Vagrantfile
sed -i '' -e "s/config.vm.network \"private_network\", ip: \"192.168.33.10\"/config.vm.network \"private_network\", ip: \"192.168.33.${localip}\"/" ${fpath}/Vagrantfile
sed -i '' -e "s/# config.vm.provider \"virtualbox\" do |vb|/config.vm.provider \"virtualbox\" do |vb|/" ${fpath}/Vagrantfile
sed -i '' -e "s:# config.vm.synced_folder \"../data\"\, \"/vagrant_data\":config.vm.synced_folder \"~/VM/${fdir}\"\, \"/var/www\":" ${fpath}/Vagrantfile
sed -i '' -e "s/#   vb.memory = \"1024\"/  vb.memory = "2048"/" ${fpath}/Vagrantfile
sed -i '' -e "49 s/  #/    vb.cpus = 2/" ${fpath}/Vagrantfile
sed -i '' -e "s/#   vb.memory = \"1024\"/  vb.memory = "2048"/" ${fpath}/Vagrantfile
sed -i '' -e "52 s/# end/end/" ${fpath}/Vagrantfile
sed -i '' -e "s/# config.vm.provision \"shell\", inline: <<-SHELL/config.vm.provision \"shell\", inline: <<-SHELL/" ${fpath}/Vagrantfile
sed -i '' -e "70 s/# SHELL/SHELL/" ${fpath}/Vagrantfile
if ! grep "docker_autostart.service" ${fpath}/Vagrantfile > /dev/null 2>&1; then
  echo 'add config.vm.provision'
  gsed -i -e "70i ln -s \/var\/www\/ \/var\/www\/release" ${fpath}/Vagrantfile
  gsed -i -e "71i sh -c \'cat << EOF > \/usr\/lib\/systemd\/system\/docker_autostart.service" ${fpath}/Vagrantfile
  gsed -i -e "72i [Unit]" ${fpath}/Vagrantfile
  gsed -i -e "73i Description=auto start of docker containers" ${fpath}/Vagrantfile
  gsed -i -e "74i After=docker.service" ${fpath}/Vagrantfile
  gsed -i -e "75i Requires=docker.service" ${fpath}/Vagrantfile
  gsed -i -e "76i [Service]" ${fpath}/Vagrantfile
  gsed -i -e "77i ExecStart=/bin/bash -c \"/usr/bin/docker start mysqld web\"" ${fpath}/Vagrantfile
  gsed -i -e "78i [Install]" ${fpath}/Vagrantfile
  gsed -i -e "79i WantedBy=multi-user.target" ${fpath}/Vagrantfile
  gsed -i -e "80i EOF\'" ${fpath}/Vagrantfile
  gsed -i -e "81i systemctl enable docker_autostart.service" ${fpath}/Vagrantfile
  gsed -i -e "82i systemctl start docker_autostart" ${fpath}/Vagrantfile
  gsed -i -e "83i systemctl restart nginx" ${fpath}/Vagrantfile
fi
# 不要な作業ファイルが出来るので削除
rm -rf ${fpath}/Vagrantfile-e

# UNICORNの初期化
if [ ! -e ${fpath}/.ssl/ ]; then
  cp -Rf ${fpath}/supple/setting/NginxWithPHPFPM/.ssl ${fpath}/
fi
mkdir -p ${fpath}/cache/nginx
sudo chmod -R 0777 ${fpath}/logs
sudo chmod -R 0777 ${fpath}/cache/nginx
sudo chmod -R 0777 ${fpath}/cache/nginx
if [ -e ${fpath}/lib ]; then
  sudo chmod -R 0777 ${fpath}/lib
fi
if [ -e ${fpath}/lib/FrameworkManager/autogenerate ]; then
  sudo chmod -R 0777 ${fpath}/lib/FrameworkManager
fi
if [ -e ${fpath}/lib/FrameworkManager/autogenerate ]; then
  sudo chmod -R 0777 ${fpath}/lib/FrameworkManager/autogenerate
fi
if [ -e ${fpath}/lib/FrameworkManager/automigration ]; then
  sudo chmod -R 0777 ${fpath}/lib/FrameworkManager/automigration
fi
if [ -e ${fpath}/lib/${fdir}ProjectPackage/autogenerate ]; then
  sudo chmod -R 0777 ${fpath}/lib/${fdir}ProjectPackage
fi
if [ -e ${fpath}/lib/${fdir}ProjectPackage/autogenerate ]; then
  sudo chmod -R 0777 ${fpath}/lib/${fdir}ProjectPackage/autogenerate
fi
if [ -e ${fpath}/lib/${fdir}ProjectPackage/automigration ]; then
  sudo chmod -R 0777 ${fpath}/lib/${fdir}ProjectPackage/automigration
fi
# Webサーバー設定
basedomain=`echo "$fdir" | tr 'A-Z' 'a-z'`
if [ -e ${fpath}/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf ]; then
  sed -i '' -e "s/localapiservice.domain/api${basedomain}.localhost/" ${fpath}/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf
  sed -i '' -e "s/localwebservice.domain/web${basedomain}.localhost/" ${fpath}/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf
  sed -i '' -e "s/localfwmservice.domain/fwm${basedomain}.localhost/" ${fpath}/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf
  # 仮で書き換えてしまう
  sed -i '' -e "s:/ProjectPackage/:/${fdir}ProjectPackage/:" ${fpath}/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf
fi
rm -rf ${fpath}/supple/setting/NginxWithPHPFPM/conf.d/nginx-linux.conf-e
# Vagrant用にデフォルトのローカルフラグのDB設定を書き換える
sed -i '' -e "s/\$host = \'localhost\'/\$host = \'mysqld\'/" ${fpath}/lib/GenericPackage/class/ORM/GenericMigrationManager.class.php
sed -i '' -e "s/fwmpass@localhost/fwmpass@mysqld/" ${fpath}/lib/FrameworkManager/core/FrameworkManager.config.xml
sed -i '' -e "s/projectpass@localhost/projectpass@mysqld/" ${fpath}/lib/FrameworkManager/sample/packages/ProjectPackage/core/Project.config.xml

# hosts書換
if ! grep "192.168.33.${localip}   api${basedomain}.localhost" /etc/hosts > /dev/null 2>&1; then
  sudo sed -i '' -e "1s/^/192.168.33.${localip}   api${basedomain}.localhost"\\$'\n'"/" /etc/hosts
fi
if ! grep "192.168.33.${localip}   web${basedomain}.localhost" /etc/hosts > /dev/null 2>&1; then
  sudo sed -i '' -e "1s/^/192.168.33.${localip}   web${basedomain}.localhost"\\$'\n'"/" /etc/hosts
fi
if ! grep "192.168.33.${localip}   fwm${basedomain}.localhost" /etc/hosts > /dev/null 2>&1; then
  sudo sed -i '' -e "1s/^/192.168.33.${localip}   fwm${basedomain}.localhost"\\$'\n'"/" /etc/hosts
fi

# virtualマシンが追加済みかどうかチェックする
if [ ! "`echo $vmlist | grep -e ${fdir}`" ]; then
  # まだ無いので初期化
  echo 'create VM'
  # BOXを追加
  if [ ! 0 -lt ${#imageFilePath} ]; then
    # NetからUNICORNのイメージファイルをDLしてbox add
    echo "vagrant box add ${fdir} https://dl.dropboxusercontent.com/u/22810487/VM/nginx110php70mysql56andmemcached14.box --force"
    vagrant box add ${fdir} https://dl.dropboxusercontent.com/u/22810487/VM/nginx110php70mysql56andmemcached14.box --force
  else
    # ファイル指定でのbox add
    echo "vagrant box add ${fdir} ${imageFilePath} --force"
    vagrant box add ${fdir} ${imageFilePath} --force
  fi
  # vagrantを起動
  if [ ! ${cmd} = 'start' ]; then
    echo 'init start VM'
    cd ~/VM/${fdir} && vagrant up
  fi
fi

if [ ${cmd} = 'start' ]; then
  # 開始
  echo 'start VM'
  cd ~/VM/${fdir} && vagrant up && open https://fwm${basedomain}.localhost/migration.php
fi

if [ ${cmd} = 'stop' ]; then
  # 停止
  echo 'stop VM'
  cd ~/VM/${fdir} && vagrant halt
fi

if [ ${cmd} = 'package' ]; then
  # アーカイブ
  echo 'export VM image'
  cd ~/VM/${fdir} && vagrant halt && vagrant package && vagrant up
fi

if [ ${cmd} = 'reload' ]; then
  # 再読込
  echo 'reload VM'
  cd ~/VM/${fdir} && vagrant halt && vagrant up
fi

# ログイン
[ ${cmd} = 'login' -o ${cmd} = 'reload' ] && echo 'login VM' && cd ~/VM/${fdir} && vagrant ssh

# shell終了
exit 0;