//
//  SampleModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "SampleModel.h"

@implementation SampleModel
{
    BOOL owner_id_replaced;
    BOOL name_replaced;
    BOOL created_replaced;
    BOOL modified_replaced;
    BOOL available_replaced;
}

@synthesize owner_id;
@synthesize name;
@synthesize created;
@synthesize modified;
@synthesize available;

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
        modelName = @"sample";
        owner_id_replaced = NO;
        name_replaced = NO;
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
        if(YES == name_replaced){
            [saveParams setValue:self.name forKey:@"name"];
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
    [newDic setObject:self.name forKey:@"name"];
    [newDic setObject:self.created forKey:@"created"];
    [newDic setObject:self.modified forKey:@"modified"];
    [newDic setObject:self.available forKey:@"available"];
    return newDic;
}

- (void)_setModelData:(NSMutableDictionary *)argDataDic;
{
    self.ID = [argDataDic objectForKey:@"id"];
    self.owner_id = [argDataDic objectForKey:@"owner_id"];
    self.name = [argDataDic objectForKey:@"name"];
    self.created = [argDataDic objectForKey:@"created"];
    self.modified = [argDataDic objectForKey:@"modified"];
    self.available = [argDataDic objectForKey:@"available"];
    [self resetReplaceFlagment];
}

- (void)resetReplaceFlagment;
{
    owner_id_replaced = NO;
    name_replaced = NO;
    created_replaced = NO;
    modified_replaced = NO;
    available_replaced = NO;
    replaced = NO;
    return;
}

@end
