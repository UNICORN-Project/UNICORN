//
//  InviteModel.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "RoomModel.h"

@interface InviteModel : ModelBase
{
    NSString *owner_id;
    NSString *room_id;
    NSString *code;
    NSString *max_invite;
    NSString *invited;
    NSString *created;
    NSString *modified;
    NSString *available;
    /* DEEP-RESTモデル */
    RoomModel *room;
}

@property (strong, nonatomic) NSString *owner_id;
@property (strong, nonatomic) NSString *room_id;
@property (strong, nonatomic) NSString *code;
@property (strong, nonatomic) NSString *max_invite;
@property (strong, nonatomic) NSString *invited;
@property (strong, nonatomic) NSString *created;
@property (strong, nonatomic) NSString *modified;
@property (strong, nonatomic) NSString *available;
@property (strong, nonatomic) RoomModel *room;

@end
