//
//  Crypto.m
//
//  Created by XXX on 12/06/13.
//  Copyright (c) 2012年 XXX. All rights reserved.
//

#import "Crypto.h"

@implementation Crypto

#define KEY	Fejix6BUJgRSs5t7
#define IV	d5056d5c6a50b2202f12d0a757d779cb
#define kChosenCipherBlockSize	kCCBlockSizeAES128
#define kChosenCipherKeySize	kCCKeySizeAES128

// AES暗号化or復号化
+ (NSData *)crypto:(NSData *)plainText key:(NSData *)symmetricKey iv:(NSData *)iv context:(CCOperation)encryptoOrDecrypto padding:(CCOptions *)pkcs7
{
	CCCryptorStatus ccStatus = kCCSuccess;
	// Symmetric crypto reference.
	CCCryptorRef thisEncipher = NULL;
	// Cipher Text container.
	NSData * cipherOrPlainText = nil;
	// Pointer to output buffer.
	uint8_t * bufferPtr = NULL;
	// Total size of the buffer.
	size_t bufferPtrSize = 0;
	// Remaining bytes to be performed on.
	size_t remainingBytes = 0;
	// Number of bytes moved to buffer.
	size_t movedBytes = 0;
	// Length of plainText buffer.
	size_t plainTextBufferSize = 0;
	// Placeholder for total written.
	size_t totalBytesWritten = 0;
	// A friendly helper pointer.
	uint8_t * ptr;
	
	/*
     LOGGING_FACILITY(plainText != nil, @"PlainText object cannot be nil." );
     LOGGING_FACILITY(symmetricKey != nil, @"Symmetric key object cannot be nil." );
     LOGGING_FACILITY(pkcs7 != NULL, @"CCOptions * pkcs7 cannot be NULL." );
     LOGGING_FACILITY([symmetricKey length] == kChosenCipherKeySize, @"Disjoint choices for key size." );
     */
    
	plainTextBufferSize = [plainText length];
	
	//LOGGING_FACILITY(plainTextBufferSize > 0, @"Empty plaintext passed in." );
	
	// We don't want to toss padding on if we don't need to
	if (encryptoOrDecrypto == kCCEncrypt) {
		if (*pkcs7 != kCCOptionECBMode) {
			if ((plainTextBufferSize % kChosenCipherBlockSize) == 0) {
				*pkcs7 = 0x0000;
			} else {
				*pkcs7 = kCCOptionPKCS7Padding;
			}
		}
        //} else if (encryptOrDecrypt != kCCDecrypt) {
		//LOGGING_FACILITY1( 0, @"Invalid CCOperation parameter [%d] for cipher context.", *pkcs7 );
	} 
	
	// Create and Initialize the crypto reference.
	ccStatus = CCCryptorCreate(	encryptoOrDecrypto, 
							   kCCAlgorithmAES128, 
							   *pkcs7, 
							   (const void *)[symmetricKey bytes], 
							   kChosenCipherKeySize, 
							   (const void *)[iv bytes], 
							   &thisEncipher
							   );
    
	//LOGGING_FACILITY1( ccStatus == kCCSuccess, @"Problem creating the context, ccStatus == %d.", ccStatus );
	
	// Calculate byte block alignment for all calls through to and including final.
	bufferPtrSize = CCCryptorGetOutputLength(thisEncipher, plainTextBufferSize, true);
	
	// Allocate buffer.
	bufferPtr = malloc( bufferPtrSize * sizeof(uint8_t) );
	
	// Zero out buffer.
	memset((void *)bufferPtr, 0x0, bufferPtrSize);
	
	// Initialize some necessary book keeping.
	
	ptr = bufferPtr;
	
	// Set up initial size.
	remainingBytes = bufferPtrSize;
	
	// Actually perform the encryption or decryption.
	ccStatus = CCCryptorUpdate( thisEncipher,
							   (const void *) [plainText bytes],
							   plainTextBufferSize,
							   ptr,
							   remainingBytes,
							   &movedBytes
							   );
	
	//LOGGING_FACILITY1( ccStatus == kCCSuccess, @"Problem with CCCryptorUpdate, ccStatus == %d.", ccStatus );
	
	// Handle book keeping.
	ptr += movedBytes;
	remainingBytes -= movedBytes;
	totalBytesWritten += movedBytes;
	
	// Finalize everything to the output buffer.
	ccStatus = CCCryptorFinal(	thisEncipher,
							  ptr,
							  remainingBytes,
							  &movedBytes
							  );
	
	totalBytesWritten += movedBytes;
	
	if (thisEncipher) {
		(void) CCCryptorRelease(thisEncipher);
		thisEncipher = NULL;
	}
	
	//LOGGING_FACILITY1( ccStatus == kCCSuccess, @"Problem with encipherment ccStatus == %d", ccStatus );
	
	cipherOrPlainText = [NSData dataWithBytes:(const void *)bufferPtr length:(NSUInteger)totalBytesWritten];
	
	if (bufferPtr) free(bufferPtr);
	
	return cipherOrPlainText;
}

@end

@implementation NSData(exDigest)

+ (NSData *)utf8Data:(NSString *) string
{
    const char* utf8str = [string UTF8String];
    NSData* data = [NSData dataWithBytes: utf8str length: strlen(utf8str)];
    return data;
}

- (NSData *)sha1Digest
{
    unsigned char result[CC_SHA1_DIGEST_LENGTH];
    CC_SHA1([self bytes], (int)[self length], result);
    return [NSData dataWithBytes:result length:CC_SHA1_DIGEST_LENGTH];
}

- (NSData *)md5Digest
{
    unsigned char result[CC_MD5_DIGEST_LENGTH];
    CC_MD5([self bytes], (int)[self length], result);
    return [NSData dataWithBytes:result length:CC_MD5_DIGEST_LENGTH];
}

- (NSString *)sha1String
{
    return [[self sha1Digest] hexString];
}

- (NSString *)hexString
{
    unsigned int i;
    static const char *hexstr[16] = { "0", "1", "2", "3",
        "4", "5", "6", "7",
        "8", "9", "a", "b",
        "c", "d", "e", "f" };
    const char *dataBuffer = (char *)[self bytes];
    NSMutableString *stringBuffer = [NSMutableString stringWithCapacity:([self length] * 2)];
    for (i=0; i<[self length]; i++) {
        uint8_t t1, t2;
        t1 = (0x00f0 & (dataBuffer[i])) >> 4;
        t2 =  0x000f & (dataBuffer[i]);
        [stringBuffer appendFormat:@"%s", hexstr[t1]];
        [stringBuffer appendFormat:@"%s", hexstr[t2]];
    }
    
    return [stringBuffer copy];
}
@end

@implementation NSString(exDigest)
- (NSString *)sha1String
{
    return [[NSData utf8Data: self] sha1String];
}
@end
