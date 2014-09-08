//
//  PublicFunction.h
//  Withly
//
//  Created by n00886 on 2012/11/06.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface PublicFunction : NSObject

// 上部ステータスバーのネットワークアクセスマークのオンオフ
+ (void) networkIndicatorOn:(BOOL)YesNo;

//// UINavigationControllerに背景イメージをセットする
//+ (void)setNavigationControllerBackgroundImage:(UINavigationController *)navigationController :(UIImage *)backgroundImage;
//
//// UITabBarControllerに背景イメージをセットする
//+ (void)setTabBarControllerBackgroundImage:(UITabBarController *)tabBarController :(UIImage *)backgroundImage;
//
//// UIToolBarControllerに背景イメージをセットする
//+ (void)setToolBarControllerBackgroundImage:(UIToolbar *)toolBarController :(UIImage *)backgroundImage;
//
//// NavigationBarのTitleLabelを返す
//+ (UILabel *)getNavigationBarTitleLabel:(NSString *)argString;

// 単純にアラートを表示する(アラートからの戻り処理無しで良い場合)
+ (void)alertShow:(NSString *)title message:(NSString *)message;
// アラート。戻り処理あり。
+ (void)alertShow:(NSString *)title message:(NSString *)message delegate:(id)delegate tag:(int)tag;
// アラート。選択肢、戻り処理あり。
+ (void)alertShow:(NSString *)title message:(NSString *)message buttonLeft:(NSString *)buttonLeft buttonRight:(NSString *)buttonRight delegate:(id)delegate tag:(int)tag;
// 予期せぬエラー
+ (void)alertShowForException;

// エラーハンドリング
+ (void)didFailWithError:(NSError *)error;

// ViewにRadiusをかける
+ (void)setCornerRadius:(UIView *)view radius:(CGFloat)radius;
+ (void)setCornerRadius:(UIView *)view radius:(CGFloat)radius masksToBounds:(BOOL)masksToBounds;

//指定したUIImageViewにボーダーを設定するメソッド
+ (void)setBorder:(UIView*)view width:(CGFloat)width color:(UIColor *)color;

// 2つのUIViewから、その中央に位置するCGRectを取得する
+ (CGRect)getCenterFrameWithView:(UIView *)argView parent:(UIView *)argParentView;

// ２つ長さから中央値のCGFloatを取得する
+ (CGFloat)getCenterValueWithFloat:(CGFloat)value parent:(CGFloat)parentValue;

// "yyyymmddhhiiss"の文字列から時間(hh:ii)を取得
+ (NSString *)getTimeString:(NSString *)argDateString;

// "YmdHis"の文字列から時間(Y/m/d)を取得
+ (NSString *)getDayString:(NSString *)argDateString;

// "yyyymmddhhiiss"の文字列から時間(yyyy/mm/dd hh:ii)を取得
+ (NSString *)getDateString:(NSString *)argDateString;

// "yyyymmddhhiiss"の文字列からLineライクな時間(同じ日だったら時:分 同じ年で違う日だったら月/日 違う年だったら年/月/日)を取得
+ (NSString *)getLinLikeDateString:(NSString *)argDateString;

// イメージをリサイズする
+ (UIImage*)resizeImage:(UIImage *)image resizeWidth:(float)argResizeWidth resizeHeight:(float)argResizeHeight;

// イメージを回転する
+ (UIImage *)rotateImage:(UIImage *)image angle:(int)angle;

// イメージにマスク処理をする
+ (UIImage *)maskImage:(UIImage *)image maskImage:(UIImage *)maskImage;

// Jsonをパースする
+ (NSMutableDictionary *)parse:(NSString *)json;
+ (NSMutableArray *)parseArr:(NSString *)json;
+ (NSMutableDictionary *)parseAuth:(NSString *)json;
+ (NSMutableDictionary *)parseChangepasswd:(NSString *)json;

// Randam
+ (int)randInt:(int)min max:(int)max;
+ (float)randFloat:(float)min and:(float)max;

// OSのバージョンを比較する
+ (BOOL)compareOSVersion:(NSString *)currentOSVersion requestOSVersion:(NSString *)argRequestOSVersion;

+ (NSString *)getSchemaWithURLString:(NSString *)URLString;
+ (NSString *)getHostWithURLString:(NSString *)URLString;
+ (NSString *)getQueryWithURLString:(NSString *)URLString;
+ (NSDictionary *)getQueryDictionary:(NSString *)queryString;

//+ (void) adjustTextLabel:(UILabel *)argLabel :(int)argFontSize;

// サムネ用のボーダー
+ (void) showBorderForThumbnail:(UIImageView *)imageView;

@end
