//
//  ABCell.m
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "ChatRoomCell.h"

#define NOTIFICATIONS_BADGE_BASE_VIEW_TAG   4
#define NOTIFICATIONS_BADGE_IMAGE_VIEW_TAG  5
#define NUMBER_OF_NOTIFICATIONS_LABEL_TAG   6

#define common_badge_font @"Helvetica-Bold"
#define common_badge_size           18

@implementation ChatRoomCell
@synthesize imgUser, lblName,numberOfNotificationsLabel, notificationsBadgeBaseView,notificationsBadgeImageView;


- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {

        imgUser = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, TABLE_VIEW_HEIGHT_FOR_ROW, TABLE_VIEW_HEIGHT_FOR_ROW)];
        [self.contentView addSubview:imgUser];

        lblName = [[UILabel alloc] initWithFrame:CGRectMake(imgUser.frame.origin.x + imgUser.frame.size.width + 10, (TABLE_VIEW_HEIGHT_FOR_ROW - 27) / 2, 200, 27)];
        lblName.backgroundColor = [UIColor clearColor];
        lblName.textColor = [UIColor colorWithRed:104.0/255.0 green:209.0/255.0 blue:219.0/255.0 alpha:1];
        lblName.font = [UIFont boldSystemFontOfSize:16];
        
        [self.contentView addSubview:lblName];

        // 通知用バッジ表示ベースView
        notificationsBadgeBaseView = [[UIView alloc] init];
        notificationsBadgeBaseView.tag = NOTIFICATIONS_BADGE_BASE_VIEW_TAG;
        notificationsBadgeBaseView.backgroundColor = [UIColor clearColor];
        notificationsBadgeBaseView.hidden = YES;
        notificationsBadgeBaseView.frame = CGRectMake(self.bounds.size.width - 32,(TABLE_VIEW_HEIGHT_FOR_ROW - 32)/2,32,32);
        
        // 通知用バッジイメージ
        notificationsBadgeImageView = [[UIImageView alloc] init];
        notificationsBadgeImageView.tag = NOTIFICATIONS_BADGE_IMAGE_VIEW_TAG;
        [notificationsBadgeBaseView addSubview:notificationsBadgeImageView];
        
        // 通知数
        numberOfNotificationsLabel = [[UILabel alloc] init];
        numberOfNotificationsLabel.tag = NUMBER_OF_NOTIFICATIONS_LABEL_TAG;
        numberOfNotificationsLabel.backgroundColor = [UIColor clearColor];
        [numberOfNotificationsLabel setFont:[UIFont fontWithName:common_badge_font size:common_badge_size]];
        numberOfNotificationsLabel.numberOfLines = 1;
        numberOfNotificationsLabel.lineBreakMode = NSLineBreakByWordWrapping;
        [notificationsBadgeBaseView addSubview:numberOfNotificationsLabel];
        [self.contentView addSubview:notificationsBadgeBaseView];
        
//        // セパレータ
//        UIImageView *separatorImageView = [[UIImageView alloc] init];
//        separatorImageView.frame = CGRectMake(0, TABLE_VIEW_HEIGHT_FOR_ROW - 4, separatorImageView.frame.size.width, separatorImageView.frame.size.height);
//        [self.contentView addSubview:separatorImageView];
    }
    return self;
}

@end
