UNICORN
====
可能性を追求した(する)フレームワーク  

主にスマホアプリケーション開発において「Fast Start」である事に重点を置いています。  



##■利用開始方法  

###composerを使ったインストール  

1.あなたのcomposer.jsonに以下のパッケージを追記し、「php composer.phar install」を実行します。
```
    "repositories": [
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
    ],
    "require": {
        "UNICORN": "1"
    }
```

2.composer install後、「UNICORN」ディレクトリが所定のvendor-dir以下に出来ています。  
「UNICORN」ディレクトリに移動し、「UNICORN」ディレクトリ内で再度「php composer.phar install」を実行して下さい。  
※最新版がどうしても入手できない場合、composerのキャッシュに古いバージョンが残ってしまっている場合があります。
そう言う場合は「~/.composer」の中身を削除(sudo rm -rf ~/.composer/)してから「php composer.phar install」を試して下さい。

###手動インストール  

1.以下のリンクからUNICORNを入手して下さい。  

https://github.com/UNICORN-Project/UNICORN/archive/master.zip  

2.利用したいプロジェクトのドキュメントルートディレクトリと同階層に「vendor」ディレクトリを作成して下さい。  

3.ダウンロードしたUNICORNを解凍し、ディレクトリ名を「UNICORN」に変更後、上記ディレクトリ配下に配置して下さい。  

4.コンソールから「UNICORN」ディレクトリへ移動し、「php composer.phar install」を実行して下さい。  


##■その他リファレンス  

###UNICORN WEB  

http://UNICORN-Project.github.io/


###APIドキュメント  

サーバーサイド  

https://cdn.rawgit.com/UNICORN-Project/UNICORN/master/docs/server/html/index.html  

iOS  

https://cdn.rawgit.com/UNICORN-Project/UNICORN/master/docs/ios/html/index.html  

Android  

https://cdn.rawgit.com/UNICORN-Project/UNICORN/master/docs/android/html/index.html  


###開発ブログ  

http://saimushi.github.io/



##■ライセンスについて  

**MITライセンスとします。**

※内包している画像の著作権や肖像権等は各権利所有者に帰属致します。  
著作権・肖像権及び各権利所有者様からの削除依頼があった場合、即時削除を行います。（主にNT-Dに関する画像の事）  

※ライセンスの表示をUNICORNは強制しません。  
書いても書かなくてもどちらでも構いません。  

http://unicorn-project.github.io/licenses.html  

