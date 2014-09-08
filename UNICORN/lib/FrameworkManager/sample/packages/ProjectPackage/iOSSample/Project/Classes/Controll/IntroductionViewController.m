//
//  IntroductionViewController.m
//  Withly
//
//  Created by n00886 on 2012/10/30.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import "IntroductionViewController.h"

@interface IntroductionViewController ()
{
    UIScrollView *scrollView;
    UIPageControl *pageControl;
}
@end

@implementation IntroductionViewController

- (void)loadView
{
    [super loadView];

    // 透明にしておく
    self.view.alpha = 0.0f;

    NSInteger pageSize = 4; // ページ数
    CGFloat width = self.view.bounds.size.width;
    CGFloat height = self.view.bounds.size.height;

    // UIScrollViewのインスタンス化
    scrollView = [[UIScrollView alloc]init];
    scrollView.frame = self.view.bounds;
    scrollView.backgroundColor = [UIColor whiteColor];

    // 横スクロールのインジケータを非表示にする
    scrollView.showsHorizontalScrollIndicator = NO;

    // ページングを有効にする
    scrollView.pagingEnabled = YES;

    scrollView.userInteractionEnabled = YES;
    scrollView.delegate = self;

    // スクロールの範囲を設定
    [scrollView setContentSize:CGSizeMake((pageSize * width), height)];

    // スクロールビューを貼付ける
    [self.view addSubview:scrollView];

    UIImage *backgroundImage = [UIImage imageNamed:@"IMG_discription01"];
    UIImageView *backgroundImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, 0, width, height)];
    backgroundImageView.image = backgroundImage;
    [scrollView addSubview:backgroundImageView];

    for (int i = 1; i < pageSize-1; i++) {
        UILabel *label = [[UILabel alloc]initWithFrame:CGRectMake(i * width, 0, width, height)];
        label.text = [NSString stringWithFormat:@"%d", i + 1];
        label.font = [UIFont fontWithName:@"Arial" size:92];
        label.backgroundColor = [UIColor whiteColor];
        label.textAlignment = NSTextAlignmentCenter;
        [scrollView addSubview:label];
    }

    backgroundImage = [UIImage imageNamed:@"IMG_discription04"];
    backgroundImageView = [[UIImageView alloc] initWithFrame:CGRectMake(3 * width, 0, width, height)];
    backgroundImageView.image = backgroundImage;
    [scrollView addSubview:backgroundImageView];

    UILabel *label4 = [[UILabel alloc]initWithFrame:CGRectMake(3* width, 30, width, 70)];
    label4.text = @"携帯電話へのアクセス";
    label4.textColor = [UIColor redColor];
    label4.font = [UIFont fontWithName:@"Arial" size:22];
    label4.textAlignment = NSTextAlignmentCenter;
    [scrollView addSubview:label4];
    
    UILabel *label4_2 = [[UILabel alloc]initWithFrame:CGRectMake(3* width, 310, width, 120)];
    label4_2.text = @"パーミッション取得、とるよ！了承して\nね！についての説明が入ります。パーミッ\nション取得、とるよ！了承してね！につい\nての説明が入ります。パーミッション取";
    label4_2.font = [UIFont fontWithName:@"Arial" size:15];
    label4_2.textAlignment = NSTextAlignmentCenter;
    label4_2.numberOfLines = 4;
    [scrollView addSubview:label4_2];
    
    UIButton *agreeBtn = [UIButton buttonWithType:UIButtonTypeRoundedRect];
    agreeBtn.frame = CGRectMake(3* width, height - 75, width, 75);
    agreeBtn.backgroundColor = [UIColor redColor];
    [agreeBtn setTitle:@"同意してはじめる" forState:UIControlStateNormal];
    [agreeBtn setTitle:@"同意してはじめる" forState:UIControlStateHighlighted];
    [agreeBtn setTitle:@"同意してはじめる" forState:UIControlStateDisabled];
    [agreeBtn setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
    [agreeBtn setTitleColor:[UIColor whiteColor] forState:UIControlStateHighlighted];
    [agreeBtn setTitleColor:[UIColor whiteColor] forState:UIControlStateDisabled];
    [agreeBtn addTarget:self action:@selector(agree:)forControlEvents:UIControlEventTouchDown];
    [self.view addSubview:agreeBtn];
    
    [scrollView addSubview:agreeBtn];
    
    // ページコントロールのインスタンス化
    CGFloat x = (width - 300) / 2;
    pageControl = [[UIPageControl alloc]initWithFrame:CGRectMake(x,80, 300, 50)];
    
    // 背景色を設定
    UIColor *pageControlbackgoundColor = [UIColor whiteColor];
    UIColor *acolor = [pageControlbackgoundColor colorWithAlphaComponent:0]; //透過率50%
    pageControl.backgroundColor = acolor;
    // デフォルトの色
    pageControl.pageIndicatorTintColor = [[UIColor grayColor] colorWithAlphaComponent:0.5];
    // 選択されてるページを現す色
    pageControl.currentPageIndicatorTintColor = [UIColor redColor];
    
    // ページ数を設定
    pageControl.numberOfPages = pageSize;
    
    // 現在のページを設定
    pageControl.currentPage = 0;
    
    // ページコントロールをタップされたときに呼ばれるメソッドを設定
    pageControl.userInteractionEnabled = YES;
    [pageControl addTarget:self
                    action:@selector(pageControl_Tapped:)
          forControlEvents:UIControlEventValueChanged];
    
    // ページコントロールを貼付ける
    [self.view addSubview:pageControl];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    [UIView animateKeyframesWithDuration:0.5f
                                   delay:0.0f
                                 options: UIViewKeyframeAnimationOptionAllowUserInteraction
                              animations:^{
                                  self.view.alpha = 1.0f;
                              }
                              completion:^(BOOL finished){
                              }];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

/**
 * スクロールビューがスワイプされたとき
 * @attention UIScrollViewのデリゲートメソッド
 */
- (void)scrollViewDidScroll:(UIScrollView *)_scrollView
{
    CGFloat pageWidth = scrollView.frame.size.width;
    if ((NSInteger)fmod(scrollView.contentOffset.x , pageWidth) == 0) {
        // ページコントロールに現在のページを設定
        pageControl.currentPage = scrollView.contentOffset.x / pageWidth;
    }
}

/**
 * ページコントロールがタップされたとき
 */
- (void)pageControl_Tapped:(id)sender
{
    CGRect frame = scrollView.frame;
    frame.origin.x = frame.size.width * pageControl.currentPage;
    [scrollView scrollRectToVisible:frame animated:YES];
}

/**
 * agreeボタンタップイベント
 */
- (void)agree:(UIButton*)button
{
    [APPDELEGATE setMainViewController];
}


@end
