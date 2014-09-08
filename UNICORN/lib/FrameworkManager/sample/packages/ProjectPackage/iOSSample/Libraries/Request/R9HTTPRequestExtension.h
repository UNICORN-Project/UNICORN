//
//  R9HTTPRequestExtension.h
//
//  Created by 藤田 泰介 on 12/02/25.
//  Modified by saimushi on 14/06/16.
//  Copyright (c) 2012 Revolution 9. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef void(^CompletionHandler)(NSHTTPURLResponse *responseHeader, NSString *responseString);
typedef void(^UploadProgressHandler)(float newProgress);
typedef void(^FailedHandler)(NSError *error);

@interface R9HTTPRequestExtension : NSOperation <NSURLConnectionDataDelegate>

@property (copy, nonatomic) CompletionHandler completionHandler;
@property (copy, nonatomic) FailedHandler failedHandler;
@property (copy, nonatomic) UploadProgressHandler uploadProgressHandler;
@property (strong, nonatomic) NSString *HTTPMethod;
@property (nonatomic, getter = isShouldRedirect) BOOL shouldRedirect;
@property (nonatomic, getter = isRunOnMainThread) BOOL runOnMainThread;

- (id)initWithURL:(NSURL *)targetUrl;

- (void)addHeader:(NSString *)value forKey:(NSString *)key;

- (void)addBody:(NSString *)value forKey:(NSString *)key;

- (void)setData:(NSData *)data withFileName:(NSString *)fileName andContentType:(NSString *)contentType forKey:(NSString *)key;

/* TimeoutInterval must be greater than 240 seconds. */
- (void)setTimeoutInterval:(NSTimeInterval)seconds;

- (void)startRequest;

/* 拡張 */
- (NSMutableURLRequest *)getRequest;
- (NSData *)createMultipartBodyData;
- (NSData *)createBodyData;

@end
