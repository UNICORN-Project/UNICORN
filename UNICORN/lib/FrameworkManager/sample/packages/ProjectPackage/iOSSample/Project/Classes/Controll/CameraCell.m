//
//  ABCell.m
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "CameraCell.h"

@implementation CameraCell
@synthesize thumbImageView, cameraView, buttonLayerView;


- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        
        // セルのコンテンツを横向きに設定
        self.contentView.transform = CGAffineTransformMakeRotation(-M_PI / 2);
        
        cameraView = [[UIView alloc]init];
        cameraView.frame = CGRectMake(0, -64, 280, 640);
        [self.contentView addSubview:cameraView];
        
        thumbImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, -64, 280, 640)];
        [self.contentView addSubview:thumbImageView];

        buttonLayerView = [[UIView alloc]init];
        buttonLayerView.frame = CGRectMake(0, -64, 280, 640);
        [self.contentView addSubview:buttonLayerView];
    }
    return self;
}

@end
