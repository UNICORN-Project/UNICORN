//
//  NSObject+UIView_.m
//  Footi
//
//  Created by saimushi on 2013/02/04.
//  Copyright (c) 2013年 saimushi. All rights reserved.
//

#import "UIScreen+property.h"

@implementation UIScreen (property)

- (NSInteger)width
{
    return self.bounds.size.width;
}

- (void)setWidth:(NSInteger)argWidth
{
    // セット出来ないのでワーニング回避の為のダミーメソッド
}

- (NSInteger)fullHeight
{
    return self.bounds.size.height;
}

- (void)setFullHeight:(NSInteger)argFullHeight
{
    // セット出来ないのでワーニング回避の為のダミーメソッド
}

- (NSInteger)height
{
    return self.applicationFrame.size.height;
}

- (void)setHeight:(NSInteger)argHeight
{
    // セット出来ないのでワーニング回避の為のダミーメソッド
}

+ (NSInteger)width;
{
    return [[UIScreen mainScreen] bounds].size.width;
}

+ (NSInteger)getWidth;
{
    return [self width];
}

+ (NSInteger)fullHeight;
{
    return [[UIScreen mainScreen] bounds].size.height;
}

+ (NSInteger)height;
{
    return [[UIScreen mainScreen] applicationFrame].size.height;
}

+ (NSInteger)getHeight;
{
    return [self height];
}

@end
