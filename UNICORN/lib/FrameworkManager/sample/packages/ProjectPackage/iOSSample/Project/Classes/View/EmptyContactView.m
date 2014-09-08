//
//  EmptyRoomView.m
//  Sample
//
//  Created by saimushi on 2014/06/09.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "EmptyContactView.h"

@implementation EmptyContactView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        UIImage *backgroundImage = [UIImage imageNamed:@"IMG_permissionerror"];
        UIImageView *backgroundImageView = [[UIImageView alloc] initWithImage:backgroundImage];
        backgroundImageView.frame = CGRectMake(0, 0, backgroundImage.size.width, backgroundImage.size.height);
        [self addSubview:backgroundImageView];
    }
    return self;
}

@end
