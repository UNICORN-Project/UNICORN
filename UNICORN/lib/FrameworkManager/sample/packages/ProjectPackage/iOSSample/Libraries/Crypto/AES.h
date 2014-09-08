//
//  AES.h
//  AES128CBC16進数出力固定の簡易AES(PHP・JAVAと親和している状態です)
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

@interface AES : NSObject
{
    // 何も無し
}

// AES128CBC暗号化
+ (NSString *)encryptHex:(NSString *)argPlainText :(NSString *)argKey :(NSString *)argIV;
// AES128CBC復号化
+ (NSString *)decryptHex:(NSString *)argEncText :(NSString *)argKey :(NSString *)argIV;

@end
