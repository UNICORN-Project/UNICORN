//
//  CameraViewController.m
//  Withly
//
//  Created by n00886 on 2012/11/06.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import "ChatRoomViewController.h"
#import "ChatData.h"
#import "ChatCell.h"
#import "CameraCell.h"

#pragma mark - Private Methods Define

@interface ChatRoomViewController() <UIGestureRecognizerDelegate>
{
    NSString *ownerID;
    NSString *name;
    TimelineModel *timeline;

    // 自動でタイマーはスタートします。
    NSTimer *timer;
    int timercount;
    int maxtime;
    UIImage* frame;
    int nowPage;
    
    UIImageView* beforeIconIv;
    UIImageView* nowIconIv;
    UIImageView* nextIconIv;
    UIImageView* before2IconIv;
    UIImageView* next2IconIv;
    
    UIImageView *thumbImageViewLeft;
    UIImageView *thumbImageViewRight;
}
// 引数の向きに対応するAVCatpureDeviceインスタンスを取得するメソッド
- (AVCaptureDevice *)cameraWithPosition:(AVCaptureDevicePosition)position;
// 引数のメディア種別に対応するAVCaptureConnectionインスタンスを取得するメソッド
- (AVCaptureConnection *)connectionWithMediaType:(NSString *)mediaType fromConnections:(NSArray *)connections;
// ビデオ入力数を取得する
- (NSUInteger)cameraCount;
//タップフォーカス用にview座標をカメラ座標に変換する
- (CGPoint)convertToPointOfInterestFromViewCoordinates:(CGPoint)viewCoordinates;
//シングルタップした場所にフォーカス等を合わせる
- (void)tapToAutoFocus:(UIGestureRecognizer *)gestureRecognizer;
- (void)autoFocusAtPoint:(CGPoint)point;
//ダブルタップでフォーカス等を常時オートにする
- (void)tapToContinouslyAutoFocus:(UIGestureRecognizer *)gestureRecognizer;
- (void)continuousFocusAtPoint:(CGPoint)point;

@end

@implementation ChatRoomViewController

@synthesize delegate;
@synthesize imageMaxSize;
@synthesize imageMinSize;
@synthesize movietable;
@synthesize nowPlayngPlayer;

// カメラツールバー内アイテム識別用タグ
#define CAMERA_TOOL_BAR_ALBUM_TAG                101
#define CAMERA_TOOL_BAR_SHUTTER_TAG              102
#define CAMERA_TOOL_BAR_CANCEL_TAG               103

#pragma mark - View lifecycle

- (id)init:(NSString *)argRoomID :(NSString *)argName :(NSString *)argOwnerID;
{
    self = [self init];
    if(nil != self){
        ownerID = argOwnerID;
        name = argName;
        timeline = [[TimelineModel alloc] init:PROTOCOL :DOMAIN_NAME :URL_BASE :COOKIE_TOKEN_NAME :SESSION_CRYPT_KEY :SESSION_CRYPT_IV :TIMEOUT];
        timeline.room_id = argRoomID;
        // 動画の最長記録時間
        // XXX 定数化？？
        maxtime = 60;
        nowPage = 0;
        nowPlayngPlayer = nil;
    }
    return self;
}

- (void)loadView
{
    [super loadView];

    // ナビゲーションバーにタイトルをセット(毎回addしないと、navigationBarにaddしたViewシステムに何故か消される)
    MainNavigationBarView *mainNavigationBarView = (MainNavigationBarView *)[self.navigationController.navigationBar viewWithTag:MainNavigationBarViewTag];
    if(nil != mainNavigationBarView){
        [mainNavigationBarView setTile:name];
    }

    CGFloat width = self.view.bounds.size.width;
    CGFloat height = self.view.bounds.size.height;

    timercount = 0;
    
    frame = [UIImage imageNamed:@"MASK_movie"];

    // シャッターボタン
    UIImage* shutterImage = [UIImage imageNamed:@"UI_shotbtn"];

    UIImage* backgroundImagePink = [self imageWithColor:[UIColor colorWithRed:255.0/255.0 green:40.0/255.0 blue:140.0/255.0 alpha:1]];
    UIImageView* backgroundIv = [[UIImageView alloc] initWithFrame:CGRectMake(0,0,width,height)];
    backgroundIv.image = backgroundImagePink;
    [self.view addSubview:backgroundIv];
    
    UIImage* backgroundImageBlue = [self imageWithColor:[UIColor colorWithRed:104.0/255.0 green:209.0/255.0 blue:219.0/255.0 alpha:1]];
    backgroundImageBlueIv = [[UIImageView alloc] initWithFrame:CGRectMake(0,height - shutterImage.size.height,0,shutterImage.size.height)];
    backgroundImageBlueIv.image = backgroundImageBlue;

    thumbImageViewLeft = [[UIImageView alloc] initWithFrame:CGRectMake(-260, 10, 280, 640)];
    thumbImageViewLeft.backgroundColor = [UIColor blackColor];
    thumbImageViewLeft.image = frame;
    [self.view addSubview:thumbImageViewLeft];
    thumbImageViewLeft.hidden = YES;
    
    thumbImageViewRight = [[UIImageView alloc] initWithFrame:CGRectMake(300, 10, 280, 640)];
    thumbImageViewRight.backgroundColor = [UIColor blackColor];
    thumbImageViewRight.image = frame;
    [self.view addSubview:thumbImageViewRight];
    thumbImageViewRight.hidden = YES;
    
    // シャッターボタン
    UIButton *shutterButton = [[UIButton alloc] init];
    shutterButton.frame = CGRectMake((width - shutterImage.size.width + 10)/2, height - shutterImage.size.height + 5, shutterImage.size.width -10, shutterImage.size.height -10);
    shutterButton.tag = CAMERA_TOOL_BAR_SHUTTER_TAG;
    [shutterButton setImage:shutterImage forState:UIControlStateNormal];
    
    [shutterButton addTarget:self action:@selector(StartButtonPressed:) forControlEvents:UIControlEventTouchDown];
    [shutterButton addTarget:self action:@selector(StopButtonPressed:) forControlEvents:UIControlEventTouchUpInside];
    [shutterButton addTarget:self action:@selector(StopButtonPressed:) forControlEvents:UIControlEventTouchUpOutside];
    
    UIImage* backgroundImageWhite = [self imageWithColor:[UIColor colorWithRed:255.0/255.0 green:255.0/255.0 blue:255.0/255.0 alpha:1]];
    UIImageView* backgroundWhiteIv = [[UIImageView alloc] initWithFrame:CGRectMake(0,height - shutterImage.size.height,width,shutterImage.size.height)];
    backgroundWhiteIv.image = backgroundImageWhite;
    [self.view addSubview:backgroundWhiteIv];

    self.movietable = [[UITableView alloc] initWithFrame:CGRectMake(0, self.navigationController.navigationBar.frame.size.height + 30,width - 40, 640)];
    self.movietable.delegate = self;
    self.movietable.dataSource = self;
    
    self.movietable.backgroundColor = [UIColor clearColor];
    self.movietable.separatorStyle = UITableViewCellSeparatorStyleNone;
    self.movietable.scrollsToTop = YES;
    self.movietable.clipsToBounds = NO;
    self.movietable.pagingEnabled = YES;
    self.movietable.showsVerticalScrollIndicator = NO;
    // チャットテーブルのセル選択は不許可
    self.movietable.allowsSelection = NO;
    [self.movietable setSeparatorInset:UIEdgeInsetsZero];
    
    // テーブルビューを横向きに設定
    self.movietable.center = CGPointMake(self.movietable.frame.origin.x + self.movietable.frame.size.height / 2, self.movietable.frame.origin.y + self.movietable.frame.size.width / 2);
    self.movietable.transform = CGAffineTransformMakeRotation(M_PI / 2);
    self.movietable.x = 20;
    self.movietable.y = self.navigationController.navigationBar.frame.size.height + 30;
    self.movietable.width = 280;
    self.movietable.height = height-(self.navigationController.navigationBar.frame.size.height + 30)-backgroundImageBlue.size.height;

    [self.view addSubview:backgroundWhiteIv];
    [self.view addSubview:backgroundImageBlueIv];
    
    [self.view addSubview:shutterButton];
  
    UIImage *iconGideImage = [UIImage imageNamed:@"IMG_chatroombg"];
    UIImageView *iconGideImageIv = [[UIImageView alloc] initWithFrame:CGRectMake(0, height - shutterImage.size.height - iconGideImage.size.height, iconGideImage.size.width,iconGideImage.size.height)];
    iconGideImageIv.image = iconGideImage;
    [self.view addSubview:iconGideImageIv];

    UIImage *beforeIcon = [UIImage imageNamed:@"IMG_shotposition"];
    UIImage *nowIcon = [UIImage imageNamed:@"IMG_shotposition"];
    UIImage *nextIcon = [UIImage imageNamed:@"IMG_shotposition"];
    UIImage *before2Icon = [UIImage imageNamed:@"IMG_shotposition"];
    UIImage *next2Icon = [UIImage imageNamed:@"IMG_shotposition"];
    
    beforeIconIv = [[UIImageView alloc] initWithFrame:CGRectMake(width*3/4 - beforeIcon.size.width/2,height - shutterImage.size.height - beforeIcon.size.height - 35,beforeIcon.size.width,beforeIcon.size.height)];
    beforeIconIv.image = beforeIcon;
    beforeIconIv.hidden = YES;
    [self.view addSubview:beforeIconIv];
    nowIconIv = [[UIImageView alloc] initWithFrame:CGRectMake(width/2 - nowIcon.size.width/2,height - shutterImage.size.height - nowIcon.size.height - 45,nowIcon.size.width,nowIcon.size.height)];
    nowIconIv.image = nowIcon;
    [self.view addSubview:nowIconIv];
    nextIconIv = [[UIImageView alloc] initWithFrame:CGRectMake(width/4 - nextIcon.size.width/2,height - shutterImage.size.height - nextIcon.size.height - 35,nextIcon.size.width,nextIcon.size.height)];
    nextIconIv.image = nextIcon;
    nextIconIv.hidden = YES;
    [self.view addSubview:nextIconIv];
    before2IconIv = [[UIImageView alloc] initWithFrame:CGRectMake(width-before2Icon.size.width/2,height - shutterImage.size.height - before2Icon.size.height - 5,before2Icon.size.width,before2Icon.size.height)];
    before2IconIv.image = before2Icon;
    before2IconIv.hidden = YES;
    [self.view addSubview:before2IconIv];
    next2IconIv = [[UIImageView alloc] initWithFrame:CGRectMake(-next2Icon.size.width/2,height - shutterImage.size.height - next2Icon.size.height - 5,next2Icon.size.width,next2Icon.size.height)];
    next2IconIv.image = next2Icon;
    next2IconIv.hidden = YES;
    [self.view addSubview:next2IconIv];
}

- (void)viewDidLoad
{
    // チャットルームではプレビューの作成の重い処理は描画が走ってから
    // previewView
    if (nil == previewView) {
        previewView = [[UIView alloc] init];
        previewView.frame = CGRectMake(0, -20, 280, 480);
        
        // Add a single tap gesture to focus on the point tapped, then lock focus
        UITapGestureRecognizer *singleTap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(tapToAutoFocus:)];
        [singleTap setDelegate:self];
        [singleTap setNumberOfTapsRequired:1];
        [previewView addGestureRecognizer:singleTap];
        
        // Add a double tap gesture to reset the focus mode to continuous auto focus
        UITapGestureRecognizer *doubleTap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(tapToContinouslyAutoFocus:)];
        [doubleTap setDelegate:self];
        [doubleTap setNumberOfTapsRequired:2];
        [singleTap requireGestureRecognizerToFail:doubleTap];
        [previewView addGestureRecognizer:doubleTap];
    }

    // FrontCamera, RearCameraきりかえボタン
    if (nil == frontRearCameraChangeButton) {
        UIImage* frontRearCameraChangeImage = [UIImage imageNamed:@"UI_changecameraview"];
        frontRearCameraChangeButton = [UIButton buttonWithType:UIButtonTypeCustom];
        frontRearCameraChangeButton.frame = CGRectMake(previewView.frame.origin.x + previewView.frame.size.width - frontRearCameraChangeImage.size.width - 15,previewView.frame.origin.y + previewView.frame.size.height - frontRearCameraChangeImage.size.height - 100,frontRearCameraChangeImage.size.width , frontRearCameraChangeImage.size.height);
        frontRearCameraChangeButton.backgroundColor = [UIColor clearColor];
        [frontRearCameraChangeButton setImage:frontRearCameraChangeImage forState:UIControlStateNormal];
        frontRearCameraChangeButton.alpha = 1.0;
        frontRearCameraChangeButton.opaque = NO;
        [frontRearCameraChangeButton addTarget:self action:@selector(onPushFrontRearCameraChangeButton:) forControlEvents:UIControlEventTouchUpInside];
//        [self.view addSubview:frontRearCameraChangeButton];
        // フロントカメラが無い機種の場合、ボタン出さない
        if (nil == [self cameraWithPosition:AVCaptureDevicePositionFront]) {
            frontRearCameraChangeButton.hidden = YES;
        }
    }
    
    // Flashきりかえボタン
    if (nil == flashModeChangeButton) {
        UIImage* flashModeChangeImage = [UIImage imageNamed:@"UI_strobo_auto"];
        flashModeChangeButton = [UIButton buttonWithType:UIButtonTypeCustom];
        flashModeChangeButton.frame = CGRectMake(previewView.frame.origin.x + 15, previewView.frame.origin.y + previewView.frame.size.height - flashModeChangeImage.size.height - 100, flashModeChangeImage.size.width, flashModeChangeImage.size.height);
        flashModeChangeButton.backgroundColor = [UIColor clearColor];
        [flashModeChangeButton setImage:flashModeChangeImage forState:UIControlStateNormal];
        flashModeChangeButton.alpha = 1.0;
        flashModeChangeButton.opaque = NO;
        [flashModeChangeButton addTarget:self action:@selector(onPushFlashModeChangeButton:) forControlEvents:UIControlEventTouchUpInside];
//        [self.view addSubview:flashModeChangeButton];
        // カメラにFlashが搭載されている場合のみ、Flashモード切替ボタンを表示する
        if (![[self cameraWithPosition:AVCaptureDevicePositionBack] hasFlash]) {
            flashModeChangeButton.hidden = YES;
        }
    }
    
    // デフォルトのカメラモードを設定
    // XXX Sampleではデフォルトフロントカメラモード
    AVCaptureDevice *camera = [self cameraWithPosition:AVCaptureDevicePositionFront];
    // 裏面に配置されているカメラを取得
    //AVCaptureDevice *camera = [self cameraWithPosition:AVCaptureDevicePositionBack];
    if (camera == nil) {
        // 無い場合は、背面に配置されているカメラを取得
        camera = [self cameraWithPosition:AVCaptureDevicePositionBack];
        // FrontRearCameraChangeButton disable
        frontRearCameraChangeButton.hidden = YES;
        // FlashModeChangeButton disable
        flashModeChangeButton.enabled = NO;
    }
    
    if (nil != camera) {
        // カメラからの入力を作成
        NSError *error = nil;
        if (nil == videoInput) {
            videoInput = [[AVCaptureDeviceInput alloc] initWithDevice:camera error:&error];
            
            // Flash Mode を設定
            if ([[videoInput device] lockForConfiguration:nil]) {
                if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeAuto]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeAuto];
                    UIImage* flashModeChangeAuto = [UIImage imageNamed:@"UI_strobo_auto"];
                    [flashModeChangeButton setImage:flashModeChangeAuto forState:UIControlStateNormal];
                } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOff]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeOff];
                    UIImage* flashModeChangeOff = [UIImage imageNamed:@"UI_strobo_off"];
                    [flashModeChangeButton setImage:flashModeChangeOff forState:UIControlStateNormal];
                } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOn]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeOn];
                    UIImage* flashModeChangeOn = [UIImage imageNamed:@"UI_strobo_on"];
                    [flashModeChangeButton setImage:flashModeChangeOn forState:UIControlStateNormal];
                }
                [[videoInput device] unlockForConfiguration];
            }
        }
        
        // 動画録画なのでAudioデバイスも取得する
        AVCaptureDevice *audioCaptureDevice = [AVCaptureDevice defaultDeviceWithMediaType:AVMediaTypeAudio];
        NSError *errorAudio = nil;
        audioInput = [AVCaptureDeviceInput deviceInputWithDevice:audioCaptureDevice error:&errorAudio];
        
        // 入力と出力からキャプチャーセッションを作成
        if (nil == session) {
            session = [[AVCaptureSession alloc] init];
            [session addInput:videoInput];
            [session addInput:audioInput];
            
            // キャプチャーセッションから入力のプレビュー表示を作成
            captureVideoPreviewLayer = [[AVCaptureVideoPreviewLayer alloc] initWithSession:session];
            [captureVideoPreviewLayer setFrame:CGRectMake(0,0,previewView.frame.size.width,previewView.frame.size.height)];
            [captureVideoPreviewLayer setVideoGravity:AVLayerVideoGravityResizeAspectFill];
            
            // レイヤーをViewに設定
            CALayer *viewLayer = previewView.layer;
            [viewLayer setMasksToBounds:YES];
            [viewLayer addSublayer:captureVideoPreviewLayer];
            
            
            // ファイル用のOutputを作成
            MovieFileOutput = [[AVCaptureMovieFileOutput alloc] init];
            
            // 動画の長さ
            Float64 TotalSeconds = maxtime;
            // 一秒あたりのFrame数
            int32_t preferredTimeScale = 30;
            // 動画の最大長さ
            CMTime maxDuration = CMTimeMakeWithSeconds(TotalSeconds, preferredTimeScale);
            MovieFileOutput.maxRecordedDuration = maxDuration;
            // 動画が必要とする容量
            MovieFileOutput.minFreeDiskSpaceLimit = 1024 * 1024;
            // sessionに追加
            if ([session canAddOutput:MovieFileOutput])
                [session addOutput:MovieFileOutput];
            
            // CameraDeviceの設定(後述)
            [self CameraSetOutputProperties];
            
            
            // 画像の質を設定。詳しくはドキュメントを読んでください
            [session setSessionPreset:AVCaptureSessionPresetMedium];
            if ([session canSetSessionPreset:AVCaptureSessionPreset640x480])     //Check size based configs are supported before setting them
                [session setSessionPreset:AVCaptureSessionPreset640x480];
            
            
            // セッションを開始
            [session startRunning];
        }
    }
    
    // XXX デバイス回転
    //デバイス回転通知
//    [[UIDevice currentDevice] beginGeneratingDeviceOrientationNotifications];
//    [[NSNotificationCenter defaultCenter] addObserver:self
//                                             selector:@selector(didRotate:)
//                                                 name:UIDeviceOrientationDidChangeNotification
//                                               object:nil];
    
    photoLibraryUsed = NO;

    // timelineの読み込み
    BOOL res = [timeline list:^(BOOL success, NSInteger statusCode, NSHTTPURLResponse *responseHeader, NSString *responseBody, NSError *error) {
        if (YES == success) {
            // 初期表示位置の判定
            // XXX デフォルトは一番右のカメラ画面
            int firstIdx = 0;
            nowPage = 0;
            if(timeline.total > 0){
                // 最初の投稿動画をファーストポジションに設定
                firstIdx = 1;
                nowPage = 1;
            }
            NSIndexPath* indexPath = [NSIndexPath indexPathForRow:firstIdx inSection:0];
            [self.movietable scrollToRowAtIndexPath:indexPath atScrollPosition:UITableViewScrollPositionTop animated:NO];
            [self _changeIconAndFrame:(int)indexPath.row];
            // テーブルViewを描画
            [self.view insertSubview:self.movietable atIndex:3];
            [self _changeIconAndFrame:1];
        }
        [APPDELEGATE hideLoading];
    }];
    if(NO == res){
        // 読み込みエラー
        [ModelBase showRequestError:400];
        [APPDELEGATE hideLoading];
    }
}

// セルの数を返す(必須)
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // セルに表示するデータの数を返す
    return timeline.total + 1;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 280;
}

// セルの値を返す(必須)
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    // XXX セルキャッシュはしない！
    if(indexPath.row > 0){
        // 動画プレイヤーセル
        TimelineModel *rowtimeline = [timeline objectAtIndex:(int)indexPath.row-1];
        NSURL *movieURL;
        if(YES == [rowtimeline.url hasPrefix:@"http"]){
            // アマゾンS3
            movieURL = [NSURL URLWithString:rowtimeline.url];
        }
        else{
            // まだローカルファイル
            movieURL = [NSURL URLWithString:rowtimeline.url];
        }
        NSLog(@"%@", [NSString stringWithFormat:@"Identifier-%d-%d", (int)indexPath.section, (int)indexPath.row]);
        ChatCell *cell = [[ChatCell alloc] init:UITableViewCellStyleDefault :[NSString stringWithFormat:@"Identifier-%d-%d", (int)indexPath.section, (int)indexPath.row] :movieURL :indexPath :rowtimeline.like];
        cell.parentViewController = self;
        if(YES == [rowtimeline.thumbnail hasPrefix:@"http"]){
            // アマゾンS3
            [cell.thumbImageView hnk_setImageFromURL:[NSURL URLWithString:rowtimeline.thumbnail]];
        }
        return cell;
    }
    else{
        // カメラセル
        CameraCell *cell = [[CameraCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:[NSString stringWithFormat:@"IdentifierCamera-%d-%d", (int)indexPath.section, (int)indexPath.row]];
        [cell.cameraView addSubview:previewView];
        [cell.buttonLayerView addSubview:frontRearCameraChangeButton];
        [cell.buttonLayerView addSubview:flashModeChangeButton];
        cell.thumbImageView.image = frame;
        return cell;
    }
}

- (void)_changeIconAndFrame:(int) pagenum
{
    NSLog(@"pagenum = %d",pagenum);
    NSLog(@"tablecount = %d",timeline.total);
    nowPage = pagenum;

    if(pagenum>timeline.total-2){
        next2IconIv.hidden = YES;
        if(pagenum>=timeline.total){
            nextIconIv.hidden = YES;
            thumbImageViewLeft.hidden = YES;
        }else{
            nextIconIv.hidden = NO;
            thumbImageViewLeft.hidden = NO;
        }
    }else{
        nextIconIv.hidden = NO;
        thumbImageViewLeft.hidden = NO;
        next2IconIv.hidden = NO;
    }
    
    if(pagenum<2){
        before2IconIv.hidden = YES;
        if(pagenum<1){
            beforeIconIv.hidden = YES;
            thumbImageViewRight.hidden = YES;
        }else{
            beforeIconIv.hidden = NO;
            thumbImageViewRight.hidden = NO;
        }
    }else{
        beforeIconIv.hidden = NO;
        thumbImageViewRight.hidden = NO;
        before2IconIv.hidden = NO;
    }
    
}

/**
 * スクロールビューがスワイプ中に呼ばれる
 * @attention UIScrollViewのデリゲートメソッド
 */
- (void)scrollViewDidScroll:(UIScrollView *)_scrollView
{
    CGFloat pageWidth = self.movietable.width;

    thumbImageViewLeft.hidden = YES;
    thumbImageViewRight.hidden = YES;

    if ((NSInteger)fmod(self.movietable.contentOffset.y , pageWidth) == 0){
        if (nil != nowPlayngPlayer){
            [nowPlayngPlayer stop];
            nowPlayngPlayer = nil;
        }
        nowPage = self.movietable.contentOffset.y / pageWidth;;
        [self _changeIconAndFrame:nowPage];
    }
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    WeAreRecording = NO;
    if (nil != nowPlayngPlayer){
        [nowPlayngPlayer stop];
        nowPlayngPlayer = nil;
    }
}

- (void)viewDidUnload
{
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIDeviceOrientationDidChangeNotification object:nil];
    
    [super viewDidUnload];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
}

#pragma mark - Setter Methods

- (void)setImageMaxSize:(CGFloat)_imageMaxSize
{
    imageMaxSize = _imageMaxSize;
}

- (void)setImageMinSize:(CGFloat)_imageMinSize
{
    imageMinSize = _imageMinSize;
}

#pragma mark - AVCapture Methods

// 引数の向きに対応するAVCatpureDeviceインスタンスを取得するメソッド
- (AVCaptureDevice *)cameraWithPosition:(AVCaptureDevicePosition)position {
    NSArray *devices = [AVCaptureDevice devicesWithMediaType:AVMediaTypeVideo];
    for (AVCaptureDevice *device in devices) {
        if ([device position] == position) {
            return device;
        }
    }
    return nil;
}

// 引数のメディア種別に対応するAVCaptureConnectionインスタンスを取得するメソッド
- (AVCaptureConnection *)connectionWithMediaType:(NSString *)mediaType fromConnections:(NSArray *)connections {
    for ( AVCaptureConnection *connection in connections ) {
        for ( AVCaptureInputPort *port in [connection inputPorts] ) {
            if ( [[port mediaType] isEqual:mediaType] ) {
                return connection;
            }
        }
    }
    return nil;
}

// ビデオ入力デバイス数を取得する
- (NSUInteger)cameraCount
{
    return [[AVCaptureDevice devicesWithMediaType:AVMediaTypeVideo] count];
}

#pragma mark - ELCImagePickerControllerDelegate
//// シャッターボタン
//- (void)onPushShutterButton:(id)sender
//{
//    // ビデオ入力のAVCaptureConnectionを取得
//    AVCaptureConnection *videoConnection = [self connectionWithMediaType:AVMediaTypeVideo fromConnections:[stillImageOutput connections]];
//
//    // ビデオ入力から画像を非同期で取得。ブロックで定義されている処理が呼び出され、画像データが引数から取得する
//    [stillImageOutput captureStillImageAsynchronouslyFromConnection:videoConnection
//                                                  completionHandler:^(CMSampleBufferRef imageDataSampleBuffer, NSError *error) {
//                                                      if (imageDataSampleBuffer != NULL) {
//
//                                                          // 入力された画像データからJPEGフォーマットとしてデータを取得
//                                                          NSData *imageData = [AVCaptureStillImageOutput jpegStillImageNSDataRepresentation:imageDataSampleBuffer];
//
//                                                          // JPEGデータからUIImageを作成
//                                                          UIImage *image = [[UIImage alloc] initWithData:imageData];
//
////                                                          [self returnImageToDelegate:image];
//                                                      }
//                                                  }];
//
//    // フラッシュエフェクト
//    UIView *flashView = [[UIView alloc] initWithFrame:previewView.frame];
//    flashView.backgroundColor = [UIColor whiteColor];
//    [self.view.window addSubview:flashView];
//
//    [UIView animateWithDuration:.4f
//                     animations:^{
//                         flashView.alpha = 0.f;
//                     }
//                     completion:^(BOOL finished){
//                         [flashView removeFromSuperview];
//                     }
//     ];
//}

// キャンセルボタン
- (void)onPushCancelButton:(id)sender
{
    // ステータスバー表示
    [[UIApplication sharedApplication] setStatusBarHidden:NO];
    
    // Delegateに以下のメソッドがあればそれを呼ぶ
    if([delegate respondsToSelector:@selector(chatroomViewControllerDismissModalViewController)]){
        [delegate chatroomViewControllerDismissModalViewController];
    } else {
        [self dismissViewControllerAnimated:YES completion:nil];
    }
}

- (void)onPushLikeButton:(NSIndexPath *)indexPath
{
    TimelineModel *timelineModel = [timeline objectAtIndex:(int)indexPath.row-1];
    timelineModel.delegate= self;
    [timelineModel incrementLike];
}

// FrontCamera, RearCameraきりかえボタン
- (void)onPushFrontRearCameraChangeButton:(id)sender
{
    if ([self cameraCount] > 1) {
        NSError *error;
        AVCaptureDeviceInput *newVideoInput;
        AVCaptureDevicePosition position = [[videoInput device] position];
        
        if (position == AVCaptureDevicePositionBack) {
            newVideoInput = [[AVCaptureDeviceInput alloc] initWithDevice:[self cameraWithPosition:AVCaptureDevicePositionFront] error:&error];
            UIImage* frontRearCameraChangeImage = [UIImage imageNamed:@"UI_changecameraview"];
            [frontRearCameraChangeButton setImage:frontRearCameraChangeImage forState:UIControlStateNormal];
            //FlashModeChangeButton disable
            flashModeChangeButton.enabled = NO;
        } else if (position == AVCaptureDevicePositionFront) {
            newVideoInput = [[AVCaptureDeviceInput alloc] initWithDevice:[self cameraWithPosition:AVCaptureDevicePositionBack] error:&error];
            UIImage* frontRearCameraChangeImage = [UIImage imageNamed:@"UI_changecameraview"];
            [frontRearCameraChangeButton setImage:frontRearCameraChangeImage forState:UIControlStateNormal];
            //FlashModeChangeButton enable
            flashModeChangeButton.enabled = YES;
        } else {
            goto bail;
        }
        
        if (newVideoInput != nil) {
            [session beginConfiguration];
            [session removeInput:videoInput];
            if ([session canAddInput:newVideoInput]) {
                [session addInput:newVideoInput];
                videoInput = newVideoInput;
            } else {
                [session addInput:videoInput];
            }
            [session commitConfiguration];
        } else if (error) {
            [self didFailWithError:error];
        }
    }
    
bail:
    
    return;
}

// Flashきりかえボタン
- (void)onPushFlashModeChangeButton:(id)sender
{
    // カメラにFlashが搭載されてない場合は処理中止
    if (![[videoInput device] hasFlash]) {
        return;
    }
    
    // Auto -> Off -> On (サポートされていない場合は別のモードへ切り替え)
    if ([[videoInput device] lockForConfiguration:nil]) {
        AVCaptureFlashMode flashMode = [[videoInput device] flashMode];
        switch (flashMode) {
                
                
                if ([[videoInput device] lockForConfiguration:nil]) {
                    if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeAuto]) {
                        [[videoInput device] setFlashMode:AVCaptureFlashModeAuto];
                        UIImage* flashModeChangeAuto = [UIImage imageNamed:@"UI_strobo_auto"];
                        [flashModeChangeButton setImage:flashModeChangeAuto forState:UIControlStateNormal];
                    } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOff]) {
                        [[videoInput device] setFlashMode:AVCaptureFlashModeOff];
                        UIImage* flashModeChangeOff = [UIImage imageNamed:@"UI_strobo_off"];
                        [flashModeChangeButton setImage:flashModeChangeOff forState:UIControlStateNormal];
                    } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOn]) {
                        [[videoInput device] setFlashMode:AVCaptureFlashModeOn];
                        UIImage* flashModeChangeOn = [UIImage imageNamed:@"UI_strobo_on"];
                        [flashModeChangeButton setImage:flashModeChangeOn forState:UIControlStateNormal];
                    }
                    [[videoInput device] unlockForConfiguration];
                }
                
                
                
                
                
            case AVCaptureFlashModeAuto: {
                if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOff]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeOff];
                    UIImage* flashModeChangeOff = [UIImage imageNamed:@"UI_strobo_off"];
                    [flashModeChangeButton setImage:flashModeChangeOff forState:UIControlStateNormal];
                } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOn]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeOn];
                    UIImage* flashModeChangeOn = [UIImage imageNamed:@"UI_strobo_on"];
                    [flashModeChangeButton setImage:flashModeChangeOn forState:UIControlStateNormal];
                }
                [[videoInput device] unlockForConfiguration];
                break;
            }
            case AVCaptureFlashModeOff: {
                if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOn]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeOn];
                    UIImage* flashModeChangeOn = [UIImage imageNamed:@"UI_strobo_on"];
                    [flashModeChangeButton setImage:flashModeChangeOn forState:UIControlStateNormal];
                } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeAuto]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeAuto];
                    UIImage* flashModeChangeAuto = [UIImage imageNamed:@"UI_strobo_auto"];
                    [flashModeChangeButton setImage:flashModeChangeAuto forState:UIControlStateNormal];
                }
                [[videoInput device] unlockForConfiguration];
                break;
            }
            case AVCaptureFlashModeOn: {
                if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeAuto]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeAuto];
                    UIImage* flashModeChangeAuto = [UIImage imageNamed:@"UI_strobo_auto"];
                    [flashModeChangeButton setImage:flashModeChangeAuto forState:UIControlStateNormal];
                } else if ([[videoInput device] isFlashModeSupported:AVCaptureFlashModeOff]) {
                    [[videoInput device] setFlashMode:AVCaptureFlashModeOff];
                    UIImage* flashModeChangeOff = [UIImage imageNamed:@"UI_strobo_off"];
                    [flashModeChangeButton setImage:flashModeChangeOff forState:UIControlStateNormal];
                }
                [[videoInput device] unlockForConfiguration];
                break;
            }
            default: {
                break;
            }
        }
    }
}

#pragma mark - UIImagePickerControllerDelegate Methods

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingImage:(UIImage *)image editingInfo:(NSDictionary *)editingInfo
{
    // イメージピッカーを隠す
    [picker dismissViewControllerAnimated:YES completion:nil];
    // カメラロール利用をON
    photoLibraryUsed = YES;
    //    if([self respondsToSelector:@selector(returnImageToDelegate:)]){
    //        // 0.5秒程度遅延させないと、２回目の"dismissModalViewController"が効かない
    //        [self performSelector:@selector(returnImageToDelegate:) withObject:image afterDelay:0.5];
    //    }
}

- (void)didFailWithError:(NSError *)error
{
    CFRunLoopPerformBlock(CFRunLoopGetMain(), kCFRunLoopCommonModes, ^(void) {
        UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:[error localizedDescription]
                                                            message:[error localizedFailureReason]
                                                           delegate:nil
                                                  cancelButtonTitle:@"OK"
                                                  otherButtonTitles:nil];
        [alertView show];
    });
}

- (UIImage*)resizeImageToMaxSize:(UIImage *)image
{
    // 画像データが設定されていないときは何もしない
    if (!image) {
        return image;
    }
    
    NSLog(@"originalImageWidth=%f", image.size.width);
    NSLog(@"originalImageHeight=%f", image.size.height);
    
    // イメージのサイズを取得
    CGSize imageSize = image.size;
    
    // 倍率
    float perImage;
    
    // リサイズ後のイメージサイズ
    CGSize newImageSize;
    
    if (imageSize.width >= imageSize.height) {
        // 倍率
        perImage = self.imageMaxSize / imageSize.width;
    } else {
        // 倍率
        perImage = self.imageMaxSize / imageSize.height;
    }
    
    // 縦横比を維持したままサイズ変更
    newImageSize = CGSizeMake(imageSize.width*perImage, imageSize.height*perImage);
    
    UIGraphicsBeginImageContext(newImageSize);
    
    /*
     // Retinaディスプレイ対応
     if (UIGraphicsBeginImageContextWithOptions != NULL) {
     UIGraphicsBeginImageContextWithOptions(newImageSize, NO, [[UIScreen mainScreen] scale]);
     } else {
     UIGraphicsBeginImageContext(newImageSize);
     }
     */
    
    // 高品質リサイズ
    CGContextRef contextRef = UIGraphicsGetCurrentContext();
    CGContextSetInterpolationQuality(contextRef, kCGInterpolationHigh);
    
    [image drawInRect:CGRectMake(0, 0, newImageSize.width, newImageSize.height)];
    UIImage *resizeImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    NSLog(@"resizeImageWidth=%f", resizeImage.size.width);
    NSLog(@"resizeImageHeight=%f", resizeImage.size.height);
    
    return resizeImage;
}

- (UIImage*)resizeImageToMinSize:(UIImage *)image
{
    // 画像データが設定されていないときは何もしない
    if (!image) {
        return image;
    }
    
    NSLog(@"originalImageWidth=%f", image.size.width);
    NSLog(@"originalImageHeight=%f", image.size.height);
    
    // イメージのサイズを取得
    CGSize imageSize = image.size;
    
    // 倍率
    float perImage;
    
    // リサイズ後のイメージサイズ
    CGSize newImageSize;
    
    if (imageSize.width <= imageSize.height) {
        // 倍率
        perImage = self.imageMinSize / imageSize.width;
    } else {
        // 倍率
        perImage = self.imageMinSize / imageSize.height;
    }
    
    // 縦横比を維持したままサイズ変更
    newImageSize = CGSizeMake(imageSize.width*perImage, imageSize.height*perImage);
    
    UIGraphicsBeginImageContext(newImageSize);
    
    /*
     // Retinaディスプレイ対応
     if (UIGraphicsBeginImageContextWithOptions != NULL) {
     UIGraphicsBeginImageContextWithOptions(newImageSize, NO, [[UIScreen mainScreen] scale]);
     } else {
     UIGraphicsBeginImageContext(newImageSize);
     }
     */
    
    // 高品質リサイズ
    CGContextRef contextRef = UIGraphicsGetCurrentContext();
    CGContextSetInterpolationQuality(contextRef, kCGInterpolationHigh);
    
    [image drawInRect:CGRectMake(0, 0, newImageSize.width, newImageSize.height)];
    UIImage *resizeImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    NSLog(@"resizeImageWidth=%f", resizeImage.size.width);
    NSLog(@"resizeImageHeight=%f", resizeImage.size.height);
    
    return resizeImage;
}

#pragma mark - Tap focus

// タップフォーカス用にview座標をカメラ座標に変換する
- (CGPoint)convertToPointOfInterestFromViewCoordinates:(CGPoint)viewCoordinates
{
    CGPoint pointOfInterest = CGPointMake(.5f, .5f);
    CGSize frameSize = [previewView frame].size;
    
    if (captureVideoPreviewLayer.connection.isVideoMirrored) {
        viewCoordinates.x = frameSize.width - viewCoordinates.x;
    }
    
    if ( [[captureVideoPreviewLayer videoGravity] isEqualToString:AVLayerVideoGravityResize] ) {
		// Scale, switch x and y, and reverse x
        pointOfInterest = CGPointMake(viewCoordinates.y / frameSize.height, 1.f - (viewCoordinates.x / frameSize.width));
        
    } else {
        CGRect cleanAperture;
        for (AVCaptureInputPort *port in [videoInput ports]) {
            if ([port mediaType] == AVMediaTypeVideo) {
                cleanAperture = CMVideoFormatDescriptionGetCleanAperture([port formatDescription], YES);
                CGSize apertureSize = cleanAperture.size;
                CGPoint point = viewCoordinates;
                
                CGFloat apertureRatio = apertureSize.height / apertureSize.width;
                CGFloat viewRatio = frameSize.width / frameSize.height;
                CGFloat xc = .5f;
                CGFloat yc = .5f;
                
                if ( [[captureVideoPreviewLayer videoGravity] isEqualToString:AVLayerVideoGravityResizeAspect] ) {
                    if (viewRatio > apertureRatio) {
                        CGFloat y2 = frameSize.height;
                        CGFloat x2 = frameSize.height * apertureRatio;
                        CGFloat x1 = frameSize.width;
                        CGFloat blackBar = (x1 - x2) / 2;
                        
						// If point is inside letterboxed area, do coordinate conversion; otherwise, don't change the default value returned (.5,.5)
                        if (point.x >= blackBar && point.x <= blackBar + x2) {
							// Scale (accounting for the letterboxing on the left and right of the video preview), switch x and y, and reverse x
                            xc = point.y / y2;
                            yc = 1.f - ((point.x - blackBar) / x2);
                        }
                        
                    } else {
                        CGFloat y2 = frameSize.width / apertureRatio;
                        CGFloat y1 = frameSize.height;
                        CGFloat x2 = frameSize.width;
                        CGFloat blackBar = (y1 - y2) / 2;
                        
						// If point is inside letterboxed area, do coordinate conversion. Otherwise, don't change the default value returned (.5,.5)
                        if (point.y >= blackBar && point.y <= blackBar + y2) {
							// Scale (accounting for the letterboxing on the top and bottom of the video preview), switch x and y, and reverse x
                            xc = ((point.y - blackBar) / y2);
                            yc = 1.f - (point.x / x2);
                        }
                    }
                    
                } else if ([[captureVideoPreviewLayer videoGravity] isEqualToString:AVLayerVideoGravityResizeAspectFill]) {
					// Scale, switch x and y, and reverse x
                    if (viewRatio > apertureRatio) {
                        CGFloat y2 = apertureSize.width * (frameSize.width / apertureSize.height);
                        xc = (point.y + ((y2 - frameSize.height) / 2.f)) / y2; // Account for cropped height
                        yc = (frameSize.width - point.x) / frameSize.width;
                        
                    } else {
                        CGFloat x2 = apertureSize.height * (frameSize.height / apertureSize.width);
                        yc = 1.f - ((point.x + ((x2 - frameSize.width) / 2)) / x2); // Account for cropped width
                        xc = point.y / frameSize.height;
                    }
                }
                
                pointOfInterest = CGPointMake(xc, yc);
                break;
            }
        }
    }
    
    return pointOfInterest;
}

// シングルタップした場所にフォーカス等を合わせる
// Auto focus at a particular point. The focus mode will change to locked once the auto focus happens.
- (void)tapToAutoFocus:(UIGestureRecognizer *)gestureRecognizer
{
    if ([[videoInput device] isFocusPointOfInterestSupported]) {
        CGPoint tapPoint = [gestureRecognizer locationInView:previewView];
        CGPoint convertedFocusPoint = [self convertToPointOfInterestFromViewCoordinates:tapPoint];
        [self autoFocusAtPoint:convertedFocusPoint];
    }
}

// ダブルタップでフォーカス等を常時オートにする
// Change to continuous auto focus. The camera will constantly focus at the point choosen.
- (void)tapToContinouslyAutoFocus:(UIGestureRecognizer *)gestureRecognizer
{
    if ([[videoInput device] isFocusPointOfInterestSupported]) {
        [self continuousFocusAtPoint:CGPointMake(.5f, .5f)];
    }
}

// Perform an auto focus at the specified point. The focus mode will automatically change to locked once the auto focus is complete.
- (void)autoFocusAtPoint:(CGPoint)point
{
    AVCaptureDevice *device = [videoInput device];
    
    // フォーカスポイントの設定
    if ([device isFocusPointOfInterestSupported] && [device isFocusModeSupported:AVCaptureFocusModeAutoFocus]) {
        NSError *error;
        
        if ([device lockForConfiguration:&error]) {
            [device setFocusPointOfInterest:point];
            [device setFocusMode:AVCaptureFocusModeAutoFocus];
            [device unlockForConfiguration];
            
        } else {
            [self didFailWithError:error];
        }
    }
    
    // ポイント露出の設定
    if ([device isExposurePointOfInterestSupported] && [device isExposureModeSupported:AVCaptureExposureModeContinuousAutoExposure ]) {
        NSError *error;
        
        if ([device lockForConfiguration:&error]) {
            [device setExposurePointOfInterest:point];
            [device setExposureMode:AVCaptureExposureModeContinuousAutoExposure];
            adjustingExposure = YES;
            [device unlockForConfiguration];
        }
    }
    
    // ホワイトバランスの設定
    if ([device isWhiteBalanceModeSupported:AVCaptureWhiteBalanceModeContinuousAutoWhiteBalance]) {
        NSError *error;
        
        if ([device lockForConfiguration:&error]) {
            device.whiteBalanceMode = AVCaptureWhiteBalanceModeContinuousAutoWhiteBalance;
            [device unlockForConfiguration];
        }
        
    } else if ([device isWhiteBalanceModeSupported:AVCaptureWhiteBalanceModeAutoWhiteBalance]) {
        NSError *error;
        
        if ([device lockForConfiguration:&error]) {
            device.whiteBalanceMode = AVCaptureWhiteBalanceModeAutoWhiteBalance;
            [device unlockForConfiguration];
        }
    }
}

// Switch to continuous auto focus mode at the specified point
- (void)continuousFocusAtPoint:(CGPoint)point
{
    AVCaptureDevice *device = [videoInput device];
	
    if ([device isFocusPointOfInterestSupported] && [device isFocusModeSupported:AVCaptureFocusModeContinuousAutoFocus]) {
		NSError *error;
        
		if ([device lockForConfiguration:&error]) {
			[device setFocusPointOfInterest:point];
			[device setFocusMode:AVCaptureFocusModeContinuousAutoFocus];
			[device unlockForConfiguration];
            
		} else {
			[self didFailWithError:error];
		}
	}
    
    // AEロック
    if ([device isExposurePointOfInterestSupported] && [device isExposureModeSupported:AVCaptureExposureModeContinuousAutoExposure ]) {
        NSError *error;
        
        if ([device lockForConfiguration:&error]) {
            [device setExposurePointOfInterest:point];
            [device setExposureMode:AVCaptureExposureModeContinuousAutoExposure];
            [device unlockForConfiguration];
        }
    }
}

// XXX デバイス回転
#pragma mark - InterfaceOrientations

// iOS6 ロテート許可
- (BOOL)shouldAutorotate
{
    //[self.view setNeedsLayout];
	return YES;
}

// iOS6 サポート向き
- (NSUInteger)supportedInterfaceOrientations
{
    return UIInterfaceOrientationMaskAll;
}

// iOS6 初期向き
- (UIInterfaceOrientation)preferredInterfaceOrientationForPresentation
{
    orientation = UIDeviceOrientationPortrait;
    return UIInterfaceOrientationPortrait;
}

// iOS5 ロテート許可
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)orientation
{
    return NO;
}

//デバイス回転通知時の命令
- (void)didRotate:(NSNotification *)notification
{
    CGFloat width = self.view.bounds.size.width;
    CGFloat height = self.view.bounds.size.height;
    
    // 平面方向の検知は無視
    if (UIDeviceOrientationFaceUp == [(UIDevice *)notification.object orientation] || UIDeviceOrientationFaceDown == [(UIDevice *)notification.object orientation]) {
        return;
    }
    
    // 回転の情報を取得
    orientation = [(UIDevice *)notification.object orientation];
    
    if (UIDeviceOrientationPortrait == orientation) {
        [self correspondToDeviceRotation:0];
        
        flashModeChangeButton.frame = CGRectMake(10, 10, 100, 32);
        frontRearCameraChangeButton.frame = CGRectMake(210, 10, 100, 32);
        
    } else if (UIDeviceOrientationPortraitUpsideDown == orientation) {
        [self correspondToDeviceRotation:180];
        
        flashModeChangeButton.frame = CGRectMake(10, 10, 100, 32);
        frontRearCameraChangeButton.frame = CGRectMake(210, 10, 100, 32);
        
    } else if (UIDeviceOrientationLandscapeLeft == orientation) {
        [self correspondToDeviceRotation:90];
        
        flashModeChangeButton.frame = CGRectMake(width - 42, 10, 32, 100);
        frontRearCameraChangeButton.frame = CGRectMake(width - 42, height - 110, 32, 100);
        
    } else if (UIDeviceOrientationLandscapeRight == orientation) {
        [self correspondToDeviceRotation:270];
        
        flashModeChangeButton.frame = CGRectMake(10, height - 110, 32, 100);
        frontRearCameraChangeButton.frame = CGRectMake(10, 10, 32, 100);
    }
}

- (void)correspondToDeviceRotation:(int)angle
{
    // 回転させるためのアフィン変形を作成する
    CGAffineTransform transform = CGAffineTransformMakeRotation(angle * M_PI / 180);
    
    // 回転させるのアニメーション
    [UIView beginAnimations:@"DEVICE_ROTATION" context:nil];
    [UIView setAnimationDuration:0.3];
    
    UIButton *photoAlbumButton = (UIButton *)[cameraToolbar viewWithTag:CAMERA_TOOL_BAR_ALBUM_TAG];
    UIButton *shutterButton = (UIButton *)[cameraToolbar viewWithTag:CAMERA_TOOL_BAR_SHUTTER_TAG];
    UIButton *cancelButton = (UIButton *)[cameraToolbar viewWithTag:CAMERA_TOOL_BAR_CANCEL_TAG];
    
    photoAlbumButton.transform = transform;
    shutterButton.transform = transform;
    cancelButton.transform = transform;
    flashModeChangeButton.transform = transform;
    frontRearCameraChangeButton.transform = transform;
    
    // アニメーション開始
    [UIView commitAnimations];
}

#pragma mark Action

- (void)onPushCloseButton :(id)sender
{
    [[self presentingViewController] dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)StartButtonPressed:(id)sender
{
    if(0 == nowPage){
        // カメラセルの時だけ動作
        [self StartRecording];
    }
}

- (IBAction)StopButtonPressed:(id)sender
{
    if(0 == nowPage){
        // カメラセルの時だけ動作
        [self StopRecording];
    }
    else {
        // それ以外の時はカメラ画面に遷移
        [self.movietable scrollToRowAtIndexPath:[NSIndexPath indexPathForRow:0 inSection:0] atScrollPosition:UITableViewScrollPositionTop animated:YES];
    }
}


-(void)StartRecording
{
    if(!WeAreRecording){
        WeAreRecording = YES;
        
        //保存する先のパスを作成
        // XXX チャットルームの場合は、連続撮影が出来るので現在のモデルに入っているトータルカウントをファイル名につけて保存しておく
        NSString *outputPath = [[NSString alloc] initWithFormat:@"%@%@", NSTemporaryDirectory(), [NSString stringWithFormat:@"output%d.mp4", timeline.total]];
        NSURL *outputURL = [[NSURL alloc] initFileURLWithPath:outputPath];
        NSFileManager *fileManager = [NSFileManager defaultManager];
        if ([fileManager fileExistsAtPath:outputPath])
        {
            NSError *error;
            if ([fileManager removeItemAtPath:outputPath error:&error] == NO)
            {
                //上書きは基本できないので、あったら削除しないとダメ
            }
        }
        //録画開始
        timercount = 0;
        timer = [NSTimer scheduledTimerWithTimeInterval:0.1
                                                 target:self
                                               selector:@selector(timer:)
                                               userInfo:nil
                                                repeats:YES
                 ];
        [MovieFileOutput startRecordingToOutputFileURL:outputURL recordingDelegate:self];
        
    }
}

-(void) timer:(NSTimer *)timer
{
    
    timercount++;
    
    backgroundImageBlueIv.width = self.view.width*timercount/maxtime;
    
    if(timercount>=maxtime){
        [self StopRecording];
        
        //ここでサーバなげる。
        
    }
    
}

-(void)StopRecording
{
    if(WeAreRecording){
        WeAreRecording = NO;
        timercount = 0;
        [timer invalidate];
        timer = nil;
        [MovieFileOutput stopRecording];
        // XXX test code
        if(YES == [APPDELEGATE isSimulator]){
            // シミュレータの場合は、ここでサーバなげる。
            NSURL *outputFileURL = [NSURL fileURLWithPath:[[NSBundle mainBundle] pathForResource:@"test" ofType:@"mp4"]];
            NSURL *thmbnailImagePath = [self createThumbnailImageJPG:outputFileURL];
            if(nil != thmbnailImagePath){
                [self _postData:thmbnailImagePath :outputFileURL];
            }
            else{
                // XXX えら〜 (テストの場合だから実装は別に無くても構わない)
            }
        }
    }
}

- (void) CameraSetOutputProperties
{
    AVCaptureConnection *CaptureConnection = [MovieFileOutput connectionWithMediaType:AVMediaTypeVideo];
    
    if ([CaptureConnection isVideoOrientationSupported])
    {
        // XXX 縦向き動画
        [CaptureConnection setVideoOrientation:AVCaptureVideoOrientationPortrait];
    }
    
    //ここから下はお好みで/
    //    CMTimeShow(CaptureConnection.videoMinFrameDuration);
    //    CMTimeShow(CaptureConnection.videoMaxFrameDuration);
    //
    //    if (CaptureConnection.supportsVideoMinFrameDuration)
    //        CaptureConnection.videoMinFrameDuration = CMTimeMake(1, CAPTURE_FRAMES_PER_SECOND);
    //    if (CaptureConnection.supportsVideoMaxFrameDuration)
    //        CaptureConnection.videoMaxFrameDuration = CMTimeMake(1, CAPTURE_FRAMES_PER_SECOND);
    //
    //    CMTimeShow(CaptureConnection.videoMinFrameDuration);
    //    CMTimeShow(CaptureConnection.videoMaxFrameDuration);
}

- (void)captureOutput:(AVCaptureFileOutput *)captureOutput didFinishRecordingToOutputFileAtURL:(NSURL *)outputFileURL
      fromConnections:(NSArray *)connections
                error:(NSError *)error
{
    
    BOOL RecordedSuccessfully = YES;
    if ([error code] != noErr)
    {
        // A problem occurred: Find out if the recording was successful.
        id value = [[error userInfo] objectForKey:AVErrorRecordingSuccessfullyFinishedKey];
        if (value)
        {
            RecordedSuccessfully = [value boolValue];
        }
    }
    if (RecordedSuccessfully)
    {
        //書き込んだのは/tmp以下なのでカメラーロールの下に書き出す
        ALAssetsLibrary *library = [[ALAssetsLibrary alloc] init];
        if ([library videoAtPathIsCompatibleWithSavedPhotosAlbum:outputFileURL])
        {
            //ここでサーバなげる。
            if(YES != [APPDELEGATE isSimulator]){
                // XXX 第1引数にサムネイル画像のローカルファイルパス入れてね☆
                NSURL *thmbnailImagePath = [self createThumbnailImageJPG:outputFileURL];
                if(nil != thmbnailImagePath){
                    [self _postData:thmbnailImagePath :outputFileURL];
                    //                [library writeVideoAtPathToSavedPhotosAlbum:outputFileURL
                    //                                            completionBlock:^(NSURL *assetURL, NSError *error)
                    //                 {
                    //                     if (error)
                    //                     {
                    //
                    //                     }
                    //                 }];
                }
                else {
                    // XXX エラー！撮影しなおし！
                }
            }
        }
        
    }
}


- (void)_postData:(NSURL *)argMovieThumbnailImageURL :(NSURL *)argMovieMp4URL
{
    // ローディングを表示
    [APPDELEGATE showLoading];
    // 取り敢えずタイムラインを作っちゃう
    TimelineModel *timelineModel = [[TimelineModel alloc] init:PROTOCOL :DOMAIN_NAME :URL_BASE :COOKIE_TOKEN_NAME :SESSION_CRYPT_KEY :SESSION_CRYPT_IV :TIMEOUT];
    timelineModel.room_id = timeline.room_id;
    // 自分を投稿者に指定
    timelineModel.user_id = ownerID;
    // ローカルの動画サムネイルのローカルパスを渡してね☆
    timelineModel.thumbnail = [argMovieThumbnailImageURL description];
    timelineModel.thumbnailImageLocalPath = argMovieThumbnailImageURL;
    timelineModel.url = [argMovieMp4URL description];
    timelineModel.movieFileLocalPath = argMovieMp4URL;
    BOOL res = [timelineModel save:^(BOOL success, NSInteger statusCode, NSHTTPURLResponse *responseHeader, NSString *responseBody, NSError *error) {
        if(YES == success){
            // AppDelegateにMovieのアップロードとサムネイルのアップロードをやらせる
            [APPDELEGATE uploadMovie:timelineModel];
            // このタイムラインをローカルのタイムラインモデルで再描画
            [timeline insertObject:timelineModel :0];
            [self performSelector:@selector(redraw:) withObject:0 afterDelay:0.2f];
        }
        // ローディングを外す(正常にしろエラー終了にしろ)
        [APPDELEGATE hideLoading];
    }];
    if(NO == res){
        // ローディングを外す(エラー終了)
        [APPDELEGATE hideLoading];
        // 400 Bad Request
        [ModelBase showRequestError:400];
    }
}


#pragma mark - リストの再描画
- (void)redraw:(int)argIndex
{
    if(backgroundImageBlueIv.width > 0){
        [UIView animateWithDuration:0.5f animations:^{
            backgroundImageBlueIv.alpha = 0.0f;
        } completion:^(BOOL finished) {
            //
            backgroundImageBlueIv.width = 0;
            backgroundImageBlueIv.alpha = 1.0f;
        }];
    }
    [self.movietable reloadData];
    [self _changeIconAndFrame:argIndex];
}

//指定したUIColorでCGRectの大きさを塗り潰したUIImageを返す
- (UIImage *)imageWithColor:(UIColor *)color {
    
    CGRect rect = CGRectMake(0.0f, 0.0f, 1.0f, 1.0f);
    UIGraphicsBeginImageContext(rect.size);
    CGContextRef context = UIGraphicsGetCurrentContext();
    
    CGContextSetFillColorWithColor(context, [color CGColor]);
    CGContextFillRect(context, rect);
    
    UIImage *image = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    return image;
}

- (NSURL*)createThumbnailImageJPG:(NSURL*)argMovieFilePath
{
    AVURLAsset* asset = [[AVURLAsset alloc]initWithURL:argMovieFilePath options:nil];
    if ([asset tracksWithMediaCharacteristic:AVMediaTypeVideo]) {
        AVAssetImageGenerator *imageGen = [[AVAssetImageGenerator alloc] initWithAsset:asset];
        [imageGen setAppliesPreferredTrackTransform:YES];
        Float64 durationSeconds = CMTimeGetSeconds([asset duration]);
        CMTime midpoint = CMTimeMakeWithSeconds(durationSeconds/(maxtime/10), 600);
        NSError* error = nil;
        CMTime actualTime;
        CGImageRef halfWayImageRef = [imageGen copyCGImageAtTime:midpoint actualTime:&actualTime error:&error];
        if (halfWayImageRef != NULL) {
            UIImage* myImage = [[UIImage alloc]initWithCGImage:halfWayImageRef];
            CGImageRelease(halfWayImageRef);
            // JPEGで保存(0.8fはクオリティ-)
            NSData *imageData = [[NSData alloc] initWithData:UIImageJPEGRepresentation(myImage, 0.8f)];
            // tmpディレクトリに書き出す
            NSString *thumbnailImagePath = [[NSHomeDirectory() stringByAppendingPathComponent:@"tmp"] stringByAppendingPathComponent:@"thumbnail.jpg"];
            [imageData writeToFile:thumbnailImagePath atomically:YES];
            // 保存したファイルパスを返却
            return [NSURL fileURLWithPath:thumbnailImagePath];
        }
    }
    return nil;
}

@end
