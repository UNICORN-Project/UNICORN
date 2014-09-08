//
//  Request.m
//
//  XXX R9HTTPRequestExtensionはHTTRequestヘッダーを自力で作るのが面倒だったのでヘッダー生成の為だけに使っている
//
//  Created by saimushi on 14/06/16.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "Request.h"
#import "R9HTTPRequestExtension.h"

#define DEFAULT_TIMEOUT 10
#define DEFAULT_COOKIE_EXPIRED @"3600"

@implementation Request
{
    NSURLSessionTask* sessionTask;
    BOOL requestSuccessed;
    BOOL uploadTask;
    int statusCode;
    NSHTTPURLResponse *responseHeader;
    NSString *responseBody;
}

@synthesize delegate;
@synthesize userAgent;


#pragma mark - リクエスト処理の実態関連

/**
 * 通常のリクエスト処理
 * NSMutableDictionary内のvalueはstringのみ
 */
- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams
{
    // GETの時はURLにパラメータを付加する！(ので、先に処理する！)
    if(YES == [method isEqualToString:@"GET"] && nil != requestParams && 0 < requestParams.count){
        requestURL = [NSString stringWithFormat:@"%@?", requestURL];
        NSArray *keys = [requestParams allKeys];
        for (int i = 0; i < [keys count]; i++) {
            NSString *encodingGetParam = ((NSString*)CFBridgingRelease(CFURLCreateStringByAddingPercentEscapes(kCFAllocatorDefault,
                                                                                                               (CFStringRef)[requestParams objectForKey:[keys objectAtIndex:i]],
                                                                                                               NULL,
                                                                                                               (CFStringRef)@"!*'();:@&=+$,/?%#[]",
                                                                                                               kCFStringEncodingUTF8)));
            requestURL = [NSString stringWithFormat:@"%@%@=%@&", requestURL, [keys objectAtIndex:i], encodingGetParam];
        }
        NSLog(@"GET URL=%@", requestURL);
    }

    NSURL *URL = [NSURL URLWithString:requestURL];
    
    // 最新のCookieを常に使う
    [Request loadCookie];

    // Requestインスタンスの生成
    R9HTTPRequestExtension *request = [[R9HTTPRequestExtension alloc] initWithURL:URL];
    [request setHTTPMethod:method];
    [request setTimeoutInterval:DEFAULT_TIMEOUT];
    [request addHeader:[Request createUserAgent] forKey:@"User-Agent"];

    // パラメータのセット
    if(NO == [method isEqualToString:@"GET"] && nil != requestParams){
        NSArray *keys = [requestParams allKeys];
        for (int i = 0; i < [keys count]; i++) {
            [request addBody:[requestParams objectForKey:[keys objectAtIndex:i]] forKey:[keys objectAtIndex:i]];
        }
    }

    // 通信キューを追加して実行
    requestSuccessed = NO;
    uploadTask = NO;
    statusCode = 0;
    responseHeader = nil;
    responseBody = nil;
    NSURLSessionConfiguration* config = [NSURLSessionConfiguration defaultSessionConfiguration];
    NSURLSession* session = [NSURLSession sessionWithConfiguration:config
                                                          delegate:self
                                                     delegateQueue:[NSOperationQueue mainQueue]];
    sessionTask = [session dataTaskWithRequest:[request getRequest]];
// XXX TODO:バックグラウンドセッション&レジューム次のタイミングでは入れたいと思います・・・orz
//    NSURLSessionConfiguration* config = [NSURLSessionConfiguration backgroundSessionConfiguration:requestURL];
//    NSURLSession* session = [NSURLSession sessionWithConfiguration:config
//                                                          delegate:self
//                                                     delegateQueue:[NSOperationQueue mainQueue]];
//    sessionTask = [session downloadTaskWithRequest:[request getRequest]];
    if([delegate respondsToSelector:@selector(setSessionDataTask:)]){
        [delegate setSessionDataTask:sessionTask];
    }
    [sessionTask resume];
}

/**
 * 通常のリクエスト処理に加えて、マルチパートでファイルをPOST(PUT)する
 * XXX 大きいファイルのアップロードは次のメソッド- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;を使ってアップロードタスクで実行して下さい！
 */
- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams :(NSData *)uploadData :(NSString *)fileName :(NSString *)contentType :(NSString *)dataKeyName;
{
    NSURL *URL = [NSURL URLWithString:requestURL];

    // 最新のCookieを常に使う
    [Request loadCookie];

    // Requestインスタンスの生成
    R9HTTPRequestExtension *request = [[R9HTTPRequestExtension alloc] initWithURL:URL];
    [request setHTTPMethod:method];
    [request setTimeoutInterval:DEFAULT_TIMEOUT];
    [request addHeader:[Request createUserAgent] forKey:@"User-Agent"];
    
    // パラメータのセット
    if(nil != requestParams){
        NSArray *keys = [requestParams allKeys];
        for (int i = 0; i < [keys count]; i++) {
            [request addBody:[requestParams objectForKey:[keys objectAtIndex:i]] forKey:[keys objectAtIndex:i]];
        }
    }
    
    // イメージをセット
    if(nil != uploadData){
        [request setData:uploadData withFileName:fileName andContentType:contentType forKey:dataKeyName];
    }

    // 通信キューを追加して実行
    requestSuccessed = NO;
    uploadTask = NO;
    statusCode = 0;
    responseHeader = nil;
    responseBody = nil;
    NSURLSessionConfiguration* config = [NSURLSessionConfiguration defaultSessionConfiguration];
    NSURLSession* session = [NSURLSession sessionWithConfiguration:config
                                                          delegate:self
                                                     delegateQueue:[NSOperationQueue mainQueue]];
    sessionTask = [session dataTaskWithRequest:[request getRequest]];
// XXX TODO:バックグラウンドセッション&レジューム次のタイミングでは入れたいと思います・・・orz
//    NSURLSessionConfiguration* config = [NSURLSessionConfiguration backgroundSessionConfiguration:requestURL];
//    NSURLSession* session = [NSURLSession sessionWithConfiguration:config
//                                                          delegate:self
//                                                     delegateQueue:[NSOperationQueue mainQueue]];
//    sessionTask = [session downloadTaskWithRequest:[request getRequest]];
    if([delegate respondsToSelector:@selector(setSessionDataTask:)]){
        [delegate setSessionDataTask:sessionTask];
    }
    [sessionTask resume];
}

/**
 * ファイルをアップロードする
 * XXX PUTメソッドでアップロードした場合、サーバー側でファイル以外に送信されたPOSTデータを判別出来なくなる事に注意して下さい！
 */
- (void)start:(NSString *)requestURL :(NSString *)method :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;
{
    NSURL *URL = [NSURL URLWithString:requestURL];
    
    // 最新のCookieを常に使う
    [Request loadCookie];

    // Requestインスタンスの生成
    R9HTTPRequestExtension *request = [[R9HTTPRequestExtension alloc] initWithURL:URL];
    [request setHTTPMethod:method];
    [request setTimeoutInterval:DEFAULT_TIMEOUT];
    [request addHeader:[Request createUserAgent] forKey:@"User-Agent"];
    
    // パラメータのセット
    if(nil != requestParams){
        NSArray *keys = [requestParams allKeys];
        for (int i = 0; i < [keys count]; i++) {
            [request addBody:[requestParams objectForKey:[keys objectAtIndex:i]] forKey:[keys objectAtIndex:i]];
        }
    }

    // 通信キューを追加して実行
    requestSuccessed = NO;
    uploadTask = YES;
    statusCode = 0;
    responseHeader = nil;
    responseBody = nil;
    NSURLSessionConfiguration* config = [NSURLSessionConfiguration backgroundSessionConfiguration:requestURL];
    NSURLSession* session = [NSURLSession sessionWithConfiguration:config
                                                          delegate:self
                                                     delegateQueue:[NSOperationQueue mainQueue]];
    sessionTask = [session uploadTaskWithRequest:[request getRequest] fromFile:uploadFilePath];
    if([delegate respondsToSelector:@selector(setSessionDataTask:)]){
        [delegate setSessionDataTask:sessionTask];
    }
    [sessionTask resume];
}


#pragma mark - ユーザーエージェン関連

/**
 * 通信に利用するUserAgentをアプリ名+アプリビルド番号+SafariUAに変更する
 */
+ (NSString *)createUserAgent;
{
    static NSString *userAgent = nil;
    if(nil == userAgent){
        UIWebView *webView = [[UIWebView alloc] initWithFrame:CGRectZero];
        userAgent = [webView stringByEvaluatingJavaScriptFromString:@"navigator.userAgent"];
        NSString *appName = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleName"];
        NSString *buildVersion = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleVersion"];
        NSLog(@"%@=%@", appName, buildVersion);
        userAgent = [userAgent stringByReplacingOccurrencesOfString:@"Mozilla" withString:[NSString stringWithFormat:@"%@/%@ Mozilla", appName, buildVersion]];
        NSLog(@"userAgent >>> %@", userAgent);
    }
    return userAgent;
}


#pragma mark - リクエストのパブリックアクセサ関連

/**
 * GETメソッドでリクエスト(パラメータの内URLリクエスト)
 */
+ (void)get:(id)calledClass :(NSString *)requestURL;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"GET" :nil];
}

/**
 * GETメソッドでリクエスト
 */
+ (void)get:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"GET" :requestParams];
}

/**
 * POSTメソッドでリクエスト
 */
+ (void)post:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"POST" :requestParams];
}

/**
 * POSTメソッドでリクエスト (ファイルあり)
 */
+ (void)post:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSData *)uploadData :(NSString *)fileName :(NSString *)contentType :(NSString *)dataKeyName;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"POST" :requestParams :uploadData :fileName :contentType :dataKeyName];
}

/**
 * POSTメソッドでファイルをアップロード
 */
+ (void)post:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"POST" :requestParams :uploadFilePath];
}

/**
 * PUTメソッドでリクエスト
 */
+ (void)put:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"PUT" :requestParams];
}

/**
 * PUTメソッドでリクエスト (ファイルあり)
 */
+ (void)put:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSData *)uploadData :(NSString *)fileName :(NSString *)contentType :(NSString *)dataKeyName;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"PUT" :requestParams :uploadData :fileName :contentType :dataKeyName];
}

/**
 * PUTメソッドでファイルをアップロード
 */
+ (void)put:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams :(NSURL *)uploadFilePath;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"PUT" :requestParams :uploadFilePath];
}

/**
 * DELETEメソッドでリクエスト
 */
+ (void)delete:(id)calledClass :(NSString *)requestURL :(NSMutableDictionary *)requestParams;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"DELETE" :requestParams];
}

/**
 * HEADメソッドでリクエスト
 */
+ (void)head:(id)calledClass :(NSString *)requestURL;
{
    Request *request = [[Request alloc] init];
    request.delegate = calledClass;
    [request start:requestURL :@"HEAD" :nil];
}


#pragma mark - Cookie関連

//指定パラメータのクッキーをセット(簡易版)
//value: クッキーの値
//key: クッキーのキー名
//domain: クッキーを適用するドメイン(xxxx.ne.jp)
//path: クッキーの有効適用範囲(サーバ上のパス)
//expires: クッキーの有効期限(0は無限)
+ (void)setCookie:(NSString *)value forKey:(NSString *)key domain:(NSString *)domain
{
    //クッキーを作成
    NSDictionary *properties = [[NSMutableDictionary alloc] init];
    [properties setValue:[value stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding]
                  forKey:NSHTTPCookieValue];
    [properties setValue:key forKey:NSHTTPCookieName];
    [properties setValue:domain forKey:NSHTTPCookieDomain];
    [properties setValue:DEFAULT_COOKIE_EXPIRED forKey:NSHTTPCookieExpires];
    [properties setValue:@"/" forKey:NSHTTPCookiePath];
    NSHTTPCookie *cookie = [[NSHTTPCookie alloc] initWithProperties:properties];
    
    //共通クッキーストレージを取得してセット
    NSHTTPCookieStorage *aStorage = [NSHTTPCookieStorage sharedHTTPCookieStorage];
    [aStorage setCookie:cookie];
}

//value: クッキーの値
//key: クッキーのキー名
//domain: クッキーを適用するドメイン(xxxx.ne.jp)
//path: クッキーの有効適用範囲(サーバ上のパス)
//expires: クッキーの有効期限(0は無限)
+ (void)setCookie:(NSString *)value forKey:(NSString *)key domain:(NSString *)domain cookiePath:(NSString *)path expires:(NSString *)expires
{
    //クッキーを作成
    NSDictionary *properties = [[NSMutableDictionary alloc] init];
    [properties setValue:[value stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding]
                  forKey:NSHTTPCookieValue];
    [properties setValue:key forKey:NSHTTPCookieName];
    [properties setValue:domain forKey:NSHTTPCookieDomain];
    [properties setValue:expires forKey:NSHTTPCookieExpires];
    [properties setValue:path forKey:NSHTTPCookiePath];
    NSHTTPCookie *cookie = [[NSHTTPCookie alloc] initWithProperties:properties];
    
    //共通クッキーストレージを取得してセット
    NSHTTPCookieStorage *aStorage = [NSHTTPCookieStorage sharedHTTPCookieStorage];
    [aStorage setCookie:cookie];
}

/* CookieをNSUserDefaultsから読み取ってsharedHTTPCookieStorageに放り込む */
+ (void)loadCookie
{
    static bool loaded = NO;
    if(NO == loaded){
        NSData *cookiesData = [[NSUserDefaults standardUserDefaults]
                               objectForKey:@"SavedHTTPCookiesKey"];
        NSLog(@"cookiesData=%@", [cookiesData description]);
        if (cookiesData) {
            NSLog(@"load cookies");
            NSArray *cookies = [NSKeyedUnarchiver unarchiveObjectWithData:cookiesData];
            NSLog(@"cookies=%@", [cookies description]);
            for (NSHTTPCookie *cookie in cookies) {
                [[NSHTTPCookieStorage sharedHTTPCookieStorage] setCookie:cookie];
            }
            loaded = YES;
        }
    }
}

/* CookieをsharedHTTPCookieStorageから読み取ってNSUserDefaultsに放り込む */
+ (void)saveCookie
{
    NSData *cookiesData = [NSKeyedArchiver archivedDataWithRootObject:
                           [[NSHTTPCookieStorage sharedHTTPCookieStorage] cookies]];
    NSLog(@"cookiesData=%@", [cookiesData description]);
    [[NSUserDefaults standardUserDefaults] setObject:cookiesData
                                              forKey:@"SavedHTTPCookiesKey"];
}


#pragma mark - NSURLSessionDataDelegate関連

/* レスポンスヘッダー取得デレゲート(通信の実質的な終了) */
- (void)URLSession:(NSURLSession *)session task:(NSURLSessionTask *)task didCompleteWithError:(NSError *)error
{
    // セッションを必要としなくなった場合，未処理のタスクをキャンセルするために invalidateAndCancel を呼ぶことでセッションを無効とします．
    [session invalidateAndCancel];
    sessionTask = nil;
    // デレゲートにとっておいていたsessionTaskを無効にしておいて上げる
    if([delegate respondsToSelector:@selector(setSessionDataTask:)]){
        [delegate setSessionDataTask:nil];
    }

    BOOL success = NO;

    // エラーオブジェクトがあるかどうか確認します．
    if(!error){
        // HTTP レスポンスかどうか確認してみます．
        if([task.response isKindOfClass:[NSHTTPURLResponse class]]){
            // 通信成功(通信ステータスとは別！)
            requestSuccessed = YES;
            // レスポンスヘッダーをキャストしてとっておく
            responseHeader = (NSHTTPURLResponse *)task.response;
            // ステータスコードをとっておく
            statusCode = (int)responseHeader.statusCode;
            NSLog(@"[statusCode] %d", statusCode);
            // HTTP 200 OK の場合
            // HTTP 201 Upload Success の場合
            if(statusCode == 200 || statusCode == 201 || statusCode == 202){
                // 最新のCookieを常に保存しておく
                [Request saveCookie];
                // ステータスコード200で成功
                if ([delegate respondsToSelector:@selector(didFinishSuccess::)]) {
                    [delegate didFinishSuccess:responseHeader :responseBody];
                }
                // 正常終了
                success = YES;
            }
        }
    }

    if(NO == success){
        // 通信エラー
        // 全ての通信で異常が発生
        // 例）DNS 名前解決に失敗 リクエストタイム レスポンスステータスコードが200と201以外が返された
        NSLog(@"%@", [error localizedDescription]);
        if ([delegate respondsToSelector:@selector(didFinishError::)]) {
            [delegate didFinishError:responseHeader :error];
        }
    }
}

// レスポンスデータの取得デレゲート
// Tells the delegate that the data task finished receiving all of the expected data.
- (void)URLSession:(NSURLSession *)session dataTask:(NSURLSessionDataTask *)dataTask didReceiveData:(NSData *)data
{
    // (!!!) didReceiveDataは分割して呼ばれる事があると言う罠があります！
    // 呼ばれ続ける限りresponseBodyにreceiveDataを連結するようになっています。
    // レスポンスボディの初期化をチェックします
    if(nil == responseBody){
        // レスポンスボディの初期化をします
        responseBody = @"";
    }
    // レスポンスボディの有無を確認してみます
    if(data){
        // レスポンスボディにreceiveDataを連結します
        NSUInteger lengthData = [data length];
        NSLog(@"responseData %@", [data description]);
        NSLog(@"[lengthData] %d", (int)lengthData);
        responseBody = [NSString stringWithFormat:@"%@%@", responseBody, [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding]];
        NSLog(@"[responseString length] %d", (int)[responseBody length]);
        NSLog(@"responseString %@", responseBody);
    }
}


#pragma mark - NSURLSessionDataDelegate プログレス関連

// アップロード進捗の取得
// URLSession:task:didSendBodyData:totalBytesSent:totalBytesExpectedToSend:
// Periodically informs the delegate of the progress of sending body content to the server.
- (void)URLSession:(NSURLSession *)session task:(NSURLSessionTask *)task didSendBodyData:(int64_t)bytesSent totalBytesSent:(int64_t)totalBytesSent totalBytesExpectedToSend:(int64_t)totalBytesExpectedToSend
{
    // bytesSent = 1パケットの送信量
    // totalBytesSent = 送信総量
    // totalBytesExpectedToSend = アップロードデータ量
    NSLog(@"[bytesSent] %lld, [totalBytesSent] %lld, [totalBytesExpectedToSend] %lld", bytesSent, totalBytesSent, totalBytesExpectedToSend);
    double progress = (double)totalBytesSent / (double)totalBytesExpectedToSend;
    NSLog(@"[progress] %f", progress);
    // 呼び元に通信状況を返して上げる
    if([delegate respondsToSelector:@selector(didChangeProgress:::)]){
        [delegate didChangeProgress:(double)bytesSent :(double)totalBytesSent :(double)totalBytesExpectedToSend];
    }
}

@end
