//
//  ABCell.m
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "ContactCell.h"

@implementation ContactCell
@synthesize imgUser, lblName, radioBt;


- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {

        radioBt = [UIButton buttonWithType:UIButtonTypeCustom];
        radioBt.frame = CGRectMake(self.bounds.size.width - 32, (TABLE_VIEW_HEIGHT_FOR_ROW - 22) / 2, 22, 22);
        
        radioBt.backgroundColor = [UIColor clearColor];
        [self.contentView addSubview:radioBt];

        imgUser = [[UIImageView alloc] initWithFrame:CGRectMake(10, (TABLE_VIEW_HEIGHT_FOR_ROW - 38) / 2, 38, 38)];
        [self.contentView addSubview:imgUser];

        lblName = [[UILabel alloc] initWithFrame:CGRectMake(imgUser.frame.origin.x + imgUser.frame.size.width + 10, (TABLE_VIEW_HEIGHT_FOR_ROW - 27) / 2, 200, 27)];
        lblName.backgroundColor = [UIColor clearColor];
        [self.contentView addSubview:lblName];

//        // セパレータ
//        UIImageView *separatorImageView = [[UIImageView alloc] init];
//        separatorImageView.frame = CGRectMake(0, TABLE_VIEW_HEIGHT_FOR_ROW - 4, separatorImageView.frame.size.width, separatorImageView.frame.size.height);
//        [self.contentView addSubview:separatorImageView];
    }
    return self;
}

@end
