//
//  AppDelegate.h
//  Sample
//
//  Created by saimushi on 2014/06/03.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "common.h"

@interface AppDelegate : UIResponder <UIApplicationDelegate, ModelDelegate>
{
    UIViewController *mainRootViewController;
}

@property (strong, nonatomic) UIWindow *window;
@property (strong, nonatomic) UIViewController *mainRootViewController;

- (void)setMainViewController;
- (void)setMainViewController:(UIViewController *)argViewControllerID;
- (BOOL)isSimulator;
- (void)showLoading;
- (void)hideLoading;

@end
