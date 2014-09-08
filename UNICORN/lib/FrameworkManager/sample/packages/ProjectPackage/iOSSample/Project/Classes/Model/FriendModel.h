//
//  FriendModel.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "ModelBase.h"

@interface FriendModel : ModelBase
{
    NSString *owner_id;
    NSString *user_id;
    NSString *created;
    NSString *modified;
    NSString *available;
}

@property (strong, nonatomic) NSString *owner_id;
@property (strong, nonatomic) NSString *user_id;
@property (strong, nonatomic) NSString *created;
@property (strong, nonatomic) NSString *modified;
@property (strong, nonatomic) NSString *available;

@end
