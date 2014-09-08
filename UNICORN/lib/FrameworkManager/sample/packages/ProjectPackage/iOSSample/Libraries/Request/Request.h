//
//  Request.h
//
//  ローディング表示にまつわることは何もしません。
//  ローディング表示はシステムのよってその表示ポリシーが違う為です。
//  その変わり、RequestはDelegateを提供します。
//  Delegateを利用して、システムに合ったローディング表示を実施して下さい。
//
//  Created by saimushi on 14/06/16.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

@protocol RequestDelegate;

@interface Request : NSObject <NSURLSessionTaskDelegate>{
	id <RequestDelegate> delegate;
}

@property (strong, nonatomic) id<RequestDelegate> delegate;
@property (strong, nonatomic) NSString *userAgent;

// 通常のリクエスト処理
// NSMutableDictionary内のvalueはstringのみ
- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams;
// 通常のリクエスト処理に加えて、マルチパートでファイルをPOST(PUT)する
// XXX 大きいファイルのアップロードは次のメソッド- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;を使ってアップロードタスクで実行して下さい！
- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams :(NSData *)uploadData :(NSString *)fileName :(NSString *)contentType :(NSString *)dataKeyName;
// 通常のリクエスト処理に加えて、マルチパートでファイルをPOST(PUT)する
// ファイルをアップロードする
// XXX PUTメソッドでアップロードした場合、サーバー側でファイル以外に送信されたPOSTデータを判別出来なくなる事に注意して下さい！
- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;

// 以下スタティックメソッド定義
// アプリ固有のユーザーエージェンを作成
+ (NSString *)createUserAgent;

// support RESTful
// GET リソース参照
+ (void)get:(id)calledClass :(NSString *)requestURL;
+ (void)get:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
// POST リソース追加・更新・インクリメント・デクリメント
+ (void)post:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
+ (void)post:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSData *)uploadData :(NSString *)fileName :(NSString *)contentType :(NSString *)dataKeyName;
// ファイルアップロード
+ (void)post:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;
// PUT リソース追加・更新
+ (void)put:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
+ (void)put:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSData *)uploadData :(NSString *)fileName :(NSString *)contentType :(NSString *)dataKeyName;
// ファイルアップロード
+ (void)put:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;
// DELETE リソース削除
+ (void)delete:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
// HEAD リソース定義参照
+ (void)head:(id)calledClass :(NSString *)requestURL;

// Cookie関連
// CookieをCookieStorageにセットする(簡易設定版)
+ (void)setCookie:(NSString *)value forKey:(NSString *)key domain:(NSString *)domain;
// CookieをCookieStorageにセットする(詳細設定版)
+ (void)setCookie:(NSString *)value forKey:(NSString *)key domain:(NSString *)domain cookiePath:(NSString *)path expires:(NSString *)expires;
// CookieをNSUserDefaultから復帰する
+ (void)loadCookie;
// CookieをNSUserDefaultに保存し、永続化する
+ (void)saveCookie;

@end


@protocol RequestDelegate  <NSObject>
@optional
- (void)setSessionDataTask:(NSURLSessionTask *)task;
- (void)didFinishSuccess:(NSHTTPURLResponse *)responseHeader :(NSString *)responseBody;
- (void)didFinishError:(NSHTTPURLResponse *)responseHeader :(NSError *)failedHandler;
- (void)didChangeProgress:(double)packetBytesSent :(double)totalBytesSent :(double)totalBytesExpectedToSend;

@end
