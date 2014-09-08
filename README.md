UNICORN
====
可能性を追求した(する)フレームワーク  
主にスマホアプリケーション開発において「Fast Start」である事に重点を置いています。

##■利用開始方法
###composerを使ったインストール
1.あなたのcomposer.jsonに以下のパッケージを追記し、「php composer.phar install」を実行します。
```
        { "packagist": false },
        {
            "type": "package",
            "package": {
                "name": "UNICORN",
                "version": "1",
                "dist": {
                    "url": "https://github.com/UNICORN-Project/UNICORN/archive/master.zip",
                    "type": "zip"
                },
                "source": {
                    "url": "https://github.com/UNICORN-project/UNICORN.git",
                    "type": "git",
                    "reference": "master"
                }
            }
        }
```

2.composer install後、「UNICORN」ディレクトリが所定のvendor-dir以下に出来ています。  
「UNICORN」ディレクトリに移動し、「UNICORN」ディレクトリ内で再度「php composer.phar install」を実行して下さい。  

**2'最後に、フレームワークの初期設定を簡略化する為に、「UNICORN」コマンドを実行しましょう！**  
UNICORNディレクトリ配下の「php composer.phar install」を実行した後には、貴方の「vendor-dir」設定しているディレクトリと並列の場所に「lib」ディレクトリが出来ているハズです。  
コンソールから「lib」ディレクトリ配下まで移動し、以下のコマンドを実行して、指示に従いましょう！  
**php UNICORN**  


###手動インストール
1.以下のリンクからUNICORNを入手して下さい。  
https://github.com/UNICORN-Project/UNICORN/archive/master.zip  

2.利用したいプロジェクトのドキュメントルートディレクトリと同階層に「vendor」ディレクトリを作成して下さい。  

3.ダウンロードしたUNICORNを解凍し、ディレクトリ名を「UNICORN」に変更後、上記ディレクトリ配下に配置して下さい。  

4.コンソールから「UNICORN」ディレクトリへ移動し、「php composer.phar install」を実行して下さい。  

**4'最後に、フレームワークの初期設定を簡略化する為に、「UNICORN」コマンドを実行しましょう！**  
UNICORNディレクトリ配下の「php composer.phar install」を実行した後には、「vendor」ディレクトリと並列の場所に「lib」ディレクトリが出来ているハズです。  
コンソールから「lib」ディレクトリ配下まで移動し、以下のコマンドを実行して、指示に従いましょう！  
**php UNICORN**  


##■その他リファレンス
###UNICORN WEB
http://UNICORN-Project.github.io/


###開発ブログ
http://saimushi.github.io/


##■ライセンスについて
**MITライセンスとします。**

内包している画像の著作権や肖像権等は各権利所有者に帰属致します。  
著作権・肖像権及び各権利所有者様からの削除依頼があった場合、即時削除を行います。（主にNT-Dに関する画像の事）

