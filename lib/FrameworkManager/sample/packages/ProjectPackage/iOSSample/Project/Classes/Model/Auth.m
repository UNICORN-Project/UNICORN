//
//  Auth.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "Auth.h"

@implementation Auth

@synthesize deviceToken;
@synthesize userID;

+ (NSString *)getDeviceToken;
{
    Auth *auth = [Auth getInstance];
    return auth.deviceToken;
}

+ (NSString *)deviceToken;
{
    return [self getDeviceToken];
}

+ (NSString *)getUserID;
{
    Auth *auth = [Auth getInstance];
    return auth.userID;
}

+ (NSString *)userID;
{
    return [self getUserID];
}

+ (void)setDeviceToken:(NSString *)argDeviceToken;
{
    Auth *auth = [Auth getInstance];
    auth.deviceToken = argDeviceToken;
}

+ (void)setUserID:(NSString *)argUserID;
{
    Auth *auth = [Auth getInstance];
    auth.userID = argUserID;
}

+ (BOOL)isLogin;
{
    if(!([[Auth getUserID] isKindOfClass:NSClassFromString(@"NSString")] && [[Auth getUserID] length] > 0)){
        return NO;
    }

    BOOL expired = NO;
    NSHTTPCookieStorage *aStorage = [NSHTTPCookieStorage sharedHTTPCookieStorage];
    NSArray *cookies = [aStorage cookies];

    for (NSHTTPCookie *aCookie in cookies) {
        NSDictionary *prop = [aCookie properties];
        NSLog(@"cookie=%@", [prop description]);
        NSString *cookieDomain = [prop objectForKey:NSHTTPCookieDomain];
        NSString *cookieName = [prop objectForKey:NSHTTPCookieName];
        NSLog(@"cookie=%@&%@", cookieDomain, DOMAIN);
        if (cookieDomain && ([cookieDomain isEqualToString:DOMAIN] || [cookieDomain isEqualToString:[NSString stringWithFormat:@".%@", DOMAIN]])) {
            if(cookieName && [cookieName isEqualToString:AUTH_COOKIE_NAME]){
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
                    NSLog(@"Expired");
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

+ (BOOL)logout;
{
    NSHTTPCookieStorage *aStorage = [NSHTTPCookieStorage sharedHTTPCookieStorage];
    NSArray *cookies = [aStorage cookies];
//
//    // ログインクッキーを全て削除
//    for (NSHTTPCookie *aCookie in cookies) {
//        NSDictionary *prop = [aCookie properties];
//        NSLog(@"cookie=%@", [prop description]);
//        NSString *cookieDomain = [prop objectForKey:NSHTTPCookieDomain];
//        NSString *cookieName = [prop objectForKey:NSHTTPCookieName];
//        NSLog(@"cookie=%@&%@", cookieDomain, DOMAIN);
//        if (cookieDomain && ([cookieDomain isEqualToString:DOMAIN] || [cookieDomain isEqualToString:[NSString stringWithFormat:@".%@", DOMAIN]])) {
//            if(cookieName && [cookieName isEqualToString:AUTH_COOKIE_NAME]){
//                //無効なクッキーへ入れ替え。(deleteCookieのみだとキャッシュが残るため)
//                //[prop setValue:@"" forKey:NSHTTPCookieValue];
//                //追記：過去の時間を設定しクッキーを無効に
//                [prop setValue:[NSDate dateWithTimeIntervalSinceNow:-3600] forKey:NSHTTPCookieExpires];
//                NSHTTPCookie *newCookie = [[NSHTTPCookie alloc] initWithProperties:prop];
//                [aStorage setCookie:newCookie];
//                [aStorage deleteCookie:aCookie];
//            }
//        }
//    }
//
//    // Auth情報を削除
//    [self setUserID:nil];

    return YES;
}


+ (void)saveLocal;
{
    // NSUserDefaultsからインスタンスを復帰する
    NSData* serialAuth = [NSKeyedArchiver archivedDataWithRootObject:[Auth getInstance]];
    NSLog(@"serialAuth=%@", [serialAuth description]);
//    NSUbiquitousKeyValueStore* cloudStore = [NSUbiquitousKeyValueStore defaultStore];
//    [cloudStore setObject:serialAuth forKey:@"Auth"];
//    [cloudStore synchronize];
    [[NSUserDefaults standardUserDefaults] setValue:serialAuth forKey:@"Auth"];
}

//+ (BOOL)get;
//{
//    Auth *auth = [Auth getInstance];
//    return [auth get:auth.userID];
//}
//
//+ (BOOL)delete;
//{
//    return [[[Auth alloc] init] delete];
//}
//
//- (BOOL)delete;
//{
//    NSMutableDictionary *requestParams = [[NSMutableDictionary alloc] init];
//    [requestParams setValue:[Auth getUserID] forKey:@"user_id"];
//    BOOL returned = [self doRequest:12 :DELETE_USER_URI :requestParams];
//    
//    if(YES == returned){
//        [Auth logout];
//    }
//    
//    return returned;
//}

/**
 * オーバーライド
 * シリアライズされたAuthからインスタンスを復帰する処理を追加する
 */
+ (id)getInstance;
{
    static id sharedInstance = nil;
    if(!sharedInstance) {
        // iCloudからインスタンスを復帰する
        NSData* serialAuth = [[NSUserDefaults standardUserDefaults] objectForKey:@"Auth"];
//        NSUbiquitousKeyValueStore* cloudStore = [NSUbiquitousKeyValueStore defaultStore];
//        [cloudStore synchronize];
//        NSData* serialAuth = [cloudStore dataForKey: @"Auth"];
        NSLog(@"serialAuth=%@", [serialAuth description]);
        if([serialAuth isKindOfClass:NSClassFromString(@"NSData")]){
            sharedInstance = (Auth *)[NSKeyedUnarchiver unarchiveObjectWithData:serialAuth];
        }
        else{
            sharedInstance = [[self alloc] init];
        }
    }
    return sharedInstance;
}


#pragma mark - serialize delegate

- (id)initWithCoder:(NSCoder *)coder {
    userID = [coder decodeObjectForKey:@"userID"];
    return self;
}

- (void)encodeWithCoder:(NSCoder *)encoder {
    [encoder encodeObject:userID forKey:@"userID"];
}

@end
