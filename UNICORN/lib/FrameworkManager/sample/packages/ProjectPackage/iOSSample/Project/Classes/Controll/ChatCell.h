//
//  ABCell.h
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "common.h"
#import "ChatRoomViewController.h"
#import <AVFoundation/AVFoundation.h>
#import <AssetsLibrary/AssetsLibrary.h>
#import <MediaPlayer/MediaPlayer.h>

#define TABLE_VIEW_HEIGHT_FOR_ROW 65
#define SELECTED_FONT_COLOR       [UIColor colorWithRed:1.00 green:0.67 blue:0.00 alpha:1.0]

@interface ChatCell : UITableViewCell
{
    UIImageView *thumbImageView;
    MPMoviePlayerController *player;
    ChatRoomViewController *parentViewController;
}

@property (strong, nonatomic) UIImageView *thumbImageView;
@property (strong, nonatomic) UILabel *lblLike;
@property (strong, nonatomic) UIButton *likeButton;
@property (strong, nonatomic) MPMoviePlayerController *player;
@property (strong, nonatomic) ChatRoomViewController *parentViewController;
@property (strong, nonatomic) NSIndexPath *indexpath;

- (id)init:(UITableViewCellStyle)style :(NSString *)reuseIdentifier :(NSURL *)argMovieURL :(NSIndexPath *)index :(NSString *)like;

@end
