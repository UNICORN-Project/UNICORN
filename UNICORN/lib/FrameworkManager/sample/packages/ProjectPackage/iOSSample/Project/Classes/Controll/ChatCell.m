//
//  ABCell.m
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "ChatCell.h"

@implementation ChatCell
{
    UIImageView *frameView;
    int maxtime;
    BOOL playing;
    int likenum;
}

@synthesize thumbImageView, player, parentViewController,indexpath,lblLike,likeButton;

- (id)init:(UITableViewCellStyle)style :(NSString *)reuseIdentifier :(NSURL *)argMovieURL :(NSIndexPath *)index :(NSString *)like;
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        parentViewController = nil;
        indexpath = index;
        maxtime = 60;
        // 再生中フラグ
        playing = NO;
        likenum = [like intValue];

        // セルのコンテンツを横向きに設定
        self.contentView.transform = CGAffineTransformMakeRotation(-M_PI / 2);
        
        player = [[MPMoviePlayerController alloc] initWithContentURL:argMovieURL];
        player.view.frame = CGRectMake(0, -64, 280, 480);
        [self.contentView addSubview:player.view];

        thumbImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, -64, 480/1.72, 640/1.72)];
        [self.contentView addSubview:thumbImageView];

        if(YES != [[argMovieURL description] hasPrefix:@"http"]){
            // ムービーがローカルパスの時はサムネイルを作って表示する
            [thumbImageView setImage:[UIImage imageWithData:[self createThumbnailImageJPG:argMovieURL]]];
        }

        frameView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"MASK_movie"]];
        frameView.x = 0;
        frameView.y =-64;
        frameView.height = 640;
        [self.contentView addSubview:frameView];

        UIImage *btnimg = [UIImage imageNamed:@"UI_playbtn"];
        //ボタンのインスタンスを作成します。
        UIButton *button = [[UIButton alloc] init];
        //ボタンに画像を設定します。
        [button setBackgroundImage:btnimg forState:UIControlStateNormal];
        //表示するフレームを設定します。
        button.frame = CGRectMake(140 - btnimg.size.width/2, 175 - btnimg.size.height/2, btnimg.size.width, btnimg.size.height);
        //ビューへ貼り付けます。
        [button addTarget:self action:@selector(onPushPlayButton:) forControlEvents:UIControlEventTouchUpInside];

        [self.contentView addSubview:button];
        
        UIImage *likeBtnimg;
        if(likenum == 0){
            likeBtnimg = [UIImage imageNamed:@"UI_likebtn_off"];
        }else{
            likeBtnimg = [UIImage imageNamed:@"UI_likebtn_on"];
        }
        //ボタンのインスタンスを作成します。
        likeButton = [[UIButton alloc] init];
        //ボタンに画像を設定します。
        [likeButton setBackgroundImage:likeBtnimg forState:UIControlStateNormal];
        //表示するフレームを設定します。
        likeButton.frame = CGRectMake(thumbImageView.frame.origin.x + thumbImageView.frame.size.width - likeBtnimg.size.width -10, thumbImageView.frame.origin.y + thumbImageView.frame.size.height - likeBtnimg.size.height -10, likeBtnimg.size.width, likeBtnimg.size.height);
        //ビューへ貼り付けます。
        [likeButton addTarget:self action:@selector(onPushLikeButton:) forControlEvents:UIControlEventTouchUpInside];
        
        [self.contentView addSubview:likeButton];
        
        lblLike = [[UILabel alloc] initWithFrame:CGRectMake(likeButton.frame.origin.x,likeButton.frame.origin.y + likeButton.frame.size.height/2 + 3,likeButton.frame.size.width,likeButton.frame.size.height/2 -6)];
        lblLike.backgroundColor = [UIColor clearColor];
        lblLike.textColor = [UIColor colorWithRed:104.0/255.0 green:209.0/255.0 blue:219.0/255.0 alpha:1];
        lblLike.font = [UIFont boldSystemFontOfSize:14];
        lblLike.textAlignment = NSTextAlignmentCenter;
        lblLike.text = [NSString stringWithFormat:@"%d",likenum];
        
        [self.contentView addSubview:lblLike];

    }
    return self;
}

- (void)dealloc
{
    //
    NSLog(@"cell dealloc!");
    [player stop];
    player = nil;
}

- (void)onPushPlayButton:(id)sender
{
    if(NO == playing){
        playing = YES;
        thumbImageView.hidden = YES;
        UIButton *button = (UIButton *)sender;
        [button setBackgroundImage:[self _createTransparencyImage] forState:UIControlStateNormal];
        player.repeatMode = MPMovieRepeatModeOne;
        [player prepareToPlay];
        parentViewController.nowPlayngPlayer = player;
    }
    else {
        playing = NO;
        thumbImageView.hidden = NO;
        UIButton *button = (UIButton *)sender;
        [button setBackgroundImage:[UIImage imageNamed:@"UI_playbtn"] forState:UIControlStateNormal];
        [player stop];
        parentViewController.nowPlayngPlayer = nil;
    }
}

- (void)onPushLikeButton:(id)sender
{
    if(likenum == 0){
        [likeButton setBackgroundImage:[UIImage imageNamed:@"UI_likebtn_on"] forState:UIControlStateNormal];
    }
    likenum++;
    lblLike.text = [NSString stringWithFormat:@"%d",likenum];
    [parentViewController onPushLikeButton:indexpath];
}

- (UIImage *)_createTransparencyImage;
{
    static UIImage *img;
    if(nil == img){
        CGRect rect = CGRectMake(0, 0, 1, 1);
        UIGraphicsBeginImageContext(rect.size);
        CGContextRef contextRef = UIGraphicsGetCurrentContext();
        CGContextSetFillColorWithColor(contextRef, [RGBA(1, 1, 1, 0) CGColor]);
        CGContextFillRect(contextRef, rect);
        img = UIGraphicsGetImageFromCurrentImageContext();
        UIGraphicsEndImageContext();
    }
    return img;
};

- (NSData*)createThumbnailImageJPG:(NSURL*)argMovieFilePath
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
            return imageData;
        }
    }
    return nil;
}

@end
