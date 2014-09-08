//
//  TimelineModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "TimelineModel.h"

@implementation TimelineModel
{
    BOOL user_id_replaced;
    BOOL room_id_replaced;
    BOOL thumbnail_replaced;
    BOOL url_replaced;
    BOOL like_replaced;
    BOOL created_replaced;
    BOOL modified_replaced;
    BOOL available_replaced;
}

@synthesize user_id;
@synthesize room_id;
@synthesize thumbnail;
@synthesize url;
@synthesize like;
@synthesize created;
@synthesize modified;
@synthesize available;
@synthesize movieFileLocalPath;
@synthesize thumbnailImageLocalPath;
@synthesize room;

-(void)setUser_id:(NSString *)argUser_id
{
    user_id = argUser_id;
    user_id_replaced = YES;
    replaced = YES;
}

-(void)setRoom_id:(NSString *)argRoom_id
{
    room_id = argRoom_id;
    room_id_replaced = YES;
    replaced = YES;
}

-(void)setThumbnail:(NSString *)argThumbnail
{
    thumbnail = argThumbnail;
    thumbnail_replaced = YES;
    replaced = YES;
}

-(void)setUrl:(NSString *)argUrl
{
    url = argUrl;
    url_replaced = YES;
    replaced = YES;
}

-(void)setLike:(NSString *)argLike
{
    like = argLike;
    like_replaced = YES;
    replaced = YES;
}

-(void)setCreated:(NSString *)argCreated
{
    created = argCreated;
    created_replaced = YES;
    replaced = YES;
}

-(void)setModified:(NSString *)argModified
{
    modified = argModified;
    modified_replaced = YES;
    replaced = YES;
}

-(void)setAvailable:(NSString *)argAvailable
{
    available = argAvailable;
    available_replaced = YES;
    replaced = YES;
}

/* オーバーライド */
- (id)init:(NSString *)argProtocol :(NSString *)argDomain :(NSString *)argURLBase :(NSString *)argTokenKeyName;
{
    self = [super init:argProtocol :argDomain :argURLBase :argTokenKeyName];
    if(nil != self){
        modelName = @"timeline";
        user_id_replaced = NO;
        room_id_replaced = NO;
        thumbnail_replaced = NO;
        url_replaced = NO;
        like_replaced = NO;
        created_replaced = NO;
        modified_replaced = NO;
        available_replaced = NO;
        // タイムラインは常にmyResourceを付けない！
        // XXX rooomIDでのみ絞込み。room一覧の時点でmyResourceが(アプリ内に置いては)確定している
        myResourcePrefix = @"";
    }
    return self;
}

/* オーバーライド */
- (BOOL)list:(RequestCompletionHandler)argCompletionHandler;
{
    if(YES == replaced){
        NSMutableDictionary *saveParams = [[NSMutableDictionary alloc] init];
        if(YES == room_id_replaced){
            [saveParams setValue:self.room_id forKey:@"room_id"];
        }
        completionHandler = argCompletionHandler;
        return [super _load:listedResource :saveParams];
    }
    // roomID指定が無いのでエラー
    return NO;
}

/* オーバーライド */
- (BOOL)save;
{
    if(YES == replaced){
        NSMutableDictionary *saveParams = [[NSMutableDictionary alloc] init];
        if(YES == user_id_replaced){
            [saveParams setValue:self.user_id forKey:@"user_id"];
        }
        if(YES == room_id_replaced){
            [saveParams setValue:self.room_id forKey:@"room_id"];
        }
        if(YES == thumbnail_replaced){
            [saveParams setValue:self.thumbnail forKey:@"thumbnail"];
        }
        if(YES == url_replaced){
            [saveParams setValue:self.url forKey:@"url"];
        }
        if(YES == like_replaced){
            [saveParams setValue:self.like forKey:@"like"];
        }
        if(YES == created_replaced){
            [saveParams setValue:self.created forKey:@"created"];
        }
        if(YES == modified_replaced){
            [saveParams setValue:self.modified forKey:@"modified"];
        }
        if(YES == available_replaced){
            [saveParams setValue:self.available forKey:@"available"];
        }
        return [super _save:saveParams];
    }
    // 何もしないで終了
    return YES;
}

/* 特殊なメソッド1 インクリメント(加算) */
- (BOOL)incrementLike;
{
    NSMutableDictionary *saveParams = [[NSMutableDictionary alloc] init];
    [saveParams setValue:@"increment" forKey:@"like"];
    if(YES == [super _increment:saveParams]){
        self.like = [NSString stringWithFormat:@"%d", ([self.like intValue] + 1)];
        return YES;
    }
    return NO;
}

/* 特殊なメソッド2 デクリメント(減算) */
- (BOOL)decrementLike;
{
    if(0 <= ([self.like intValue] - 1)){
        // likeのデクリメントは0未満にならないようにする制御
        NSMutableDictionary *saveParams = [[NSMutableDictionary alloc] init];
        [saveParams setValue:@"decrement" forKey:@"like"];
        if(YES == [super _increment:saveParams]){
            self.like = [NSString stringWithFormat:@"%d", ([self.like intValue] - 1)];
            return YES;
        }
        return NO;
    }
    return YES;
}

- (NSMutableDictionary *)convertModelData;
{
    NSMutableDictionary *newDic = [[NSMutableDictionary alloc] init];
    [newDic setObject:self.ID forKey:@"id"];
    [newDic setObject:self.user_id forKey:@"user_id"];
    [newDic setObject:self.room_id forKey:@"room_id"];
    [newDic setObject:self.thumbnail forKey:@"thumbnail"];
    [newDic setObject:self.url forKey:@"url"];
    [newDic setObject:self.like forKey:@"like"];
    [newDic setObject:self.created forKey:@"created"];
    [newDic setObject:self.modified forKey:@"modified"];
    [newDic setObject:self.available forKey:@"available"];
    NSMutableArray *roomList = [[NSMutableArray alloc] init];
    if(0 < self.room.total){
        do {
            [roomList addObject:[self.room convertModelData]];
        } while (YES == [room next]);
    }
    [newDic setObject:roomList forKey:@"room"];
    [self resetReplaceFlagment];
    return newDic;
}

- (void)_setModelData:(NSMutableDictionary *)argDataDic;
{
    self.ID = [argDataDic objectForKey:@"id"];
    self.user_id = [argDataDic objectForKey:@"user_id"];
    self.room_id = [argDataDic objectForKey:@"room_id"];
    self.thumbnail = [argDataDic objectForKey:@"thumbnail"];
    self.url = [argDataDic objectForKey:@"url"];
    self.like = [argDataDic objectForKey:@"like"];
    self.created = [argDataDic objectForKey:@"created"];
    self.modified = [argDataDic objectForKey:@"modified"];
    self.available = [argDataDic objectForKey:@"available"];
    /* DEEP-REST */
    room = [[RoomModel alloc] init:protocol :domain :urlbase :tokenKeyName :cryptKey :cryptIV :timeout];
    [room setModelData:[argDataDic objectForKey:@"room"]];
    [self resetReplaceFlagment];
}

- (void)resetReplaceFlagment;
{
    user_id_replaced = NO;
    room_id_replaced = NO;
    thumbnail_replaced = NO;
    url_replaced = NO;
    like_replaced = NO;
    created_replaced = NO;
    modified_replaced = NO;
    available_replaced = NO;
    replaced = NO;
    return;
}

@end
