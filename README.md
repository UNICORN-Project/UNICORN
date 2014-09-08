UNICORN
====
可能性を追求した(する)フレームワーク  
主にスマホアプリケーション開発において「Fast Start」である事に重点を置いています。

##■利用開始方法
###composerを使ったインストール
あなたのcomposer.jsonに以下のパッケージを追記し、php composer install を実行する
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



##■その他リファレンス
http://saimushi.github.io/UNICORN/

##■ライセンスについて
**MITライセンスとします。**

内包している画像の著作権や肖像権等は各権利所有者に帰属致します。  
著作権・肖像権及び各権利所有者様からの削除依頼があった場合、即時削除を行います。（主にNT-Dに関する画像の事）

