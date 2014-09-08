//
//  AppDelegate.m
//  Sample
//
//  Created by saimushi on 2014/06/03.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#import "AppDelegate.h"
#import "MainViewController.h"
#import "SecureUDID.h"

@implementation AppDelegate

@synthesize mainRootViewController;

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    // ナビゲーションバーのスタイルを定義しておく
    // ナビゲーションバーの全体の色指定
    [UINavigationBar appearance].barTintColor = RGBA(255, 40, 140, 1);
    // ナビゲーションバーのボタンアイテムのテキストカラー指定
    [UINavigationBar appearance].tintColor = [UIColor whiteColor];
    // ナビゲーションバーのタイトルテキストカラー指定
    [UINavigationBar appearance].titleTextAttributes = @{NSForegroundColorAttributeName: [UIColor whiteColor]};

    self.window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    // Override point for customization after application launch.
    self.window.backgroundColor = [UIColor whiteColor];
    [self.window makeKeyAndVisible];
//    if(nil == [ModelBase loadIdentifier:SESSION_CRYPT_KEY :SESSION_CRYPT_IV]){
//        [self setMainViewController:[[FirstViewController alloc] init]];
//    }
//    else{
        // 一度認証を踏んでいたら、Main画面からスタートさせる
        [self setMainViewController];
//    }
    
    return YES;
}

- (void)setMainViewController;
{
    UINavigationController *rootNavigation = [[UINavigationController alloc] initWithRootViewController:[[MainViewController alloc] init]];
    rootNavigation.navigationBar.barStyle = UIBarStyleBlack;
    [self setMainViewController:rootNavigation];
}

- (void)setMainViewController:(UIViewController *)argViewControllerID;
{
    self.mainRootViewController = argViewControllerID;
    [self.window setRootViewController:self.mainRootViewController];
}

- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

// デバイストークンの受取
- (void)application:(UIApplication*)app didRegisterForRemoteNotificationsWithDeviceToken:(NSData*)token
{
    [ModelBase saveDeviceTokenData:token];
}

// 通信のレジュームパッケージはそのうちつくりまふ・・・orz
//- (void)application:(UIApplication *)application performFetchWithCompletionHandler:(void (^)(UIBackgroundFetchResult))completionHandler
//{
//    [sessionTask resume];
//}

- (BOOL)isSimulator;
{
    return [[[UIDevice currentDevice] model] hasSuffix:@"Simulator"];
}


#pragma mark - ローディング関連

- (void)showLoading;
{
    // ステータスバーの通信インジケータを表示
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YES;
    // ローディングを表示
    [MRProgressOverlayView showOverlayAddedTo:self.window animated:YES];
}

- (void)hideLoading;
{
    // ステータスバーの通信インジケータを非表示
    [UIApplication sharedApplication].networkActivityIndicatorVisible = NO;
    // ローディングを非表示
    [MRProgressOverlayView dismissOverlayForView:self.window animated:YES];
}



#pragma mark - ModelDelegate関連

/* アップロード・ダウンロードプログレス通知 */
/* XXX rootViewControllerのナビゲーションバーにプログレスを表示します */
- (void)didChangeProgress:(ModelBase*)model :(double)packetBytesSent :(double)totalBytesSent :(double)totalBytesExpectedToSend;
{
    NSLog(@"[bytesSent] %f, [totalBytesSent] %f, [totalBytesExpectedToSend] %f", packetBytesSent, totalBytesSent, totalBytesExpectedToSend);
    double progress = (double)totalBytesSent / (double)totalBytesExpectedToSend;
    // performSelectorOnMainThreadで描画スレッドに行ってね♪
}

@end
