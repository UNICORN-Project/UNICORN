//
//  InviteModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "InviteModel.h"

@implementation InviteModel
{
    BOOL owner_id_replaced;
    BOOL room_id_replaced;
    BOOL code_replaced;
    BOOL max_invite_replaced;
    BOOL invited_replaced;
    BOOL created_replaced;
    BOOL modified_replaced;
    BOOL available_replaced;
}

@synthesize owner_id;
@synthesize room_id;
@synthesize code;
@synthesize max_invite;
@synthesize invited;
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

-(void)setRoom_id:(NSString *)argRoom_id
{
    room_id = argRoom_id;
    room_id_replaced = YES;
    replaced = YES;
}

-(void)setCode:(NSString *)argCode
{
    code = argCode;
    code_replaced = YES;
    replaced = YES;
}

-(void)setMax_invite:(NSString *)argMax_invite
{
    max_invite = argMax_invite;
    max_invite_replaced = YES;
    replaced = YES;
}

-(void)setInvited:(NSString *)argInvited
{
    invited = argInvited;
    invited_replaced = YES;
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
        modelName = @"invite";
        owner_id_replaced = NO;
        room_id_replaced = NO;
        code_replaced = NO;
        max_invite_replaced = NO;
        invited_replaced = NO;
        created_replaced = NO;
        modified_replaced = NO;
        available_replaced = NO;
    }
    return self;
}

/* オーバーライド */
- (BOOL)save;
{
    if(YES == replaced){
        NSMutableDictionary *saveParams = [[NSMutableDictionary alloc] init];
        if(YES == owner_id_replaced){
            [saveParams setValue:self.owner_id forKey:@"owner_id"];
        }
        if(YES == room_id_replaced){
            [saveParams setValue:self.room_id forKey:@"room_id"];
        }
        if(YES == code_replaced){
            [saveParams setValue:self.code forKey:@"code"];
        }
        if(YES == max_invite_replaced){
            [saveParams setValue:self.max_invite forKey:@"max_invite"];
        }
        if(YES == invited_replaced){
            [saveParams setValue:self.invited forKey:@"invited"];
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

- (NSMutableDictionary *)convertModelData;
{
    NSMutableDictionary *newDic = [[NSMutableDictionary alloc] init];
    [newDic setObject:self.ID forKey:@"id"];
    [newDic setObject:self.owner_id forKey:@"owner_id"];
    [newDic setObject:self.room_id forKey:@"room_id"];
    [newDic setObject:self.code forKey:@"code"];
    [newDic setObject:self.max_invite forKey:@"max_invite"];
    [newDic setObject:self.invited forKey:@"invited"];
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
    self.room_id = [argDataDic objectForKey:@"room_id"];
    self.code = [argDataDic objectForKey:@"code"];
    self.max_invite = [argDataDic objectForKey:@"max_invite"];
    self.invited = [argDataDic objectForKey:@"invited"];
    self.created = [argDataDic objectForKey:@"created"];
    self.modified = [argDataDic objectForKey:@"modified"];
    self.available = [argDataDic objectForKey:@"available"];
    /* DEEP-REST */
    room = [[RoomModel alloc] init:protocol :domain :urlbase :tokenKeyName :cryptKey :cryptIV :timeout];
    [room setModelData:[argDataDic objectForKey:@"room"]];
    [self resetReplaceFlagment];
}

//- (InviteModel *)objectAtIndex:(int)argIndex;
//{
//    NSMutableDictionary *nextDic = [super _objectAtIndex:argIndex];
//    InviteModel *nextModel = [[InviteModel alloc] init:protocol :domain :urlbase  :tokenKeyName :cryptKey :cryptIV :timeout];
//    [nextModel _setModelData:nextDic];
//    return nextModel;
//}

//- (void)addObject:(InviteModel *)argModel;
//{
//    [super _addObject:[[self convertModelData:argModel] mutableCopy]];
//}

- (void)resetReplaceFlagment;
{
    owner_id_replaced = NO;
    room_id_replaced = NO;
    code_replaced = NO;
    max_invite_replaced = NO;
    invited_replaced = NO;
    created_replaced = NO;
    modified_replaced = NO;
    available_replaced = NO;
    replaced = NO;
    return;
}

@end
