//
//  MainViewController.m
//  Withly
//
//  Created by n00886 on 2012/10/30.
//  Copyright (c) 2012年 n00886. All rights reserved.
//

#import "MainViewController.h"
//#import "ChatRoomViewController.h"

@interface MainViewController ()
{
    int dataCnt;
    UITableView *dataListView;
    EGORefreshTableHeaderView *_refreshHeaderView;
    UIImageView *headerImageView;
    BOOL _loading;
    SampleModel *data;
}
@end

@implementation MainViewController

- (id)init
{
    self = [super init];
    if(self != nil){
        _loading = NO;
        dataCnt = 0;
        data = [[SampleModel alloc] init:PROTOCOL :DOMAIN_NAME :URL_BASE :COOKIE_TOKEN_NAME :SESSION_CRYPT_KEY :SESSION_CRYPT_IV :TIMEOUT];
    }
    return self;
}

- (void)loadView
{
    [super loadView];

    // TableView
    dataListView = [[UITableView alloc] init];
    // フレーム
    dataListView.frame = CGRectMake(0, self.navigationController.navigationBar.frame.size.height + 20, self.view.width, self.view.height - self.navigationController.navigationBar.frame.size.height);
    dataListView.delegate = self;
    dataListView.dataSource = self;
    dataListView.backgroundColor = [UIColor clearColor];
    dataListView.separatorStyle = UITableViewCellSeparatorStyleSingleLine;
    dataListView.scrollsToTop = YES;
    dataListView.contentInset = UIEdgeInsetsMake(-64.0, 0.0, 0.0, 0.0);
    // TableHeaderView
    UIImage* headerImage = [self imageWithColor:[UIColor colorWithRed:255.0/255.0 green:40.0/255.0 blue:140.0/255.0 alpha:1]];
    UIView *headerView = [[UIView alloc] initWithFrame:CGRectMake(0, 0, dataListView.bounds.size.width, 0)];
    
    headerImageView = [[UIImageView alloc] init];
    headerImageView.frame = CGRectMake(0, headerView.frame.size.height - headerImage.size.height, headerImage.size.width, headerImage.size.height);
    headerImageView.image = headerImage;
    headerImageView.alpha = 0.0;
    [headerView addSubview:headerImageView];
    headerView.backgroundColor = [UIColor clearColor];
    
    dataListView.tableHeaderView = headerView;
    
    // PullDownToRefresh
    _refreshHeaderView = [[EGORefreshTableHeaderView alloc] initWithFrame:CGRectMake(0.0f, 0.0f - dataListView.bounds.size.height -  headerView.frame.size.height, self.view.frame.size.width, dataListView.bounds.size.height + headerView.frame.size.height)];
    _refreshHeaderView.delegate = self;
    _refreshHeaderView.backgroundColor = RGBA(255, 255, 255, 1);
    [dataListView addSubview:_refreshHeaderView];

    [self.view addSubview:dataListView];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
//    if(nil == [ModelBase loadIdentifier:SESSION_CRYPT_KEY :SESSION_CRYPT_IV]){
//        // 滞在カウンタ 1.5秒滞在
//        [self performSelector:@selector(showAddressViewController) withObject:nil afterDelay:0.05f];
//    }
    // データをロード
    [self dataListLoad];
}

/* 描画(再描画)が走る直前に呼ばれるので、その度に処理したい事を追加 */
- (void)viewWillAppear:(BOOL)animated
{
    MainNavigationBarView *mainNavigationBarView = (MainNavigationBarView *)[self.navigationController.navigationBar viewWithTag:MainNavigationBarViewTag];
    if(nil != mainNavigationBarView){
        [mainNavigationBarView setTile:@"サンプル"];
    }
    else {
        // ナビゲーションバーにタイトルをセット(毎回addしないと、navigationBarにaddしたViewシステムに何故か消される)
        [self.navigationController.navigationBar addSubview:[[MainNavigationBarView alloc] initWithFrame:self.navigationController.navigationBar.frame andTitle:@"サンプル"]];
    }
    // ナビゲーションバーのボタンはナビゲーションして戻ってくるとけされてしまったままになるので、ここで描画毎に追加する
    self.navigationItem.rightBarButtonItem = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"UI_icon_chatplus"] target:self action:@selector(showAddressViewController)];
}

- (void)showAddressViewController
{
//    ABInviteViewController *abInviteViewController = [[ABInviteViewController alloc] init];
//    UINavigationController *navigationController = [[UINavigationController alloc] initWithRootViewController:abInviteViewController];
//    navigationController.navigationBar.barStyle = UIBarStyleBlack;
//    navigationController.navigationBar.translucent = YES;
//    [navigationController.navigationBar addSubview:[self.navigationController.navigationBar viewWithTag:MainNavigationBarViewTag]];
//    abInviteViewController.modalTransitionStyle = UIModalTransitionStyleCoverVertical;
//    [self presentViewController:navigationController animated:modalAnimated completion:nil];
//    // 初回以降はアニメーションを有効にする
//    modalAnimated = YES;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)dataListLoad
{
    // 配列参照
    // XXX LIMIT OFFSETを指定するときはqueryメソッド使ってね☆
    [data list:^(BOOL success, NSInteger statusCode, NSHTTPURLResponse *responseHeader, NSString *responseBody, NSError *error) {
        if(YES == success){
            // 正常終了時 テーブルView Refresh
            [dataListView reloadData];
        }
        else {
            // エラー処理をするならココ
        }
        // Pull to Refleshを止める
        _loading = NO;
        [_refreshHeaderView egoRefreshScrollViewDataSourceDidFinishedLoading:dataListView];
    }];
}

#pragma mark TableView Delegate

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 50;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    if (0 < data.total) {
        return data.total;
    }
    // デフォルトのEmptyRoom表示用
    return 1;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:[NSString stringWithFormat:@"Identifier-%d-%d", (int) indexPath.section, (int)indexPath.row]];
    if(0 < data.total){
        SampleModel *rowdata = [data objectAtIndex:(int)indexPath.row];
        cell.textLabel.text = rowdata.name;
    }
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    // セルの選択を外す
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    if(0 < data.total){
        // ローディングを表示
        [APPDELEGATE showLoading];

        // チャットルームにdataIDとタイトルを渡してナビゲーション遷移する
        NSLog(@"indexPath %@", indexPath);
        NSLog(@"indexPath %ld", (long)indexPath.row);
//        SampleModel *rowdata = [data objectAtIndex:(int)indexPath.row];
//        [self.navigationController pushViewController:[[ChatRoomViewController alloc] init:rowdata.ID :rowdata.name :rowdata.owner_id] animated:YES];
    }
}


#pragma mark - UIScrollViewDelegate Methods

- (void)scrollViewDidScroll:(UIScrollView *)scrollView
{
    if (0 > scrollView.contentOffset.y) {
        [UIView animateWithDuration:0.2
                              delay:0.0
                            options: UIViewAnimationOptionCurveEaseIn | UIViewAnimationOptionAllowUserInteraction
                         animations:^{
                             headerImageView.alpha = 1.0;
                         }
                         completion:^(BOOL finished){
                             nil;
                         }];
        
    } else {
        [UIView animateWithDuration:0.2
                              delay:0.0
                            options: UIViewAnimationOptionCurveEaseOut
                         animations:^{
                             headerImageView.alpha = 0.0;
                         }
                         completion:^(BOOL finished){
                             nil;
                         }];
    }
    
	[_refreshHeaderView egoRefreshScrollViewDidScroll:scrollView];
}

- (void)scrollViewDidEndDragging:(UIScrollView *)scrollView willDecelerate:(BOOL)decelerate
{
    [_refreshHeaderView egoRefreshScrollViewDidEndDragging:scrollView];
}


#pragma mark - EGORefreshTableHeaderDelegate Methods

- (void)egoRefreshTableHeaderDidTriggerRefresh:(EGORefreshTableHeaderView*)view
{
    // テーブルView Refresh
    _loading = YES;
    [self dataListLoad];
}

- (BOOL)egoRefreshTableHeaderDataSourceIsLoading:(EGORefreshTableHeaderView*)view
{
	return _loading; // should return if data source model is reloading
}

- (NSDate*)egoRefreshTableHeaderDataSourceLastUpdated:(EGORefreshTableHeaderView*)view
{
	return [NSDate date]; // should return date data source was last changed
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

@end
