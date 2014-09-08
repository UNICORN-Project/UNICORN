//
//  UserModel.m
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "UserModel.h"

@implementation UserModel

@synthesize userID;
@synthesize userName;
@synthesize imageURL;

- (BOOL)get:(NSString *)argUserID;
{
    NSMutableDictionary *requestParams = [[NSMutableDictionary alloc] init];
    [requestParams setValue:argUserID forKey:@"user_id"];
    BOOL returned = [self doRequest:1 :@"" :requestParams];
    
    if(YES == returned){
        // 通信成功の場合の処理
        self.userID = argUserID;
        NSArray *tmpArr = [response objectForKey:@"list"];
        if([tmpArr isKindOfClass:NSClassFromString(@"NSArray")]){
            NSDictionary *tmpDic = [tmpArr objectAtIndex:0];
            if([tmpDic isKindOfClass:NSClassFromString(@"NSDictionary")]){
                
                self.userName = [tmpDic objectForKey:@"nickname"];
                if(![self.userName isKindOfClass:NSClassFromString(@"NSString")]){
                    self.userName = @"";
                }
                self.imageURL = [tmpDic objectForKey:@"thumbnail"];
                if(![self.imageURL isKindOfClass:NSClassFromString(@"NSString")]){
                    self.imageURL = [NSString stringWithFormat:@"%@", argUserID];
                }
            }
        }
    }
    return returned;
}

@end
