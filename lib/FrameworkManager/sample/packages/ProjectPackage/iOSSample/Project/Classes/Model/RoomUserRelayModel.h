//
//  RoomUserRelayModel.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "RoomModel.h"

@interface RoomUserRelayModel : ModelBase
{
    NSString *owner_id;
    NSString *name;
    NSString *user_id;
    NSString *room_id;
    NSString *created;
    NSString *modified;
    NSString *available;
    /* DEEP-RESTモデル */
    RoomModel *room;
}

@property (strong, nonatomic) NSString *owner_id;
@property (strong, nonatomic) NSString *name;
@property (strong, nonatomic) NSString *user_id;
@property (strong, nonatomic) NSString *room_id;
@property (strong, nonatomic) NSString *created;
@property (strong, nonatomic) NSString *modified;
@property (strong, nonatomic) NSString *available;
@property (strong, nonatomic) RoomModel *room;

/* 独自実装 */
- (BOOL)save:(NSMutableArray *)argUserIDs :(BOOL)argGrouped :(RequestCompletionHandler)argCompletionHandler;

@end
