//
//  MainNavigationBarView.h
//  Sample
//
//  Created by saimushi on 2014/06/09.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "common.h"
#define MainNavigationBarViewTag 999

@interface MainNavigationBarView : UIView

- (id)initWithFrame:(CGRect)frame andTitle:(NSString *)title;
- (void)setTile:(NSString *)title;

@end
