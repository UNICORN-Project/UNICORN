//
//  FirstViewController.m
//  Withly
//
//  Created by n00886 on 2012/10/30.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import "FirstViewController.h"
#import "SplashView.h"
#import "IntroductionViewController.h"

@interface FirstViewController ()

@end

@implementation FirstViewController

- (void)loadView
{
    [super loadView];
    [self.view addSubview:[[SplashView alloc] initWithFrame:self.view.frame]];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // 滞在カウンタ 1秒滞在
    [self performSelector:@selector(nextViewController) withObject:nil afterDelay:1.0f];
}

- (void)nextViewController
{
    [UIView animateKeyframesWithDuration:0.5f
                                   delay:0.0f
                                 options: UIViewKeyframeAnimationOptionAllowUserInteraction
                              animations:^{
                                  self.view.alpha = 0.0f;
                              }
                              completion:^(BOOL finished){
                                  [APPDELEGATE setMainViewController:[[IntroductionViewController alloc] init]];
                              }];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

@end
