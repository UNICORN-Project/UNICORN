//
//  ModelBase.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

typedef void(^RequestCompletionHandler)(BOOL success, NSInteger statusCode, NSHTTPURLResponse *responseHeader, NSString *responseBody, NSError* error);

// モデル参照モード
typedef enum{
	myResource,
	listedResource,
	automaticResource,
} loadResourceMode;


#import "Request.h"

@protocol ModelDelegate;

@interface ModelBase : NSObject <RequestDelegate>
{
    // モデルの共通規定値
    NSString *protocol;
    NSString *domain;
    NSString *urlbase;
    NSString *cryptKey;
    NSString *cryptIV;
    int timeout;
    NSString *tokenKeyName;
    NSString *modelName;
    NSString *ID;
    // 自分のリソースを参照する場合の修飾子（デフォルトでは必ず自分のリソース参照となるので、loadやsaveの際に空文字を入れて下さい）
    NSString *myResourcePrefix;
    // モデルは原則配列を許容する
    int index;
    int total;
    NSMutableArray *list;
    // 通信に関する変数
    BOOL replaced;
    BOOL requested;
    NSMutableDictionary *response;
    int statusCode;
    // Blockでハンドラを受け取るバージョンの為に用意
    RequestCompletionHandler completionHandler;
    // 非同期用にデレゲートを用意
	id <ModelDelegate> delegate;
    NSURLSessionTask* sessionDataTask;
}

@property (strong, nonatomic) NSURLSessionTask *sessionDataTask;
@property (strong, nonatomic) NSString *modelName;
@property (strong, nonatomic) NSString *ID;
@property (nonatomic) int index;
@property (nonatomic) int total;
@property (strong, nonatomic) id<ModelDelegate> delegate;

/* シングルトンでModelクラスを受け取る */
+ (id)getInstance;

/* 各種モデルの初期化処理 */
- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName;
- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName :(int)argTimeout;
/* トークンの生成時に暗号化を使う場合は以下のメソッドでinitする必要があります */
/* XXX また、暗号化鍵を渡さないでinitする場合は、- (NSString *)createToken; をオーバーライドして下さい！ */
- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName :(NSString *)argCryptKey :(NSString *)argCryptIV;
- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName :(NSString *)argCryptKey :(NSString *)argCryptIV :(int)argTimeout;

/* モデルのデータを外部からJSON配列のまま貰ってModelの最初期化を行えるようにするモデルデータのアクセサ */
- (void)setModelData:(NSMutableArray *)argDataArray;
- (void)setModelData:(NSMutableArray *)argDataArray :(int)argIndex;

// RESTfulURLの生成ロジック
// XXX システムによって実装が変わる場合はオーバーライドして適宜変更して下さい
- (NSString *)createURLString:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argMyResourcePrefix :(NSString *)argModelName :(NSString *)argResourceID;

/* 単一モデルを読み込む(読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)load;
/* 単一モデルを読み込む(BlockでHandlerを受け取れるバージョン:読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)load:(RequestCompletionHandler)argCompletionHandler;
/* モデルリストを読み込む(読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)list;
/* モデルリストを読み込む(BlockでHandlerを受け取れるバージョン:読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)list:(RequestCompletionHandler)argCompletionHandler;
/* 条件指定でモデルを読み込む(読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)query:(NSMutableDictionary *)argWhereParams;
/* 条件指定でモデルを読み込む(BlockでHandlerを受け取れるバージョン:読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)query:(NSMutableDictionary *)argWhereParams :(RequestCompletionHandler)argCompletionHandler;

/* モデルを読み込む(Protected:参照処理の実態) */
- (BOOL)_load:(int)argListed :(NSMutableDictionary *)argSaveParams;
/* モデルを保存する(モデルが継承してオーバーライドする空のメソッド定義) */
- (BOOL)save;
/* モデルを保存する(BlockでHandlerを受け取れるバージョン:読み込み処理をモデル側で実装を変えた場合はこのメソッドをオーバーライドする) */
- (BOOL)save:(RequestCompletionHandler)argCompletionHandler;
/* モデルを保存する(Protected:保存処理の実態) */
- (BOOL)_save:(NSMutableDictionary *)argSaveParams;
/* モデルを保存する(Protected:ファイル添付付き) */
/* XXX 大きいファイルのアップロードには- (BOOL)save:(NSMutableDictionary *)argSaveParams :(NSURL *)argUploadFilePath;を使って下さい！ */
- (BOOL)_save:(NSMutableDictionary *)argSaveParams :(NSData *)argUploadData :(NSString *)argUploadDataName :(NSString *)argUploadDataContentType :(NSString *)argUploadDataKey;
/* ファイルを一つのモデルリソースと見立てて保存する(Protected:ファイルアップロード) */
/* PUTメソッドでのアップロード処理を強制します！ */
- (BOOL)_save:(NSMutableDictionary *)argSaveParams :(NSURL *)argUploadFilePath;

/* 特殊なメソッド1 インクリメント(加算:モデルが継承してオーバーライドする空のメソッド定義) */
- (BOOL)increment;
- (BOOL)_increment:(NSMutableDictionary *)argSaveParams;
/* 特殊なメソッド2 デクリメント(減算:モデルが継承してオーバーライドする空のメソッド定義) */
- (BOOL)decrement;
- (BOOL)_decrement:(NSMutableDictionary *)argSaveParams;

/* 端末固有IDの保存 */
+ (void)saveIdentifier:(NSString *)argIdentifier :(NSString *)argCryptKey :(NSString *)argCryptIV;
/* 端末固有IDの読み込み */
+ (NSString *)loadIdentifier:(NSString *)argCryptKey :(NSString *)argCryptIV;

/* デバイストークンの保存 */
+ (void)saveDeviceTokenString:(NSString *)argDeviceTokenString;
+ (void)saveDeviceTokenData:(NSData *)argDeviceTokenData;
/* デバイストークンの読み込み */
+ (NSString *)loadDeviceToken;

// モデルの配列操作に関する処理
- (BOOL)next;
- (id)objectAtIndex:(int)argIndex;
- (void)insertObject:(ModelBase *)argModel :(int)argIndex;
- (void)addObject:(ModelBase *)argModel;
- (void)removeObjectAtIndex:(int)argIndex;

/* モデルの実態側でメソッドを実装して下さい！ */
- (void)resetReplaceFlagment;
- (NSMutableDictionary *)convertModelData;
/* モデルデータをセットする(モデルが継承してオーバーライドする空のメソッド定義) */
- (void)_setModelData:(NSMutableDictionary *)argDataDic;

// 廃止？？
- (id)search:(NSString *)argSearchKey :(NSString *)argSearchValue;

// ステータスコードに応じたRESTfulエラーメッセージを表示
+(void)showRequestError:(int)argStatusCode;

@end

/* Modelの非同期通信用デレゲート */
/* Modelを非同期で使いたい場合だけ、Delegateに指定して下さい */
@protocol ModelDelegate  <NSObject>

@optional
- (void)didFinishSuccess:(ModelBase*)model :(NSHTTPURLResponse *)responseHeader :(NSString *)responseBody;
- (void)didFinishError:(ModelBase*)model :(NSHTTPURLResponse *)responseHeader :(NSError *)failedHandler;
- (void)didChangeProgress:(ModelBase*)model :(double)packetBytesSent :(double)totalBytesSent :(double)totalBytesExpectedToSend;

@end
