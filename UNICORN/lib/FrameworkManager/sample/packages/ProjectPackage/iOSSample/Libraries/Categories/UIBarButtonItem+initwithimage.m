//
//  UIBarButtonItem+initwithimage.m
//
//  Created by saimushi on 2014/06/09.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "UIBarButtonItem+initwithimage.h"

@implementation UIBarButtonItem (initwithimage)

- (UIBarButtonItem *)initWithImage:(UIImage *)image target:(id)target action:(SEL)action;
{
    UIButton *btnView = [[UIButton alloc] initWithFrame:CGRectMake(0, 0, image.size.width, image.size.height)];
    [btnView setBackgroundImage:image forState:UIControlStateNormal];
    [btnView addTarget:target action:action forControlEvents:UIControlEventTouchUpInside];
    UIBarButtonItem *customBarButtonItem = [[UIBarButtonItem alloc] initWithCustomView:btnView];
    return customBarButtonItem;
}

@end
