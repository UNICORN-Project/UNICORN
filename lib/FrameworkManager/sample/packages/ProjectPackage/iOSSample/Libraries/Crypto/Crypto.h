//
//  Crypto.h
//
//  Created by XXX on 12/06/13.
//  Copyright (c) 2012年 XXX. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CommonCrypto/CommonCryptor.h>
#import <CommonCrypto/CommonDigest.h>

@interface Crypto : NSObject

// AES暗号化or復号化
+ (NSData *)crypto:(NSData *)plainText key:(NSData *)symmetricKey iv:(NSData *)iv context:(CCOperation)encryptoOrDecrypto padding:(CCOptions *)pkcs7;

@end

@interface NSData(exDigest)
+ (NSData *)utf8Data: (NSString *)string;
- (NSData *)sha1Digest;
- (NSData *)md5Digest;
- (NSString *)hexString;
- (NSString *)sha1String;
@end

@interface NSString(exDigest)
- (NSString *)sha1String;
@end