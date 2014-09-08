//
//  EmptyRoomView.m
//  Sample
//
//  Created by saimushi on 2014/06/09.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "EmptyRoomView.h"

@implementation EmptyRoomView

- (id)initWithFrame:(CGRect)frame
{
    self = [super initWithFrame:frame];
    if (self) {
        UIImage *backgroundImage = [UIImage imageNamed:@"wire0530"];
        UIImageView *backgroundImageView = [[UIImageView alloc] initWithImage:backgroundImage];
        backgroundImageView.frame = CGRectMake(0, 0, backgroundImage.size.width, backgroundImage.size.height);
        [self addSubview:backgroundImageView];
    }
    return self;
}

@end
