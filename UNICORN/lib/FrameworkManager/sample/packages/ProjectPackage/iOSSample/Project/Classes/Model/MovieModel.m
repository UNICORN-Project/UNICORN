//
//  MovieModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "MovieModel.h"

@implementation MovieModel

@synthesize thumbnail;
@synthesize url;

/* オーバーライド */
- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName;
{
    self = [super init:argProtocol :argDomain :argURLBase :argTokenKeyName];
    if(nil != self){
        modelName = @"movie";
    }
    return self;
}

/* 独自実装 */
/* ローカルに書きだしたMP4をTimelineモデルに保存する場合 */
- (BOOL)saveMovie:(NSURL *)argLocalMP4FileURL :(NSString *)argTimeLineID :(RequestCompletionHandler)argCompletionHandler;
{
    if(nil != self.ID){
        NSLog(@"moviePath=%@", argLocalMP4FileURL);
        self.ID = [NSString stringWithFormat:@"%@.mp4", argTimeLineID];
        NSLog(@"movieFileNmae=%@", self.ID);
        completionHandler = argCompletionHandler;
        return [super _save:nil :argLocalMP4FileURL];
    }
    // 異常終了
    return NO;
}

/* ローカルに書きだした動画のThmubnailをTimelineモデルに保存する場合 */
- (BOOL)saveThumbnail:(NSURL *)argLocalImageFileURL :(NSString *)argTimeLineID :(RequestCompletionHandler)argCompletionHandler;
{
    if(nil != self.ID){
        NSLog(@"moviePath=%@", argLocalImageFileURL);
        self.ID = [NSString stringWithFormat:@"%@.jpg", argTimeLineID];
        NSLog(@"movieFileNmae=%@", self.ID);
        completionHandler = argCompletionHandler;
        return [super _save:nil :argLocalImageFileURL];
    }
    // 異常終了
    return NO;
}

/* オーバーライド */
- (BOOL)save;
{
    // このメソッドの実行を許可しない！
    // 何もしないでエラー終了
    return NO;
}

- (void)_setModelData:(NSMutableDictionary *)argDataDic;
{
    NSString *tmpThumbnail = [argDataDic objectForKey:@"thumbnail"];
    if(YES == [tmpThumbnail isKindOfClass:NSClassFromString(@"NSString")] && nil != tmpThumbnail){
        self.thumbnail = tmpThumbnail;
    }
    NSString *tmpURL = [argDataDic objectForKey:@"url"];
    if(YES == [tmpURL isKindOfClass:NSClassFromString(@"NSString")] && nil != tmpURL){
        self.url = tmpURL;
    }
}

@end
