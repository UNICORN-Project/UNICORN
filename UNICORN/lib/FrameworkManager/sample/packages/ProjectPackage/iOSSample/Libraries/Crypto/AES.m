//
//  AES.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "AES.h"
#import "Crypto.h"
#import "NSString+bin2hex.h"
#import "NSData+hex2bin.h"

@implementation AES

/* AES128CBC暗号化 */
+ (NSString *)encryptHex:(NSString *)argPlainText :(NSString *)argKey :(NSString *)argIV;
{
    CCOptions pkcs7 = kCCOptionPKCS7Padding;
    NSData *encpyptedBin = [Crypto crypto:[argPlainText dataUsingEncoding:NSUTF8StringEncoding]
                                      key:[argKey dataUsingEncoding:NSUTF8StringEncoding]
                                       iv:[argIV dataUsingEncoding:NSUTF8StringEncoding]
                                  context:kCCEncrypt
                                  padding:&pkcs7];
    return [NSString stringHexWithData:encpyptedBin];
}

/* AES128CBC復号化 */
+ (NSString *)decryptHex:(NSString *)argEncText :(NSString *)argKey :(NSString *)argIV;
{
    CCOptions pkcs7 = kCCOptionPKCS7Padding;
    NSData *decpyptedBin = [Crypto crypto:[NSData dataWithHexString:argEncText]
                                      key:[argKey dataUsingEncoding:NSUTF8StringEncoding]
                                       iv:[argIV dataUsingEncoding:NSUTF8StringEncoding]
                                  context:kCCDecrypt
                                  padding:&pkcs7];
    return [[NSString alloc] initWithData:decpyptedBin encoding:NSUTF8StringEncoding];
}

@end

