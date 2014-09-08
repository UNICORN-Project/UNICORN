//
//  RoomUserRelayModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "RoomUserRelayModel.h"

@implementation RoomUserRelayModel
{
    BOOL owner_id_replaced;
    BOOL name_replaced;
    BOOL user_id_replaced;
    BOOL room_id_replaced;
    BOOL created_replaced;
    BOOL modified_replaced;
    BOOL available_replaced;
}

@synthesize owner_id;
@synthesize name;
@synthesize user_id;
@synthesize room_id;
@synthesize created;
@synthesize modified;
@synthesize available;
@synthesize room;

-(void)setOwner_id:(NSString *)argOwner_id
{
    owner_id = argOwner_id;
    owner_id_replaced = YES;
    replaced = YES;
}

-(void)setName:(NSString *)argName
{
    name = argName;
    name_replaced = YES;
    replaced = YES;
}

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
        modelName = @"room_user_relay";
        owner_id_replaced = NO;
        name_replaced = NO;
        user_id_replaced = NO;
        room_id_replaced = NO;
        created_replaced = NO;
        modified_replaced = NO;
        available_replaced = NO;
    }
    return self;
}

/* 独自実装 */
/* 新規チャットルーム作成 */
- (BOOL)save:(NSMutableArray *)argUserIDs :(BOOL)argGrouped :(RequestCompletionHandler)argCompletionHandler;
{
    NSMutableDictionary *saveParams = [[NSMutableDictionary alloc] init];
    if(YES == owner_id_replaced){
        [saveParams setValue:self.owner_id forKey:@"owner_id"];
    }
    if(YES == name_replaced){
        [saveParams setValue:self.name forKey:@"name"];
    }
    if(YES == room_id_replaced){
        [saveParams setValue:self.room_id forKey:@"room_id"];
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
    NSString *firstUserID = @"";
    int userIDIdx = 0;
    for (NSString *userID in argUserIDs) {
        // 紐付けレコードを作成するユーザーIDをセット
        [saveParams setValue:userID forKey:[NSString stringWithFormat:@"user_ids[%d]", userIDIdx]];
        userIDIdx ++;
        if(1 == userIDIdx){
            firstUserID = userID;
        }
    }
    if(1 == userIDIdx){
        // 1対1で、且つ相手が既にサービスユーザーである場合のチャット新規作成はユーザーIDをセットする
        [saveParams setValue:firstUserID forKey:@"user_id"];
    }
    else {
        // それ以外はuser_idは敢えて0を指定(コレをしないとDEEP-RESTでユーザーレコードを作られてしまう！)
        [saveParams setValue:@"0" forKey:@"user_id"];
    }
    if(YES == argGrouped){
        // グループチャットフラグをセット(このパラメータは本来このモデルには存在しない！DEEPした時にROOMに対して適用される)
        [saveParams setValue:@"1" forKey:@"grouped"];
    }
    completionHandler = argCompletionHandler;
    return [super _save:saveParams];
}

/* オーバーライド */
- (BOOL)save;
{
    // このメソッドの実行を許可しない！
    // 何もしないでエラー終了
    return NO;
}

- (NSMutableDictionary *)convertModelData;
{
    NSMutableDictionary *newDic = [[NSMutableDictionary alloc] init];
    [newDic setObject:self.ID forKey:@"id"];
    [newDic setObject:self.owner_id forKey:@"owner_id"];
    [newDic setObject:self.name forKey:@"name"];
    [newDic setObject:self.user_id forKey:@"user_id"];
    [newDic setObject:self.room_id forKey:@"room_id"];
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
    self.owner_id = [argDataDic objectForKey:@"owner_id"];
    self.name = [argDataDic objectForKey:@"name"];
    self.user_id = [argDataDic objectForKey:@"user_id"];
    self.room_id = [argDataDic objectForKey:@"room_id"];
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
    owner_id_replaced = NO;
    name_replaced = NO;
    user_id_replaced = NO;
    room_id_replaced = NO;
    created_replaced = NO;
    modified_replaced = NO;
    available_replaced = NO;
    replaced = NO;
    return;
}

@end
