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
#import <MediaPlayer/MediaPlayer.h>
#import "FriendModel.h"
#import "Person.h"
#import "RoomUserRelayModel.h"
#import "InviteModel.h"
#import "TimelineModel.h"
#import "MovieModel.h"

#define CAPTURE_FRAMES_PER_SECOND       20

@protocol ChatRoomViewControllerDelegate;

#pragma mark - CameraViewController

@interface ChatRoomViewController : UIViewController <UINavigationControllerDelegate,AVCaptureFileOutputRecordingDelegate,UITableViewDataSource, UITableViewDelegate,ModelDelegate> {
    id<ChatRoomViewControllerDelegate> delegate;
    
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

    MPMoviePlayerController *nowPlayngPlayer;
}

@property (nonatomic, strong) id<ChatRoomViewControllerDelegate> delegate;
@property (nonatomic) CGFloat imageMaxSize;
@property (nonatomic) CGFloat imageMinSize;
//@property (nonatomic, strong) NSMutableArray *tableData;
@property (nonatomic, strong) UITableView *movietable;
@property (strong, nonatomic) MPMoviePlayerController *nowPlayngPlayer;

- (id)init:(NSString *)argRoomID :(NSString *)argName :(NSString *)argOwnerID;
// Methods
//Setter Methods
- (void)setImageMaxSize:(CGFloat)_imageMaxSize;
- (void)setImageMinSize:(CGFloat)_imageMinSize;

// UIButton pushed
- (void)onPushLikeButton:(NSIndexPath *)indexPath;
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

#pragma mark - ChatRoomViewControllerDelegate

//delegate実装
@protocol ChatRoomViewControllerDelegate <NSObject>

@optional
- (void)chatroomViewControllerDidFinishPickingImage:(UIImage *)image;
- (void)chatroomViewControllerDidFinishPickingImage:(UIImage *)image photoLibraryUsed:(BOOL)photoLibraryUsed :(NSArray*)choises;
- (void)chatroomViewControllerDismissModalViewController;

@end
