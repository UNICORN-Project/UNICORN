//
//  MainNavigationBarView.m
//  Sample
//
//  Created by saimushi on 2014/06/09.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "MainNavigationBarView.h"

@implementation MainNavigationBarView
{
    UILabel *titleLabel;
}

- (id)initWithFrame:(CGRect)frame andTitle:(NSString *)title;
{
    self = [self initWithFrame:frame];
    if(self){
        self.tag = MainNavigationBarViewTag;
        UIView *view = (UIView *)[self initWithFrame:frame];
        view.y = -20;
        view.height += 20;
        view.backgroundColor = RGBA(255, 40, 140, 1);
        titleLabel = [[UILabel alloc] init];
        titleLabel.frame = view.frame;
        titleLabel.width -= 50;
        titleLabel.height -= 20;
        titleLabel.center = self.center;
        titleLabel.y = 20;
        titleLabel.backgroundColor = [UIColor clearColor];
        [titleLabel setFont:[UIFont fontWithName:@"HiraKakuProN-W6" size:navibar_title_size]];
        titleLabel.minimumScaleFactor = 1.0f;
        titleLabel.textColor = [UIColor whiteColor];
        titleLabel.textAlignment = NSTextAlignmentCenter;
        titleLabel.text = title;
        [titleLabel setAdjustsFontSizeToFitWidth:YES];
        [view insertSubview:titleLabel atIndex:1];
        view.userInteractionEnabled = NO;
    }
    return self;
}

- (void)setTile:(NSString *)title;
{
    titleLabel.text = title;
}

@end
