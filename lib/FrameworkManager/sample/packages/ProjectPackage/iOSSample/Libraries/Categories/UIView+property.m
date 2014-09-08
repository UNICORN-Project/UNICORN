//
//  NSObject+UIView_.m
//  Footi
//
//  Created by saimushi on 2013/02/04.
//  Copyright (c) 2013年 saimushi. All rights reserved.
//

#import "UIView+property.h"

@implementation UIView (property)

- (NSInteger)width
{
    return self.frame.size.width;
}

- (void)setWidth:(NSInteger)argWidth
{
    self.frame = CGRectMake(self.frame.origin.x, self.frame.origin.y, argWidth, self.frame.size.height);
}

- (NSInteger)height
{
    return self.frame.size.height;
}

- (void)setHeight:(NSInteger)argHeight
{
    self.frame = CGRectMake(self.frame.origin.x, self.frame.origin.y, self.frame.size.width, argHeight);
}

- (NSInteger)x
{
    return self.frame.origin.x;
}

- (void)setX:(NSInteger)argX
{
    self.frame = CGRectMake(argX, self.frame.origin.y, self.frame.size.width, self.frame.size.height);
}

- (NSInteger)y
{
    return self.frame.origin.y;
}

- (void)setY:(NSInteger)argY
{
    self.frame = CGRectMake(self.frame.origin.x, argY, self.frame.size.width, self.frame.size.height);
}

@end
