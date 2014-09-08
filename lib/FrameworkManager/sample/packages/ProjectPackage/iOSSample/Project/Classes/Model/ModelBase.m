//
//  ModelBase.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "ModelBase.h"
#import "AES.h"
#import "SecureUDID.h"
// XXX PublicFunctionはこのクラス内では廃止傾向
#import "PublicFunction.h"

@implementation ModelBase

@synthesize sessionDataTask;
@synthesize modelName;
@synthesize ID;
@synthesize index;
@synthesize total;
@synthesize delegate;


#pragma mark - モデルをシングルトンインスタンス化する

+ (id)getInstance;
{
	static id sharedInstance = nil;
	if(!sharedInstance) {
		sharedInstance = [[self alloc] init];
	}
	return sharedInstance;
}


#pragma mark - 初期化処理

- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName;
{
    self = [super init];
    if(nil != self){
        // 初期化処理
        protocol = argProtocol;
        domain = argDomain;
        urlbase = argURLBase;
        timeout = 10;
        cryptKey = nil;
        cryptIV = nil;
        tokenKeyName = argTokenKeyName;
        modelName = nil;
        ID = nil;
        // 自分のリソースを参照する場合に、特別な修飾子が必要なRESTAPI（例えっばFacebook）の場合に利用し、適宜実装クラスで変更を加えて下さい！
        // XXX デフォルトではme/とします。
        myResourcePrefix = @"me/";
        statusCode = 0;
        index = 0;
        total = 0;
        replaced = NO;
        // ハンドラBlockは標準ではnilである！
        completionHandler = nil;
        // delegateは標準ではnilである！
        delegate = nil;
        sessionDataTask = nil;
    }
    return self;
}

- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName :(int)argTimeout;
{
    self = [self init:argProtocol :argDomain :argURLBase :argTokenKeyName];
    if(nil != self){
        timeout = argTimeout;
    }
    return self;
}

- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName :(NSString *)argCryptKey :(NSString *)argCryptIV;
{
    self = [self init:argProtocol :argDomain :argURLBase :argTokenKeyName];
    if(nil != self){
        cryptKey = argCryptKey;
        cryptIV = argCryptIV;
    }
    return self;
}

- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName :(NSString *)argCryptKey :(NSString *)argCryptIV :(int)argTimeout;
{
    self = [self init:argProtocol :argDomain :argURLBase :argTokenKeyName :argTimeout];
    if(nil != self){
        cryptKey = argCryptKey;
        cryptIV = argCryptIV;
    }
    return self;
}


#pragma mark - 通信処理

/* RESTfulURLの生成*/
- (NSString *)createURLString:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argMyResourcePrefix :(NSString *)argModelName :(NSString *)argResourceID;
{
    NSString *url = @"";
    if(nil != argResourceID){
        // 更新(Put)
        url = [NSString stringWithFormat:@"%@://%@%@%@%@/%@.json", argProtocol, argDomain, argURLBase, argMyResourcePrefix, argModelName, argResourceID];
    }
    else{
        // 新規(POST)
        url = [NSString stringWithFormat:@"%@://%@%@%@%@.json", argProtocol, argDomain, argURLBase, argMyResourcePrefix, argModelName];
    }
    return url;
}

/* モデルを参照する */
- (BOOL)load;
{
    if(nil == self.ID){
        // ID無指定は単一モデル参照エラー
        return NO;
    }
    return [self _load:myResource :nil];
}

- (BOOL)load:(RequestCompletionHandler)argCompletionHandler;
{
    if(nil == self.ID){
        // ID無指定は単一モデル参照エラー
        return NO;
    }
    completionHandler = argCompletionHandler;
    return [self _load:myResource :nil];
}

- (BOOL)list;
{
    return [self _load:listedResource :nil];
}

- (BOOL)list:(RequestCompletionHandler)argCompletionHandler;
{
    completionHandler = argCompletionHandler;
    return [self _load:listedResource :nil];
}

- (BOOL)query:(NSMutableDictionary *)argWhereParams;
{
    return [self _load:automaticResource :argWhereParams];
}

- (BOOL)query:(NSMutableDictionary *)argWhereParams :(RequestCompletionHandler)argCompletionHandler;
{
    completionHandler = argCompletionHandler;
    return [self _load:automaticResource :argWhereParams];
}

/* モデルを読み込む */
- (BOOL)_load:(int)argLoadResourceMode :(NSMutableDictionary *)argSaveParams;
{
    // モデルの読み込み(RESTful)
    // 認証を先ずチェック
    if(NO == [self isCertification]){
        // 認証が生きて居ないので、ローカルで認証用のトークンを生成する(登録はREST-APIが勝手にやってくれるバージョンを採用している)
        NSLog(@"no certify");
        NSString *token = [self createToken];
        [Request setCookie:token forKey:tokenKeyName domain:domain];
        [Request saveCookie];
    }
    // 保存モデルのRESTfulURLを作成
    NSString *url = @"";
    // 通信
    statusCode = 0;
    requested = NO;
    if(myResource == argLoadResourceMode){
        // 単一モデル参照
        url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :self.ID];
        NSLog(@"get url=%@", url);
        [Request get:self :url :argSaveParams];
    }
    else if(listedResource == argLoadResourceMode){
        // 配列モデル参照
        url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :nil];
        NSLog(@"get url=%@", url);
        [Request get:self :url :argSaveParams];
    }
    else if(nil != self.ID){
        // 単一モデル参照
        url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :self.ID];
        NSLog(@"get url=%@", url);
        [Request get:self :url :argSaveParams];
    }
    else {
        // 配列モデル参照
        url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :nil];
        NSLog(@"get url=%@", url);
        [Request get:self :url :argSaveParams];
    }

    if(nil != completionHandler || nil != delegate){
        // completionHandlerが指定されているので、通信の終了は待たずに正常終了する
        // delegateが指定されているので、通信の終了は待たずに正常終了する
        return YES;
    }
    
    if(NO == requested){
        // 終了を待つ
        float timecount = 0.0f;
        do {
            // 0.2秒置きに通信の終了をチェック
            [[NSRunLoop currentRunLoop] runUntilDate:[NSDate dateWithTimeIntervalSinceNow:0.2]];
            timecount += 0.2;
            if(timeout <= (int)timecount){
                // タイムアウト
                break;
            }
        } while (!requested);
    }
    
    NSLog(@"end url=%@", url);
    BOOL returned = NO;
    
    if(YES == requested && YES == (statusCode == 200 || statusCode == 201 || statusCode == 202)){
        returned = YES;
    }
    
    return returned;
}

/* モデルを保存する */
/* // XXX 必ずモデル側でオーバーライド実装して下さい！ */
- (BOOL)save;
{
    return NO;
}

-/* モデルを保存する(BlockでHandlerを受け取れるバージョン) */
 (BOOL)save:(RequestCompletionHandler)argCompletionHandler;
{
    completionHandler = argCompletionHandler;
    return [self save];
}

/* モデルを保存する */
- (BOOL)_save:(NSMutableDictionary *)argSaveParams;
{
    // モデルの保存
    // 認証を先ずチェック
    if(NO == [self isCertification]){
        // 認証が生きて居ないので、ローカルで認証用のトークンを生成する(登録はREST-APIが勝手にやってくれるバージョンを採用している)
        NSLog(@"no certify");
        NSString *token = [self createToken];
        [Request setCookie:token forKey:tokenKeyName domain:domain];
        [Request saveCookie];
    }
    // 保存モデルのRESTfulURLを作成
    NSString *url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :self.ID];
    // 通信
    statusCode = 0;
    requested = NO;
    if(nil != self.ID){
        // 更新(Put)
        NSLog(@"put url=%@", url);
        [Request put:self :url :argSaveParams];
    }
    else{
        // 新規(POST)
        NSLog(@"post url=%@", url);
        [Request post:self :url :argSaveParams];
    }

    if(nil != completionHandler || nil != delegate){
        // completionHandlerが指定されているので、通信の終了は待たずに正常終了する
        // delegateが指定されているので、通信の終了は待たずに正常終了する
        return YES;
    }

    NSLog(@"timeout=%d",timeout);
    if(NO == requested){
        // 終了を待つ
        float timecount = 0.0f;
        do {
            // 0.2秒置きに通信の終了をチェック
            [[NSRunLoop currentRunLoop] runUntilDate:[NSDate dateWithTimeIntervalSinceNow:0.2]];
            timecount += 0.2;
            NSLog(@"timecount=%f",timecount);
            if(timeout <= (int)timecount){
                // タイムアウト
                break;
            }
        } while (!requested);
    }

    NSLog(@"end url=%@", url);
    BOOL returned = NO;

    if(YES == requested && YES == (statusCode == 200 || statusCode == 201 || statusCode == 202)){
        returned = YES;
    }
    
    return returned;
}

/* モデルを保存する(ファイルアップロード付き) */
/* XXX 大きいファイルのアップロードには- (BOOL)_save:(NSMutableDictionary *)argSaveParams :(NSURL *)argUploadFilePath;を使って下さい！ */
- (BOOL)_save:(NSMutableDictionary *)argSaveParams :(NSData *)argUploadData :(NSString *)argUploadDataName :(NSString *)argUploadDataContentType :(NSString *)argUploadDataKey;
{
    // モデルの保存
    // 認証を先ずチェック
    if(NO == [self isCertification]){
        // 認証が生きて居ないので、ローカルで認証用のトークンを生成する(登録はREST-APIが勝手にやってくれるバージョンを採用している)
        NSLog(@"no certify");
        NSString *token = [self createToken];
        [Request setCookie:token forKey:tokenKeyName domain:domain];
        [Request saveCookie];
    }
    // 保存モデルのRESTfulURLを作成
    NSString *url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :self.ID];
    // 通信
    statusCode = 0;
    requested = NO;
    if(nil != self.ID){
        // 更新(Put)
        NSLog(@"put url=%@", url);
        [Request put:self :url :argSaveParams :argUploadData :argUploadDataName :argUploadDataContentType :argUploadDataKey];
    }
    else{
        // 新規(POST)
        NSLog(@"post url=%@", url);
        [Request post:self :url :argSaveParams :argUploadData :argUploadDataName :argUploadDataContentType :argUploadDataKey];
    }

    if(nil != completionHandler || nil != delegate){
        // completionHandlerが指定されているので、通信の終了は待たずに正常終了する
        // delegateが指定されているので、通信の終了は待たずに正常終了する
        return YES;
    }
    
    if(NO == requested){
        // 終了を待つ
        float timecount = 0.0f;
        do {
            // 0.2秒置きに通信の終了をチェック
            [[NSRunLoop currentRunLoop] runUntilDate:[NSDate dateWithTimeIntervalSinceNow:0.2]];
            timecount += 0.2;
            if(timeout <= (int)timecount){
                // タイムアウト
                break;
            }
        } while (!requested);
    }
    
    NSLog(@"end url=%@", url);
    BOOL returned = NO;
    
    if(YES == requested && YES == (statusCode == 200 || statusCode == 201)){
        returned = YES;
    }

    return returned;
}

/* ファイルを一つのモデルリソースと見立てて保存(アップロード)する */
/* PUTメソッドでのアップロード処理を強制します！ */
- (BOOL)_save:(NSMutableDictionary *)argSaveParams :(NSURL *)argUploadFilePath;
{
    // モデルの保存
    // 認証を先ずチェック
    if(NO == [self isCertification]){
        // 認証が生きて居ないので、ローカルで認証用のトークンを生成する(登録はREST-APIが勝手にやってくれるバージョンを採用している)
        NSLog(@"no certify");
        NSString *token = [self createToken];
        [Request setCookie:token forKey:tokenKeyName domain:domain];
        [Request saveCookie];
    }
    // 保存モデルのRESTfulURLを作成
    NSString *url = [self createURLString:protocol :domain :urlbase :myResourcePrefix :self.modelName :self.ID];
    // 通信
    statusCode = 0;
    requested = NO;
    if(nil != self.ID){
        // 新規 or 更新(Put)
        NSLog(@"put url=%@", url);
        [Request put:self :url :argSaveParams :argUploadFilePath];
    }
    else{
        // XXX ID無しのファイルアップロードは出来ない！
        return NO;
    }

    if(nil != completionHandler || nil != delegate){
        // completionHandlerが指定されているので、通信の終了は待たずに正常終了する
        // delegateが指定されているので、通信の終了は待たずに正常終了する
        return YES;
    }

    if(NO == requested){
        NSLog(@"timeout=%d",timeout);
        // 終了を待つ
        float timecount = 0.0f;
        do {
            // 0.2秒置きに通信の終了をチェック
            [[NSRunLoop currentRunLoop] runUntilDate:[NSDate dateWithTimeIntervalSinceNow:0.2]];
            timecount += 0.2;
            if(timeout <= (int)timecount){
                // タイムアウト
                break;
            }
        } while (!requested);
    }

    NSLog(@"end url=%@", url);
    BOOL returned = NO;

    if(YES == requested && YES == (statusCode == 200 || statusCode == 201 || statusCode == 202)){
        returned = YES;
    }

    return returned;
}

/* 特殊なメソッド1 インクリメント(加算) */
- (BOOL)increment;
{
    return YES;
}
- (BOOL)_increment:(NSMutableDictionary *)argSaveParams;
{
    if(nil != self.ID){
        return [self _save:argSaveParams];
    }
    // インクリメントはID指定ナシはエラー！
    return NO;
}

/* 特殊なメソッド2 デクリメント(減算) */
- (BOOL)decrement;
{
    return YES;
}

- (BOOL)_decrement:(NSMutableDictionary *)argSaveParams;
{
    if(nil != self.ID){
        return [self _save:argSaveParams];
    }
    // インクリメントはID指定ナシはエラー！
    return NO;
}


#pragma mark - 認証関連

/* ログイン認証チェック */
- (BOOL)isCertification;
{
    BOOL expired = NO;
    NSHTTPCookieStorage *aStorage = [NSHTTPCookieStorage sharedHTTPCookieStorage];
    NSArray *cookies = [aStorage cookies];
    
    for (NSHTTPCookie *aCookie in cookies) {
        NSDictionary *prop = [aCookie properties];
        NSLog(@"cookie=%@", [prop description]);
        NSString *cookieDomain = [prop objectForKey:NSHTTPCookieDomain];
        NSString *cookieName = [prop objectForKey:NSHTTPCookieName];
        NSLog(@"cookie=%@&%@", cookieDomain, domain);
        if (cookieDomain && ([cookieDomain isEqualToString:domain] || [cookieDomain isEqualToString:[NSString stringWithFormat:@".%@", domain]])) {
            if(cookieName && [cookieName isEqualToString:tokenKeyName]){
                NSLog(@"cookie=%@", [aCookie description]);
                NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
                [dateFormatter setTimeStyle:NSDateFormatterFullStyle];
                [dateFormatter setDateFormat:@"yyyy-MM-dd HH:mm:ss zzz"];
                NSDate *cookieExpired = [dateFormatter dateFromString:[[prop objectForKey:NSHTTPCookieExpires] description]];
                NSLog(@"cookieExpired=%@", cookieExpired);
                [dateFormatter setTimeZone:[NSTimeZone timeZoneWithAbbreviation:@"GMT"]];
                NSDate *now = [dateFormatter dateFromString:[dateFormatter stringFromDate:[NSDate date]]];
                NSLog(@"now=%@", now);
                NSComparisonResult result = [cookieExpired compare:now];
                if(result != NSOrderedAscending){
                    // 有効期限内
                    NSLog(@"Expire!");
                    expired = YES;
                }
                else{
                    // 要らないCookieは消す
                    [prop setValue:[NSDate dateWithTimeIntervalSinceNow:-3600] forKey:NSHTTPCookieExpires];
                    NSHTTPCookie *newCookie = [[NSHTTPCookie alloc] initWithProperties:prop];
                    [aStorage setCookie:newCookie];
                    [aStorage deleteCookie:aCookie];
                }
            }
        }
    }
    
    return expired;
}

- (NSString *)createToken;
{
    // UUIDを使ってOnetimeトークンを発行
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setLocale:[NSLocale systemLocale]];
    [dateFormatter setTimeZone:[NSTimeZone timeZoneWithAbbreviation:@"GMT"]];
    [dateFormatter setDateFormat:@"yyyyMMddHHmmss"];
    // 和暦回避
    NSCalendar *calendar = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    [dateFormatter setCalendar:calendar];
    NSString *gmtdatetime = [dateFormatter stringFromDate:[NSDate date]];
    NSLog(@"gmtdatetime=%@", gmtdatetime);
    
    NSString *identifier = [SecureUDID UDIDForDomain:domain usingKey:cryptKey];
    [ModelBase saveIdentifier:identifier :cryptKey :cryptIV];
    NSLog(@"identifier=%@", identifier);
    NSLog(@"plainToken=%@", [NSString stringWithFormat:@"%@%@", identifier, gmtdatetime]);
    // 固有識別子を元にトークンを作る
    NSString *token = [NSString stringWithFormat:@"%@%@", [AES encryptHex:[NSString stringWithFormat:@"%@%@", [AES encryptHex:identifier :cryptKey :cryptIV], gmtdatetime] :cryptKey :cryptIV], gmtdatetime];
    NSLog(@"token=%@", token);
    
    // Cookie内のtokenを一旦クリア
    NSHTTPCookieStorage *storage = [NSHTTPCookieStorage sharedHTTPCookieStorage];
    // Cookie処理ループ
    for(NSHTTPCookie *cookie in [storage cookies]){
        NSDictionary *cookieProperty = [cookie properties];
        if([[cookieProperty objectForKey:NSHTTPCookieDomain] isEqualToString:domain] || [[cookieProperty objectForKey:NSHTTPCookieDomain] isEqualToString:[NSString stringWithFormat:@".%@", domain]]){
            NSLog(@"cookie=%@", [cookie description]);
            [storage deleteCookie:cookie];
        }
    }
    return token;
}


+ (void)saveIdentifier:(NSString *)argIdentifier :(NSString *)argCryptKey :(NSString *)argCryptIV;
{
    if(nil == [ModelBase loadIdentifier:argCryptKey :argCryptIV]){
        [[NSUserDefaults standardUserDefaults] setObject:[AES encryptHex:argIdentifier :argCryptKey :argCryptIV]
                                                  forKey:@"identifier"];
    }
}

+ (NSString *)loadIdentifier:(NSString *)argCryptKey :(NSString *)argCryptIV;
{
    NSString *identifier = [[NSUserDefaults standardUserDefaults] objectForKey:@"identifier"];
    if(nil != identifier){
        return [AES decryptHex:identifier :argCryptKey :argCryptIV];
    }
    return nil;
}


#pragma mark - デバイストークン関連

/* デバイストークンの保存 */
+ (void)saveDeviceTokenString:(NSString *)argDeviceToken;
{
    [[NSUserDefaults standardUserDefaults] setObject:argDeviceToken forKey:@"devicetoken"];
}
+ (void)saveDeviceTokenData:(NSData *)argDeviceTokenData;
{
    NSString *deviceToken = [[[[argDeviceTokenData description] stringByReplacingOccurrencesOfString:@"<"withString:@""]
							  stringByReplacingOccurrencesOfString:@">" withString:@""]
							 stringByReplacingOccurrencesOfString: @" " withString: @""];
    NSLog(@"deviceToken: %@", deviceToken);
    [[self class] saveDeviceTokenString:deviceToken];
}

/* デバイストークンの読み込み */
+ (NSString *)loadDeviceToken;
{
    return [[NSUserDefaults standardUserDefaults] objectForKey:@"devicetoken"];
}


#pragma mark - 配列モデルの各種操作

- (BOOL)next;
{
    if(self.index < self.total){
        self.index++;
        [self setModelData:list :self.index];
        return YES;
    }
    return NO;
}

- (id)objectAtIndex:(int)argIndex;
{
    if(0 < self.total && argIndex <= self.total){
        id nextModel = [[[self class] alloc] init:protocol :domain :urlbase  :tokenKeyName :cryptKey :cryptIV :timeout];
        [nextModel setModelData:list :argIndex];
        return nextModel;
    }
    return nil;
}

- (void)insertObject:(ModelBase *)argModel :(int)argIndex;
{
    NSMutableArray *newList = [list mutableCopy];
    [newList insertObject:[[argModel convertModelData] mutableCopy] atIndex:argIndex];
    list = newList;
    self.total = (int)[list count];
}

- (void)addObject:(ModelBase *)argModel;
{
    NSMutableArray *newList = [list mutableCopy];
    [newList addObject:[[argModel convertModelData] mutableCopy]];
    list = newList;
    self.total = (int)[list count];
}

- (void)removeObjectAtIndex:(int)argIndex
{
    NSMutableArray *newList = [list mutableCopy];
    [newList removeObjectAtIndex:argIndex];
    list = newList;
    self.total = (int)[list count];
}

/* 廃止? */
- (id)search:(NSString *)argSearchKey :(NSString *)argSearchValue;
{
    NSPredicate *patternMatchFilter = [NSPredicate predicateWithBlock:^BOOL(id obj, NSDictionary *d){
        NSDictionary *data = obj;
        if (![[data allKeys] containsObject:argSearchKey] || [[data objectForKey:argSearchKey] isEqual:[NSNull null]]) {
            return NO;
        }
        NSRange range = [[data objectForKey:argSearchKey] rangeOfString:argSearchValue];
        return (range.location != NSNotFound);
    }];
    NSMutableArray *seachList = [[list filteredArrayUsingPredicate:patternMatchFilter] mutableCopy];
    id searchResultModel = [[[self class] alloc] init:protocol :domain :urlbase :tokenKeyName :cryptKey :cryptIV :timeout];
    [searchResultModel setModelData:seachList];
    return searchResultModel;
}

/* モデル側で必ず実装して下さい！ */
- (NSMutableDictionary *)convertModelData;
{
    return nil;
}

- (void)setModelData:(NSMutableArray *)argDataArray;
{
    response = nil;
    list = [argDataArray mutableCopy];
    self.total = (int)[list count];
    if(0 < [list count]){
        response = [list objectAtIndex:0];
        [self _setModelData:response];
    }
}

- (void)setModelData:(NSMutableArray *)argDataArray :(int)argIndex;
{
    response = nil;
    list = [argDataArray mutableCopy];
    self.total = (int)[list count];
    if(0 < [list count]){
        response = [list objectAtIndex:argIndex];
        self.index = argIndex;
        [self _setModelData:response];
    }
}

- (void)_setModelData:(NSMutableDictionary *)argDataDic;
{
}

/* モデル側で必ず実装して下さい！ */
- (void)resetReplaceFlagment;
{
    return;
}


#pragma mark - スタティックメソッド系

+(void)showRequestError:(int)argStatusCode;
{
    NSString *errorMsg = @"通信がタイムアウトしました。\n\n電波状況の良い所で再度実行してみて下さい。";
    if(0 < argStatusCode){
        errorMsg = @"ご迷惑をお掛けします。\n\nサーバーが致命的なエラーを発生させました。\n最初からやり直すか、それでも改善しない場合はシステム管理会社に問い合わせをして下さい。";
        if(400 == argStatusCode){
            errorMsg = @"エラーコード400\n\nデータの入力にあやまりがあるか\nサーバー側の問題により、処理を正常に受付出来ませんでした。\n最初からやり直すか、それでも改善しない場合はシステム管理会社に問い合わせをして下さい。";
        }
        if(401 == argStatusCode){
            errorMsg = @"エラーコード401\n\n何らかの理由により、認証に失敗しました。\n最初からやり直すか、それでも改善しない場合はシステム管理会社に問い合わせをして下さい。";
        }
        if(404 == argStatusCode){
            errorMsg = @"エラーコード404\n\n要求したデータが既に存在しませんでした。\n最初からやり直すか、それでも改善しない場合はシステム管理会社に問い合わせをして下さい。";
        }
        if(503 == argStatusCode){
            errorMsg = @"エラーコード503\n\nご迷惑をお掛けします。\nサーバーが現在メンテナンス中です。\nしばらく経ってから再度実行して下さい。";
        }
    }
    [PublicFunction alertShow:nil message:errorMsg];
}


#pragma mark - RequestのDelegate関連

// RequestクラスのDelegateメソッド
- (void)didFinishSuccess:(NSHTTPURLResponse *)responseHeader :(NSString *)responseBody;
{
    NSLog(@"responseBody=%@", responseBody);
    BOOL success = NO;
    statusCode = 500;
    if(nil != responseBody && 0 < [responseBody length]){
        // 通信結果を格納
        statusCode = (int)[responseHeader statusCode];
        // jsonをパース(HBOPのRESTリソースモデルのJSON形式に準拠)
        [self setModelData:[PublicFunction parseArr:responseBody]];
        // delegateを呼んで上げる
        if(nil != delegate && [delegate respondsToSelector:@selector(didFinishSuccess:::)]){
            [delegate didFinishSuccess:self :responseHeader :responseBody];
        }
        // ハンドラの実行
        if (nil != completionHandler){
            if(200 == statusCode || 201 == statusCode || 202 == statusCode){
                success = YES;
            }
            completionHandler(success, statusCode, responseHeader, nil, nil);
        }
        // 通信終了
        requested = YES;
        // 正常通信だった場合はココで処理終了！
        return;
    }
    // 通信終了(異常)
    requested = YES;
    [ModelBase showRequestError:statusCode];
    // ハンドラの実行
    if (nil != completionHandler){
        completionHandler(success, statusCode, responseHeader, nil, nil);
    }
}

- (void)didFinishError:(NSHTTPURLResponse *)responseHeader :(NSError *)failedHandler;
{
    if(nil != delegate && [delegate respondsToSelector:@selector(didFinishError:::)]){
        // delegateを呼んで上げる
        [delegate didFinishError:self :responseHeader :failedHandler];
        // delegate指定の場合はココで終了
        return;
    }
    else{
        if(nil != responseHeader){
            // RESTfulな自動的なエラーメッセージハンドリング
            statusCode = (int)responseHeader.statusCode;
        }
        // 通信終了
        requested = YES;
        [ModelBase showRequestError:statusCode];
    }
    // ハンドラの実行
    if (nil != completionHandler){
        completionHandler(NO, statusCode, responseHeader, nil, failedHandler);
    }
}

- (void)didChangeProgress:(double)packetBytesSent :(double)totalBytesSent :(double)totalBytesExpectedToSend;
{
    NSLog(@"[bytesSent] %f, [totalBytesSent] %f, [totalBytesExpectedToSend] %f", packetBytesSent, totalBytesSent, totalBytesExpectedToSend);
    double progress = (double)totalBytesSent / (double)totalBytesExpectedToSend;
    NSLog(@"[progress] %f％", progress * 100);
    if(nil != delegate && [delegate respondsToSelector:@selector(didChangeProgress::::)]){
        // delegateを呼んで上げる
        [delegate didChangeProgress:self :packetBytesSent :totalBytesSent :totalBytesExpectedToSend];
    }
}

@end
