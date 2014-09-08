//
//  NSObject+UIView_.m
//  Footi
//
//  Created by saimushi on 2013/02/04.
//  Copyright (c) 2013年 saimushi. All rights reserved.
//

#import "UIView+position.h"

@implementation UIView (position)

- (void) setFullFrame;
{
    self.frame = CGRectMake(0, 0, [[UIScreen mainScreen] bounds].size.width, [[UIScreen mainScreen] bounds].size.height);
}

- (void) setFrame:(NSInteger)argX :(NSInteger)argY :(NSInteger)argWidth :(NSInteger)argHeight;
{
    self.frame = CGRectMake(argX, argY, argWidth, argHeight);
}

@end
