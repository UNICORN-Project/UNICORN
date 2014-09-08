//
//  Auth.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "PublicFunction.h"

@interface Auth : NSObject <NSCoding>
{
    NSString *deviceToken;
    NSString *userID;
}

@property (strong, nonatomic) NSString *deviceToken;
@property (strong, nonatomic) NSString *userID;

+ (NSString *)getDeviceToken;
+ (NSString *)deviceToken;
+ (NSString *)getUserID;
+ (NSString *)userID;

+ (void)setDeviceToken:(NSString *)argDeviceToken;
+ (void)setUserID:(NSString *)argUserID;

+ (BOOL)isLogin;
+ (BOOL)logout;
+ (void)saveLocal;

//+ (BOOL)auth:(NSString *)argMailaddress :(NSString *)argPassword;
//- (BOOL)auth:(NSString *)argMailaddress :(NSString *)argPassword;
//+ (BOOL)register:(NSString *)argHash;
//- (BOOL)register:(NSString *)argHash;

//+ (BOOL)get;

//+ (BOOL)delete;
//- (BOOL)delete;

@end
