//
//  FriendModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "FriendModel.h"

@implementation FriendModel
{
    BOOL owner_id_replaced;
    BOOL user_id_replaced;
    BOOL created_replaced;
    BOOL modified_replaced;
    BOOL available_replaced;
}

@synthesize owner_id;
@synthesize user_id;
@synthesize created;
@synthesize modified;
@synthesize available;

-(void)setOwner_id:(NSString *)argOwner_id
{
    owner_id = argOwner_id;
    owner_id_replaced = YES;
    replaced = YES;
}

-(void)setUser_id:(NSString *)argUser_id
{
    user_id = argUser_id;
    user_id_replaced = YES;
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
        modelName = @"friend";
        owner_id_replaced = NO;
        user_id_replaced = NO;
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
        if(YES == user_id_replaced){
            [saveParams setValue:self.user_id forKey:@"user_id_replaced"];
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
    [newDic setObject:self.user_id forKey:@"user_id"];
    [newDic setObject:self.created forKey:@"created"];
    [newDic setObject:self.modified forKey:@"modified"];
    [newDic setObject:self.available forKey:@"available"];
    [self resetReplaceFlagment];
    return newDic;
}

- (void)_setModelData:(NSMutableDictionary *)argDataDic;
{
    self.ID = [argDataDic objectForKey:@"id"];
    self.owner_id = [argDataDic objectForKey:@"owner_id"];
    self.user_id = [argDataDic objectForKey:@"user_id"];
    self.created = [argDataDic objectForKey:@"created"];
    self.modified = [argDataDic objectForKey:@"modified"];
    self.available = [argDataDic objectForKey:@"available"];
    [self resetReplaceFlagment];
}

//- (FriendModel *)objectAtIndex:(int)argIndex;
//{
//    NSMutableDictionary *nextDic = [super _objectAtIndex:argIndex];
//    FriendModel *nextModel = [[FriendModel alloc] init:protocol :domain :urlbase  :tokenKeyName :cryptKey :cryptIV :timeout];
//    [nextModel _setModelData:nextDic];
//    return nextModel;
//}

//- (void)addObject:(FriendModel *)argModel;
//{
//    [super _addObject:[[self convertModelData:argModel] mutableCopy]];
//}

- (void)resetReplaceFlagment;
{
    owner_id_replaced = NO;
    user_id_replaced = NO;
    created_replaced = NO;
    modified_replaced = NO;
    available_replaced = NO;
    replaced = NO;
    return;
}

@end
