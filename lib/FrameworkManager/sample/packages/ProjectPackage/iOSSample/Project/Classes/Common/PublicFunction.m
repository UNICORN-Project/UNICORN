//
//  PublicFunction.m
//  Withly
//
//  Created by n00886 on 2012/11/06.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import <QuartzCore/QuartzCore.h>
#import "PublicFunction.h"
#import "SBJson.h"
#import "Crypto.h"
#import "NSData+hex2bin.h"
#import "NSString+bin2hex.h"

@implementation PublicFunction

#pragma mark - StatusBar

// 上部ステータスバーのネットワークアクセスマークのオンオフ
+ (void) networkIndicatorOn:(BOOL)YesNo
{
    [UIApplication sharedApplication].networkActivityIndicatorVisible = YesNo;
}

#pragma mark - NavigationController, TabBarController set BackgroundImage

//// UINavigationControllerに背景イメージをセットする
//+ (void)setNavigationControllerBackgroundImage:(UINavigationController *)navigationController :(UIImage *)backgroundImage;
//{
//    // iOS 5以上
//    if([navigationController.navigationBar respondsToSelector:@selector(setBackgroundImage:forBarMetrics:)] ) {
//        [navigationController.navigationBar setBackgroundImage:backgroundImage forBarMetrics:UIBarMetricsDefault];
//    } else {
//        UIImageView *backgroundImageView = [[UIImageView alloc] initWithImage:backgroundImage];
//        [[navigationController navigationBar] addSubview:backgroundImageView];
//        [[navigationController navigationBar] sendSubviewToBack:backgroundImageView];
//    }
//}
//
//// UITabBarControllerに背景イメージをセットする
//+ (void)setTabBarControllerBackgroundImage:(UITabBarController *)tabBarController :(UIImage *)backgroundImage
//{
//    // iOS 5以上
//    if([tabBarController.tabBar respondsToSelector:@selector(setBackgroundImage:)] ) {
//        [tabBarController.tabBar setBackgroundImage:backgroundImage];
//    } else {
//        UIImageView *backgroundImageView = [[UIImageView alloc] initWithImage:backgroundImage];
//        [[tabBarController tabBar] addSubview:backgroundImageView];
//        [[tabBarController tabBar] sendSubviewToBack:backgroundImageView];
//    }
//}
//
//// UIToolBarControllerに背景イメージをセットする
//+ (void)setToolBarControllerBackgroundImage:(UIToolbar *)toolBar :(UIImage *)backgroundImage
//{
//    // iOS 5以上
//    if([toolBar respondsToSelector:@selector(setBackgroundImage:forToolbarPosition:barMetrics:)] ) {
//        [toolBar setBackgroundImage:backgroundImage forToolbarPosition:UIToolbarPositionAny barMetrics:UIBarMetricsDefault];
//    } else {
//        UIImageView *backgroundImageView = [[UIImageView alloc] initWithImage:backgroundImage];
//        [toolBar addSubview:backgroundImageView];
//        [toolBar sendSubviewToBack:backgroundImageView];
//    }
//}
//
//#pragma mark - NavigationBar Title
//
//// NavigationBarのTitleLabelを返す
//+ (UILabel *)getNavigationBarTitleLabel:(NSString *)argString
//{
//    UILabel *titleLabel = [[UILabel alloc] init];
//    titleLabel.frame = CGRectZero;
//    titleLabel.backgroundColor = [UIColor clearColor];
//    [titleLabel setFont:[UIFont boldSystemFontOfSize:20]];
//    //[titleLabel setFont:[UIFont fontWithName:@"HiraMinProN-W6" size:20]];
//    titleLabel.textColor = [UIColor whiteColor];
//    titleLabel.shadowColor = [UIColor colorWithWhite:0.0 alpha:0.3];
//    titleLabel.shadowOffset = CGSizeMake(2, 2);
//    titleLabel.textAlignment = UITextAlignmentCenter;
//    titleLabel.text = argString;
//    [titleLabel sizeToFit];
//    
//    return titleLabel;
//}

#pragma mark - UIAlertView

// 単純にアラートを出す。戻り処理無し
+ (void)alertShow:(NSString *)title message:(NSString *)message
{
    UIAlertView* alert = [[UIAlertView alloc] init];
    alert.title = title;
    if(nil == message){
        message = @"Fatal Error!\rPlease try restart this application";
    }
    alert.message = message;
    [alert addButtonWithTitle:@"OK"];
    [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
}

// アラート表示。戻り処理あり。ボタンはOKのみ
+ (void)alertShow:(NSString *)title message:(NSString *)message delegate:(id)delegate tag:(int)tag
{
    UIAlertView* alert = [[UIAlertView alloc] init];
    alert.title = title;
    alert.message = message;
    alert.delegate = delegate;
    alert.tag = tag;
    [alert addButtonWithTitle:@"OK"];
    [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
}

// アラート表示。戻り処理あり。ボタンは任意の２個
+ (void)alertShow:(NSString *)title message:(NSString *)message buttonLeft:(NSString *)buttonLeft buttonRight:(NSString *)buttonRight delegate:(id)delegate tag:(int)tag
{
    UIAlertView* alert = [[UIAlertView alloc] init];
    alert.title = title;
    alert.message = message;
    alert.delegate = delegate;
    alert.tag = tag;
    [alert addButtonWithTitle:buttonLeft];
    [alert addButtonWithTitle:buttonRight];
    [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
}

// 予期せぬエラー
+ (void)alertShowForException;
{
    UIAlertView* alert = [[UIAlertView alloc] init];
    alert.title = @"";
    alert.message = @"予期しないエラーが発生しました。";
    [alert addButtonWithTitle:@"OK"];
    [alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
}

#pragma mark - Error handring

// エラーハンドリング
+ (void)didFailWithError:(NSError *)error
{
    CFRunLoopPerformBlock(CFRunLoopGetMain(), kCFRunLoopCommonModes, ^(void) {
        UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:[error localizedDescription]
                                                            message:[error localizedFailureReason]
                                                           delegate:nil
                                                  cancelButtonTitle:NSLocalizedString(@"OK", @"OK button title")
                                                  otherButtonTitles:nil];
        [alertView show];
    });
}

#pragma mark - Handring UIView

// ViewにRadiusをかける
+ (void)setCornerRadius:(UIView *)view radius:(CGFloat)radius
{
    [[view layer] setCornerRadius:radius];
}

+ (void)setCornerRadius:(UIView*)view radius:(CGFloat)radius masksToBounds:(BOOL)masksToBounds
{
    [[view layer] setCornerRadius:radius];
    [[view layer] setMasksToBounds:YES];
}

//指定したUIImageViewにボーダーを設定するメソッド
+ (void)setBorder:(UIView*)view width:(CGFloat)width color:(UIColor *)color
{
    //return;
    [[view layer] setBorderWidth:width];
    [[view layer] setBorderColor:[color CGColor]];
}

// 2つのUIViewから、その中央に位置するCGRectを取得する
+ (CGRect)getCenterFrameWithView:(UIView *)argView parent:(UIView *)argParentView
{
    CGFloat x;
    CGFloat y;
    CGFloat width;
    CGFloat height;
    
    CGFloat margin_x;
    CGFloat margin_y;
    
    width = argView.frame.size.width;
    height = argView.frame.size.height;
    
    margin_x = argParentView.frame.size.width - width;
    margin_y = argParentView.frame.size.height - height;
    
    x = margin_x / 2.0f;
    y = margin_y / 2.0f;
    
    return CGRectMake(x, y, width, height);
}

// ２つ長さから中央値のCGFloatを取得する
+ (CGFloat)getCenterValueWithFloat:(CGFloat)value parent:(CGFloat)parentValue
{
    CGFloat retValue = parentValue - value;
    retValue = retValue / 2.0f;
    return retValue;
}

#pragma mark - Handring DateString

// "YmdHis"の文字列から時間(H:i)を取得
+ (NSString *)getTimeString:(NSString *)argDateString
{
    if(![argDateString isKindOfClass:NSClassFromString(@"NSString")]){
        return @"";
    }
    NSString *hour = [argDateString substringWithRange: NSMakeRange(8, 2)];
    NSString *minute = [argDateString substringWithRange: NSMakeRange(10, 2)];
    NSString *timeString = [NSString stringWithFormat:@"%@:%@",hour,minute];
    return timeString;
}

// "YmdHis"の文字列から時間(Y/m/d)を取得
+ (NSString *)getDayString:(NSString *)argDateString
{
    if(![argDateString isKindOfClass:NSClassFromString(@"NSString")]){
        return @"";
    }
    NSString *year = [argDateString substringWithRange: NSMakeRange(0, 4)];
    NSString *month = [argDateString substringWithRange: NSMakeRange(4, 2)];
    NSString *date = [argDateString substringWithRange: NSMakeRange(6, 2)];
    NSString *dayString = [NSString stringWithFormat:@"%@/%@/%@",year, month, date];
    return dayString;
}

// "YmdHis"の文字列から時間(Y/m/d H:i)を取得
+ (NSString *)getDateString:(NSString *)argDateString
{
    if(![argDateString isKindOfClass:NSClassFromString(@"NSString")]){
        return @"";
    }
    NSString *year = [argDateString substringWithRange: NSMakeRange(0, 4)];
    NSString *month = [argDateString substringWithRange: NSMakeRange(4, 2)];
    NSString *date = [argDateString substringWithRange: NSMakeRange(6, 2)];
    NSString *hour = [argDateString substringWithRange: NSMakeRange(8, 2)];
    NSString *minute = [argDateString substringWithRange: NSMakeRange(10, 2)];
    NSString *timeString = [NSString stringWithFormat:@"%@/%@/%@ %@:%@",year, month, date, hour, minute];
    return timeString;
}

// "YmdHis"の文字列から時間(Y/m/d H:i)を取得
+ (NSString *)getLinLikeDateString:(NSString *)argDateString
{
    if(![argDateString isKindOfClass:NSClassFromString(@"NSString")]){
        return @"";
    }
    NSLog(@"argDateString=%@", argDateString);
    NSString *year = [argDateString substringWithRange: NSMakeRange(0, 4)];
    NSString *month = [argDateString substringWithRange: NSMakeRange(4, 2)];
    NSString *date = [argDateString substringWithRange: NSMakeRange(6, 2)];
    NSString *hour = [argDateString substringWithRange: NSMakeRange(8, 2)];
    NSString *minute = [argDateString substringWithRange: NSMakeRange(10, 2)];

    NSString *timeString = @"";

    // 投稿日付
    // NDDate → NSString変換用のフォーマッタを作成
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    // 和暦回避
    NSCalendar *calendar = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    [dateFormatter setDateFormat:@"yyyyMMdd"];
    [dateFormatter setCalendar:calendar];
    NSDate *nowDate = [NSDate date];
    NSString *nowDateStr = [dateFormatter stringFromDate:nowDate];

    NSLog(@"nowDateStr=%@", nowDateStr);
    NSString *nowYear = [nowDateStr substringWithRange: NSMakeRange(0, 4)];
    NSString *nowMonthAndDay = [nowDateStr substringWithRange: NSMakeRange(4, 4)];

    if(![nowYear isEqualToString:year]){
        timeString = [NSString stringWithFormat:@"%@/%@/%@",year, month, date];
    } else {
        NSLog(@"nowMonthAndDay=%@", nowMonthAndDay);
        if(![nowMonthAndDay isEqualToString:[NSString stringWithFormat:@"%@%@", month, date]]){
            timeString = [NSString stringWithFormat:@"%@/%@",month, date];
        }else{
            timeString = [NSString stringWithFormat:@"%@:%@",hour, minute];
        }
    }
    return timeString;
}

#pragma mark - Image resize

// イメージをリサイズする
+ (UIImage*)resizeImage:(UIImage *)image resizeWidth:(float)argResizeWidth resizeHeight:(float)argResizeHeight
{
    // 画像データが設定されていないときは何もしない
    if (!image) {
        return image;
    }
    
    NSLog(@"originalImageWidth=%f", image.size.width);
    NSLog(@"originalImageHeight=%f", image.size.height);
    
    // リサイズ後のイメージ
    UIImage *resizeImage;
    
    // 画像のサイズを取得
    CGSize imageSize = image.size;
    
    // リサイズ後のイメージ
    UIImage *resizeHeightImage;
    UIImage *resizeWidthImage;
    UIImage *resizeCutImage;
    
    // 選択された画像のwidthに合わせてサイズ変更
    // 倍率
    float perWidth = argResizeWidth / imageSize.width;
    
    NSLog(@"perWidth=%f", perWidth);
    
    // 縦横比を維持したままサイズ変更
    CGSize newWidthSize = CGSizeMake(imageSize.width*perWidth, imageSize.height*perWidth);
    
    UIGraphicsBeginImageContext(newWidthSize);
    
    /*
     // Retinaディスプレイ対応
     if (UIGraphicsBeginImageContextWithOptions != NULL) {
     UIGraphicsBeginImageContextWithOptions(newWidthSize, NO, [[UIScreen mainScreen] scale]);
     } else {
     UIGraphicsBeginImageContext(newWidthSize);
     }
     */
    
    // 高品質リサイズ
    CGContextRef contextRef = UIGraphicsGetCurrentContext();
	CGContextSetInterpolationQuality(contextRef, kCGInterpolationHigh);
    
    [image drawInRect:CGRectMake(0, 0, newWidthSize.width, newWidthSize.height)];
    resizeWidthImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    // widthリサイズ後、heightが短い場合はさらにリサイズ
    if (resizeWidthImage.size.height < argResizeHeight) {
        // 倍率
        float perHeight = argResizeHeight / resizeWidthImage.size.height;
        
        // 縦横比を維持したままサイズ変更
        CGSize newHeightSize = CGSizeMake(resizeWidthImage.size.width*perHeight, resizeWidthImage.size.height*perHeight);
        
        UIGraphicsBeginImageContext(newHeightSize);
        
        /*
         // Retinaディスプレイ対応
         if (UIGraphicsBeginImageContextWithOptions != NULL) {
         UIGraphicsBeginImageContextWithOptions(newHeightSize, NO, [[UIScreen mainScreen] scale]);
         } else {
         UIGraphicsBeginImageContext(newHeightSize);
         }
         */
        
        // 高品質リサイズ
        CGContextRef contextRef = UIGraphicsGetCurrentContext();
        CGContextSetInterpolationQuality(contextRef, kCGInterpolationHigh);
        
        [resizeWidthImage drawInRect:CGRectMake(0, 0, newHeightSize.width, newHeightSize.height)];
        resizeHeightImage = UIGraphicsGetImageFromCurrentImageContext();
        UIGraphicsEndImageContext();
        
        // ImageViewに合わせて画像をカット
        // 切り取る場所とサイズを指定
        CGRect rect = CGRectMake(-((resizeHeightImage.size.width-argResizeWidth)/2), -((resizeHeightImage.size.height-argResizeHeight)/2), argResizeWidth, argResizeHeight);
        UIGraphicsBeginImageContext(rect.size);
        [resizeHeightImage drawAtPoint:rect.origin];
        resizeCutImage = UIGraphicsGetImageFromCurrentImageContext();
        UIGraphicsEndImageContext();
        
        resizeImage = resizeCutImage;
    } else {
        // ImageViewに合わせて画像をカット
        // 切り取る場所とサイズを指定
        CGRect rect = CGRectMake(-((resizeWidthImage.size.width-argResizeWidth)/2), -((resizeWidthImage.size.height-argResizeHeight)/2), argResizeWidth, argResizeHeight);
        UIGraphicsBeginImageContext(rect.size);
        [resizeWidthImage drawAtPoint:rect.origin];
        resizeCutImage = UIGraphicsGetImageFromCurrentImageContext();
        UIGraphicsEndImageContext();
        
        resizeImage = resizeCutImage;
    }
    
    return resizeImage;
}

#pragma mark - Image rotate

+ (UIImage *)rotateImage:(UIImage *)image angle:(int)angle
{
    CGImageRef imgRef = [image CGImage];
    CGContextRef context;
    
    switch (angle) {
        case 90:
            UIGraphicsBeginImageContext(CGSizeMake(image.size.height, image.size.width));
            context = UIGraphicsGetCurrentContext();
            CGContextTranslateCTM(context, image.size.height, image.size.width);
            CGContextScaleCTM(context, 1.0, -1.0);
            CGContextRotateCTM(context, M_PI/2.0);
            break;
        case 180:
            UIGraphicsBeginImageContext(CGSizeMake(image.size.width, image.size.height));
            context = UIGraphicsGetCurrentContext();
            CGContextTranslateCTM(context, image.size.width, 0);
            CGContextScaleCTM(context, 1.0, -1.0);
            CGContextRotateCTM(context, -M_PI);
            break;
        case 270:
            UIGraphicsBeginImageContext(CGSizeMake(image.size.height, image.size.width));
            context = UIGraphicsGetCurrentContext();
            CGContextScaleCTM(context, 1.0, -1.0);
            CGContextRotateCTM(context, -M_PI/2.0);
            break;
        default:
            NSLog(@"you can select an angle of 90, 180, 270");
            return nil;
    }
    
    CGContextDrawImage(context, CGRectMake(0, 0, image.size.width, image.size.height), imgRef);
    UIImage *ret = UIGraphicsGetImageFromCurrentImageContext();
    
    UIGraphicsEndImageContext();
    return ret;
}

#pragma mark - Image mask

+ (UIImage *)maskImage:(UIImage *)image maskImage:(UIImage *)maskImage
{
    //マスク画像をCGImageに変換する
    CGImageRef maskRef = maskImage.CGImage;
    //マスクを作成する
    CGImageRef mask = CGImageMaskCreate(CGImageGetWidth(maskRef),
                                        CGImageGetHeight(maskRef),
                                        CGImageGetBitsPerComponent(maskRef),
                                        CGImageGetBitsPerPixel(maskRef),
                                        CGImageGetBytesPerRow(maskRef),
                                        CGImageGetDataProvider(maskRef), NULL, false);
    
    //マスクの形に切り抜く
    CGImageRef masked = CGImageCreateWithMask([image CGImage], mask);
    //CGImageをUIImageに変換する
    UIImage *maskedImage = [UIImage imageWithCGImage:masked];
    
    CGImageRelease(mask);
    CGImageRelease(masked);
    
    return maskedImage;
}

#pragma mark - Parse Json

+ (NSMutableDictionary *)parse:(NSString *)json
{
    SBJsonParser *parser = [[SBJsonParser alloc] init];
    NSMutableDictionary *jsonDic = [parser objectWithString:json];
    return jsonDic;
}

+ (NSMutableArray *)parseArr:(NSString *)json
{
    SBJsonParser *parser = [[SBJsonParser alloc] init];
    NSMutableArray *jsonDic = [parser objectWithString:json];
    return jsonDic;
}

+ (NSMutableDictionary *)parseAuth:(NSString *)json
{
    SBJsonParser *parser = [[SBJsonParser alloc] init];
    NSMutableDictionary *jsonDic = [parser objectWithString:json];
    if (![[jsonDic objectForKey:@"status"] isEqualToString:@"000"]) {
        [PublicFunction alertShow:@"" message:[jsonDic objectForKey:@"error"]];
    }
    
    return jsonDic;
}

+ (NSMutableDictionary *)parseChangepasswd:(NSString *)json
{
    NSMutableDictionary *jsonDic = [PublicFunction parse:json];
    if (![[jsonDic objectForKey:@"status"] isEqualToString:@"000"]) {
        [PublicFunction alertShow:@"" message:[jsonDic objectForKey:@"error"]];
    }
    
    return jsonDic;
}

#pragma mark - Randam

+ (int)randInt:(int)min max:(int)max
{
	static int randInitFlag;
	if (randInitFlag == 0) {
		srand((int)time(NULL));
		randInitFlag = 1;
	}
	return min + (int)(rand()*(max-min+1.0)/(1.0+RAND_MAX));
}

+ (float)randFloat:(float)min and:(float)max
{
    static int randInitFlag;
	if (randInitFlag == 0) {
		srand((int)time(NULL));
		randInitFlag = 1;
	}
    float diff = max - min;
    
    return (((float) rand() / RAND_MAX) * diff) + min;
}

#pragma mark - Others

// OSのバージョンを比較する
// 比較対象のバージョン(argRequestOSVersion)以上のバージョンのとき YES
// 比較対象のバージョン(argRequestOSVersion)が未満のバージョンときの処理 NO
+ (BOOL)compareOSVersion:(NSString *)currentOSVersion requestOSVersion:(NSString *)argRequestOSVersion
{
    if ([currentOSVersion compare:argRequestOSVersion options:NSNumericSearch] != NSOrderedDescending) {
		// 比較対象のバージョン(argRequestOSVersion)未満のバージョンときの処理
		return NO;
        
	} else {
		// 比較対象のバージョン(argRequestOSVersion)以上のバージョンのときの処理
		return YES;
	}
}

// スタンプデータを取得する
+ (NSArray *)getStampDatas
{
    // スタンプデータを"Stamp.plist"から読み込む
    NSString* stampPlistPath = [[NSBundle mainBundle] pathForResource:@"Stamp" ofType:@"plist"];
    NSArray* stampDatas = [NSArray arrayWithContentsOfFile:stampPlistPath];
    
    return stampDatas;
}

// 国際電話番号データを取得する
+ (NSArray *)getCountryCodeDatas
{
    // 国際電話番号データを"CountryCode.plist"から読み込む
    NSString* countryCodePlistPath = [[NSBundle mainBundle] pathForResource:@"CountryCode" ofType:@"plist"];
    NSArray *countryCodeDatasArray = [NSArray arrayWithContentsOfFile:countryCodePlistPath];
    
    return countryCodeDatasArray;
}

// 今日の日付を取得する (ローカル時間で取得する)
+ (NSDateComponents *)getTodayByDateComponents
{
    NSDate *nowDate = [NSDate date];
    NSCalendar* calendar = [NSCalendar currentCalendar];
    
    // 年、月、日、時、分、秒を受け取る設定
    NSUInteger flg = NSYearCalendarUnit        // YEAR
    
    | NSMonthCalendarUnit    // MONTH
    
    | NSDayCalendarUnit        // DAY
    
    | NSHourCalendarUnit    // HOUR
    
    | NSMinuteCalendarUnit    // MINUTE
    
    | NSSecondCalendarUnit;    // SECOUND
    
    NSDateComponents *localTodayDate = [calendar components:flg fromDate:nowDate];
    
    return localTodayDate;
}

// 今日の日付を取得する (ローカル時間で取得する)
+ (NSString *)getTodayByString
{
    NSDateComponents *localTodayDate = [self getTodayByDateComponents];
    NSString *localTodayDateString = [NSString stringWithFormat:@"%04d%02d%02d%02d%d%02d", (int)[localTodayDate year], (int)[localTodayDate month], (int)[localTodayDate day], (int)[localTodayDate hour], (int)[localTodayDate minute], (int)[localTodayDate second]];
    
    return localTodayDateString;
}

// URLからschemaを取得する
+ (NSString *)getSchemaWithURLString:(NSString *)URLString
{
    // ://で区切る
    NSArray *urlArray = [URLString componentsSeparatedByString:@"://"];
    NSString *schemaString = [urlArray objectAtIndex:0];
    
    return schemaString;
}

// URLからhostを取得する
+ (NSString *)getHostWithURLString:(NSString *)URLString
{
    // ://で区切る
    NSArray *urlArray = [URLString componentsSeparatedByString:@"://"];
    // ?で区切る
    NSArray *hostArray = [[urlArray objectAtIndex:1] componentsSeparatedByString:@"?"];
    NSString *hostString = [hostArray objectAtIndex:0];
    
    return hostString;
}

// URLからパラメータを取得する
+ (NSString *)getQueryWithURLString:(NSString *)URLString
{
    // ://で区切る
    NSArray *urlArray = [URLString componentsSeparatedByString:@"://"];
    // ?で区切る
    NSArray *hostArray = [[urlArray objectAtIndex:1] componentsSeparatedByString:@"?"];
    NSString *pathString = [hostArray objectAtIndex:1];
    
    return pathString;
}

// URLパラメータをNSDictionaryで取得する
+ (NSDictionary *)getQueryDictionary:(NSString *)queryString
{
    NSDictionary *queryDictionary = [NSDictionary dictionary];
    
    // &で区切る
    NSArray  *queryListArray = [queryString componentsSeparatedByString:@"&"];
    if(!queryListArray){
        return queryDictionary;
    }
    
    NSMutableDictionary* queryMutableDictionary = [NSMutableDictionary dictionary];
    // リスト分ループ
    for(NSString* query in queryListArray){
        // xx=yyyyとなっているので、=で区切ってdictionary化
        NSArray *queryList = [query componentsSeparatedByString:@"="];
        [queryMutableDictionary setObject: [queryList lastObject] forKey: [queryList objectAtIndex: 0]];
    }
    
    queryDictionary = queryMutableDictionary;
    
    return queryDictionary;
}

//+ (void) adjustTextLabel:(UILabel *)argLabel :(int)argFontSize
//{
//    CGSize labelStringSize = [argLabel.text sizeWithFont:[UIFont systemFontOfSize:argFontSize] constrainedToSize:CGSizeMake(CGFLOAT_MAX, CGFLOAT_MAX) lineBreakMode:NSLineBreakByWordWrapping];
//    argLabel.frame = CGRectMake(argLabel.frame.origin.x , argLabel.frame.origin.y, labelStringSize.width, labelStringSize.height);
//}

+ (void) showBorderForThumbnail:(UIImageView *)imageView
{
    imageView.layer.borderWidth = 1;
    imageView.layer.borderColor = [[UIColor colorWithRed:0.7 green:0.7 blue:0.7 alpha:1] CGColor];
}

@end
