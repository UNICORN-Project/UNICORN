//
//  CameraViewController.h
//  Withly
//
//  Created by n00886 on 2012/11/06.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import "common.h"
#import <AVFoundation/AVFoundation.h>
#import <AssetsLibrary/AssetsLibrary.h>     
#import "FriendModel.h"
#import "Person.h"
#import "RoomUserRelayModel.h"
#import "InviteModel.h"
#import "TimelineModel.h"
#import "MovieModel.h"

#define CAPTURE_FRAMES_PER_SECOND       20

@protocol CameraViewControllerDelegate;

#pragma mark - CameraViewController

@interface CameraViewController : UIViewController <UINavigationControllerDelegate,AVCaptureFileOutputRecordingDelegate> {
    id<CameraViewControllerDelegate> delegate;
    
    BOOL WeAreRecording;
    
    // 撮影中の画像を表示するView
    UIView *previewView;
    
    // UIButton
    UIButton* frontRearCameraChangeButton;
    UIButton* flashModeChangeButton;
    
    // カメラからの入力
    AVCaptureDeviceInput *videoInput;
    // Audioからの入力
    AVCaptureDeviceInput *audioInput;
    
    // キャプチャーセッション
    AVCaptureSession *session;
    
    //動画ファイルとして出力するOutput
    AVCaptureMovieFileOutput *MovieFileOutput;
    
    // キャプチャーセッションから入力のプレビュー表示
    AVCaptureVideoPreviewLayer *captureVideoPreviewLayer;
    
    // 撮影画像の長辺の最大サイズ
    CGFloat imageMaxSize;
    
    // 撮影画像の長辺の最小サイズ
    CGFloat imageMinSize;
    
    // 自動露出中
    BOOL adjustingExposure;
    
    // ツールバー
    UIToolbar *cameraToolbar;
    
    // デバイス向き
    UIDeviceOrientation orientation;
    
    // カメラロールを利用したかどうか
    BOOL photoLibraryUsed;
    
    NSDate *viewStayStartTime;
    NSDate *viewStayEndTime;
    
    UIImageView* backgroundImageBlueIv;
}

@property (nonatomic, strong) id<CameraViewControllerDelegate> delegate;
@property (nonatomic) CGFloat imageMaxSize;
@property (nonatomic) CGFloat imageMinSize;

// Friend一覧(招待者Personオブジェクトを含む)を受け取ってinitする
- (id)init:(NSMutableDictionary *)argFriendDic;

// Methods
//Setter Methods
- (void)setImageMaxSize:(CGFloat)_imageMaxSize;
- (void)setImageMinSize:(CGFloat)_imageMinSize;

// UIButton pushed
- (void)onPushCancelButton:(id)sender;
- (void)onPushFrontRearCameraChangeButton:(id)sender;
- (void)onPushFlashModeChangeButton:(id)sender;

// 録画を始めたり終えたりするイベント
- (IBAction)StartButtonPressed:(id)sender;
- (IBAction)StopButtonPressed:(id)sender;

// Other Methods
- (void)didFailWithError:(NSError *)error;
- (UIImage*)resizeImageToMaxSize:(UIImage *)image;
- (UIImage*)resizeImageToMinSize:(UIImage *)image;

// InterfaceOrientations
- (void)didRotate:(NSNotification *)notification;
- (void)correspondToDeviceRotation:(int)angle;

@end

#pragma mark - CameraViewControllerDelegate

//delegate実装
@protocol CameraViewControllerDelegate <NSObject>

@optional
- (void)cameraViewControllerDidFinishPickingImage:(UIImage *)image;
- (void)cameraViewControllerDidFinishPickingImage:(UIImage *)image photoLibraryUsed:(BOOL)photoLibraryUsed :(NSArray*)choises;
- (void)cameraViewControllerDismissModalViewController;

@end
