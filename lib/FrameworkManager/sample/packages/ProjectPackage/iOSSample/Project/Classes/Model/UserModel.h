//
//  UserModel.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "BaseModel.h"

#define USER_RELATION_TYPE_NONE 0
#define USER_RELATION_TYPE_SACATOMO 1
#define USER_RELATION_TYPE_PENDING 2
#define USER_RELATION_TYPE_REQUEST 3
#define USER_RELATION_TYPE_MINE 4

@interface UserModel : BaseModel
{
    NSString *userID;
    NSString *userName;
    NSString *imageURL;
}

@property (strong, nonatomic) NSString *userID;
@property (strong, nonatomic) NSString *userName;
@property (strong, nonatomic) NSString *imageURL;

- (BOOL)get:(NSString *)argUserID;

@end
