//
//  NSObject+UIView_.h
//  Footi
//
//  Created by saimushi on 2013/02/04.
//  Copyright (c) 2013年 saimushi. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface UIScreen (property)

@property NSInteger width;
@property NSInteger fullHeight;
@property NSInteger height;

+ (NSInteger)width;
+ (NSInteger)fullHeight;
+ (NSInteger)height;
+ (NSInteger)getWidth;
+ (NSInteger)getHeight;

@end
