//
//  TimelineModel.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "RoomModel.h"

@interface TimelineModel : ModelBase
{
    NSString *user_id;
    NSString *room_id;
    NSString *thumbnail;
    NSString *url;
    NSString *like;
    NSString *created;
    NSString *modified;
    NSString *available;
    /* 独自実装 */
    NSURL *movieFileLocalPath;
    NSURL *thumbnailImageLocalPath;
    /* DEEP-RESTモデル */
    RoomModel *room;
}

@property (strong, nonatomic) NSString *user_id;
@property (strong, nonatomic) NSString *room_id;
@property (strong, nonatomic) NSString *thumbnail;
@property (strong, nonatomic) NSString *url;
@property (strong, nonatomic) NSString *like;
@property (strong, nonatomic) NSString *created;
@property (strong, nonatomic) NSString *modified;
@property (strong, nonatomic) NSString *available;
@property (strong, nonatomic) NSURL *movieFileLocalPath;
@property (strong, nonatomic) NSURL *thumbnailImageLocalPath;
@property (strong, nonatomic) RoomModel *room;

- (BOOL)incrementLike;
- (BOOL)decrementLike;

@end
