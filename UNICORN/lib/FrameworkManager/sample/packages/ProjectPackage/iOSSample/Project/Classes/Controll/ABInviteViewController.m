//
//  ABInviteViewController.m
//  Withly
//
//  Created by ThinhHQ on 7/23/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "ABInviteViewController.h"
#import "ContactCell.h"
#import "EmptyContactView.h"
#import "CameraViewController.h"
#import <AddressBook/AddressBook.h>

#define LIST_ADDRESS_POSITION_X		              0
#define LIST_ADDRESS_POSITION_Y                   65
#define TITLE_HEIGHT                   			  10

// フォントサイズ
#define LABEL1_FONT_SIZE 12

@interface ABInviteViewController ()
{
    NSIndexPath* lastIndexPath;
    // XXX ParsonとFriendModelの結果両方setValueしてね☆
    FriendModel *friendData;
    NSMutableDictionary* selectedData;
    UIButton *shotBtn;
    UIView *addressView;
    UIView *friendView;
    int selectedcount;
    int section;// 0=friend(FriendModel),1=address(Parson)
    
}
@end

@implementation ABInviteViewController

@synthesize tableData, addressList,friendList, albumID, isFlowGroupEdit,segmentedControl;

- (id)init
{
    self = [super init];
    if (nil != self) {
        // タッチを有効にする
        self.view.userInteractionEnabled = YES;
        // マルチタッチを有効にする
        [self.view setMultipleTouchEnabled:YES];
        
        friendData = [[FriendModel alloc] init:PROTOCOL :DOMAIN_NAME :URL_BASE :COOKIE_TOKEN_NAME :SESSION_CRYPT_KEY :SESSION_CRYPT_IV :TIMEOUT];
        
        selectedData = [[NSMutableDictionary alloc] init];
        // XXX 今はParsonの一覧しかないのでDefault1
        section = 1;
    }
    return self;
}

- (void)loadView
{
    [super loadView];
    
    CGFloat width = self.view.bounds.size.width;
    CGFloat height = self.view.bounds.size.height;
 
    indexPathSelected = nil;

    NSArray *arr = [NSArray arrayWithObjects:@"Sample", @"Contact", nil];
    segmentedControl = [[UISegmentedControl alloc] initWithItems:arr];
    segmentedControl.frame = CGRectMake(10,74, width - 20, 40);
    // セグメントの選択が変更されたときに呼ばれるメソッドを設定
    [segmentedControl addTarget:self
                action:@selector(segment_ValueChanged:)
      forControlEvents:UIControlEventValueChanged];
    
    [self.view addSubview:segmentedControl];
    
    addressView = [[UIView alloc]initWithFrame:CGRectMake(0, 124, width, height)];
    friendView = [[UIView alloc]initWithFrame:CGRectMake(0, 124, width, height)];
    
    //Searchbar
    friendsSearchBar = [[UISearchBar alloc] initWithFrame:CGRectMake(10, 0, width - 20, 50)];
    friendsSearchBar.delegate= self;
    friendsSearchBar.placeholder = @"名前で検索";
    [friendView addSubview:friendsSearchBar];
    
    addressSearchBar = [[UISearchBar alloc] initWithFrame:CGRectMake(10,0, width - 20, 50)];
    addressSearchBar.delegate= self;
    addressSearchBar.placeholder = @"名前で検索";
    [addressView addSubview:addressSearchBar];
    
    self.addressList = [[UITableView alloc] initWithFrame:CGRectMake(0, addressSearchBar.frame.origin.y + addressSearchBar.frame.size.height, width, height-addressSearchBar.frame.origin.y - addressSearchBar.frame.size.height)];
    self.addressList.delegate = self;
    self.addressList.dataSource = self;
    self.addressList.backgroundColor = [UIColor clearColor];
    self.addressList.separatorStyle = UITableViewCellSeparatorStyleSingleLine;
    self.addressList.scrollsToTop = YES;
    [self.addressList setSeparatorInset:UIEdgeInsetsZero];
    
    [addressView addSubview:addressList];
    
    self.friendList = [[UITableView alloc] initWithFrame:CGRectMake(0, friendsSearchBar.frame.origin.y + friendsSearchBar.frame.size.height, width, height-friendsSearchBar.frame.origin.y - friendsSearchBar.frame.size.height)];
    self.friendList.delegate = self;
    self.friendList.dataSource = self;
    self.friendList.backgroundColor = [UIColor clearColor];
    self.friendList.separatorStyle = UITableViewCellSeparatorStyleSingleLine;
    self.friendList.scrollsToTop = YES;
    [self.friendList setSeparatorInset:UIEdgeInsetsZero];
    
    [friendView addSubview:friendList];
    
    addressView.hidden = NO;
    friendView.hidden = YES;
    
    [self.view addSubview:addressView];
    [self.view addSubview:friendView];
    
    UIImage* frameShot = [UIImage imageNamed:@"IMG_bg_toshotbtn"];
    UIImageView* frameShotIv = [[UIImageView alloc] initWithFrame:CGRectMake(0, height - frameShot.size.height, frameShot.size.width, frameShot.size.height)];
    frameShotIv.image = frameShot;
    [self.view addSubview:frameShotIv];
    
    UIImage* shotImage = [UIImage imageNamed:@"UI_btn_toshot_on"];
    shotBtn = [[UIButton alloc] init];
    [shotBtn setBackgroundImage:shotImage forState:UIControlStateNormal];
    shotBtn.frame = CGRectMake((width- shotImage.size.width)/2, height - (frameShot.size.height + shotImage.size.height)/2, shotImage.size.width, shotImage.size.height);
    [shotBtn addTarget:self action:@selector(onPushShotButton:)forControlEvents:UIControlEventTouchDown];
    shotBtn.enabled = NO;
    [self.view addSubview:shotBtn];
    
}

/* 描画(再描画)が走る直前に呼ばれるので、その度に処理したい事を追加 */
-(void) viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
    self.tableData = [[NSMutableArray alloc] init];
    lastIndexPath = nil;
    selectedcount = 0;
    //NSLog(@"ddd %@", nameSelected );
    [self checkStatus];
    // ナビゲーションバーにタイトルをセット(毎回addしないと、navigationBarにaddしたViewシステムに何故か消される)
    [self.navigationController.navigationBar addSubview:[[MainNavigationBarView alloc] initWithFrame:self.navigationController.navigationBar.frame andTitle:@"連絡帳"]];
    // ナビゲーションバーのボタンはナビゲーションして戻ってくるとけされてしまったままになるので、ここで描画毎に追加する
    UIBarButtonItem* leftBarButtonItem = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemCancel target:self action:@selector(onPushCloseButton:)];
    [leftBarButtonItem setTitle:@"キャンセル"];
    self.navigationItem.leftBarButtonItem = leftBarButtonItem;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // モーダル直後は友達一覧なので一覧データをロードしてしまう
    // 滞在カウンタ 1.5秒滞在
    [self performSelector:@selector(friendListLoad) withObject:nil afterDelay:0.05f];
}

- (void)friendListLoad
{
    // friendmodelの読み込み
    BOOL res = [friendData list:^(BOOL success, NSInteger statusCode, NSHTTPURLResponse *responseHeader, NSString *responseBody, NSError *error) {
        if (YES == success) {
            
            
            
        }
        [APPDELEGATE hideLoading];
    }];
    if(NO == res){
        // 読み込みエラー
        [ModelBase showRequestError:400];
        [APPDELEGATE hideLoading];
    }
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Properties
- (void)setDisplayedFriends:(NSMutableArray *) listOfFriends
{
    if ([tableData count] >0) {
        tableData = nil;
    }
    tableData = listOfFriends ;
    
    [addressList reloadData];
}

#pragma mark Function

- (void) checkStatus
{
    if ([[UIDevice currentDevice].systemVersion floatValue] >= 6.0) {
        ABAddressBookRef addressBookRef = ABAddressBookCreateWithOptions(NULL, NULL);
        
        if (ABAddressBookGetAuthorizationStatus() == kABAuthorizationStatusNotDetermined) {
            ABAddressBookRequestAccessWithCompletion(addressBookRef, ^(bool granted, CFErrorRef error) {
                // First time access has been granted, add the contact
                if(granted){
                    [self getPersonOutOfAddressBook];
                }else{
                    CGFloat width = self.view.bounds.size.width;
                    CGFloat height = self.view.bounds.size.height;
                    [self.addressList removeFromSuperview];
                    [friendsSearchBar removeFromSuperview];
                    EmptyContactView *emptyView = [[EmptyContactView alloc] initWithFrame:CGRectMake(0, 0, width,height)];
                    [self.view addSubview:emptyView];
                }
            });
        }
        else if (ABAddressBookGetAuthorizationStatus() == kABAuthorizationStatusAuthorized) {
            // The user has previously given access, add the contact
            [self getPersonOutOfAddressBook];
        }
        else {
            // The user has previously denied access
            // Send an alert telling user to change privacy setting in settings app
            CGFloat width = self.view.bounds.size.width;
            CGFloat height = self.view.bounds.size.height;
            [self.addressList removeFromSuperview];
            [friendsSearchBar removeFromSuperview];
            EmptyContactView *emptyView = [[EmptyContactView alloc] initWithFrame:CGRectMake(0, 0, width,height)];
            [self.view addSubview:emptyView];
        }
    }
    else
        [self getPersonOutOfAddressBook];
}

- (void)getPersonOutOfAddressBook
{
    CFErrorRef error = NULL;

    ABAddressBookRef addressBook;

    addressBook = ABAddressBookCreateWithOptions(NULL, &error);

    if (addressBook != nil)
    {
        NSArray *allContacts = (__bridge_transfer NSArray *)ABAddressBookCopyArrayOfAllPeople(addressBook);
        
        NSUInteger i = 0;
        for (i = 0; i < [allContacts count]; i++)
        {
            Person *person = [[Person alloc] init];
            
            ABRecordRef contactPerson = (__bridge ABRecordRef)allContacts[i];
            
            //email
            ABMultiValueRef emails = ABRecordCopyValue(contactPerson, kABPersonEmailProperty);
            NSUInteger j = 0;
            if (ABMultiValueGetCount(emails) != 0) {
            	person.arrEmail = [[NSMutableArray alloc] initWithCapacity:ABMultiValueGetCount(emails)];
            	for (j = 0; j < ABMultiValueGetCount(emails); j++)
            	{
            	    NSString *email = (__bridge_transfer NSString *)ABMultiValueCopyValueAtIndex(emails, j);
                    NSString *emailLabel = (__bridge_transfer NSString *)(ABMultiValueCopyLabelAtIndex(emails, j));
                    NSMutableDictionary *emailDic = [[NSMutableDictionary alloc] init];
                    [emailDic setValue:email forKey:@"email"];
                    [emailDic setValue:emailLabel forKey:@"label"];
                    [person.arrEmail addObject:emailDic];
                }
            }
			//phone
            ABMultiValueRef phones = ABRecordCopyValue(contactPerson, kABPersonPhoneProperty);
			if (ABMultiValueGetCount(phones) != 0) {
            	person.arrPhone = [[NSMutableArray alloc] initWithCapacity:ABMultiValueGetCount(phones)];
            	for (j = 0; j < ABMultiValueGetCount(phones); j++)
            	{
            	    NSString *phone = (__bridge_transfer NSString *)ABMultiValueCopyValueAtIndex(phones, j);
                    NSString *phoneLabel = (__bridge_transfer NSString *)(ABMultiValueCopyLabelAtIndex(phones, j));
                    NSMutableDictionary *phoneDic = [[NSMutableDictionary alloc] init];
                    [phoneDic setValue:phone forKey:@"phone"];
                    [phoneDic setValue:phoneLabel forKey:@"label"];
            	    [person.arrPhone addObject:phoneDic];
                }
            }
            
            //contact info
            NSString *firstName = (__bridge_transfer NSString *)ABRecordCopyValue(contactPerson, kABPersonFirstNameProperty);
            NSString *lastName =  (__bridge_transfer NSString *)ABRecordCopyValue(contactPerson, kABPersonLastNameProperty);
            if(nil == firstName){
                firstName = @"";
            }
            if(nil == lastName){
                lastName = @"";
            }
            // XXX グローバル対応は必ずココを変える事！！
            NSString *fullName = [NSString stringWithFormat:@"%@ %@", lastName, firstName];
            // 名字ふりがな
            NSString *firstNamePhonetic = (__bridge_transfer NSString *)ABRecordCopyValue(contactPerson, kABPersonFirstNamePhoneticProperty);
            // 名前ふりがな
            NSString *lastNamePhonetic = (__bridge_transfer NSString *)ABRecordCopyValue(contactPerson, kABPersonLastNamePhoneticProperty);
            
            person.firstName = firstName;
            person.lastName = lastName;
            person.firstNamePhonetic = firstNamePhonetic;
            person.lastNamePhonetic = lastNamePhonetic;
            
            if (firstName == nil && lastName == nil) {
                if (person.arrEmail > 0) {
                	fullName = [[person.arrEmail objectAtIndex:0]objectForKey:@"email"];
                }
                if (person.arrPhone > 0) {
                	fullName = (NSString*)[person.arrPhone objectAtIndex:0];
                }
            }
            
            person.fullName = fullName;
            person.flag = 0;
            person.image = (__bridge_transfer NSData *) ABPersonCopyImageData(contactPerson);
            
            if (ABMultiValueGetCount(emails) != 0 || ABMultiValueGetCount(phones) != 0) {
                [self.tableData addObject:person];
            }
        }
        [self setDisplayedFriends:self.tableData];
    }
    
    CFRelease(addressBook);
}

/**
 * セグメントの選択が変更されたとき
 * @param sender セグメント
 */
- (void)segment_ValueChanged:(id)sender
{
    switch (segmentedControl.selectedSegmentIndex) {
        case 0: // Sample
            addressView.hidden = YES;
            friendView.hidden = NO;
            break;
            
        case 1: // Contact
            addressView.hidden = NO;
            friendView.hidden = YES;
            break;
            
        default:
            break;
    }
    
    [addressSearchBar resignFirstResponder];
    [friendsSearchBar resignFirstResponder];
}

-(void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event {
    [addressSearchBar resignFirstResponder];
    [friendsSearchBar resignFirstResponder];
    
}

#pragma mark Action

- (void)onPushCloseButton :(id)sender
{
    [self.navigationController dismissViewControllerAnimated:YES completion:nil];
}

#pragma mark TableView Delegate

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return TABLE_VIEW_HEIGHT_FOR_ROW;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    if (isFiltered) {
        return [arrfiltered count];
    }
    
    return [self.tableData count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *cellIdentifier = @"Identifier";
    
    ContactCell *cell;
    
    if (cell == nil) {
        cell = [[ContactCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:cellIdentifier];
    }
    Person *person;
    if (isFiltered)
    	person = [arrfiltered objectAtIndex:indexPath.row];
    else
    	person = [self.tableData objectAtIndex:indexPath.row];
    
    cell.lblName.text = person.fullName;
    
    cell.imgUser.image = [PublicFunction maskImage:[UIImage imageNamed:@"IMG_thumbnailnull"] maskImage:[UIImage imageNamed:@"common_icon_profile_mask"]];
    if (person.image != nil) {
        cell.imgUser.image = [PublicFunction maskImage:[UIImage imageWithData:person.image] maskImage:[UIImage imageNamed:@"common_icon_profile_mask"]];
        
    }
    if (person.flag == 0){
        [cell.radioBt setImage:[UIImage imageNamed:@"UI_checkbox_off"] forState:UIControlStateNormal];
    }
    else {
        [cell.radioBt setImage:[UIImage imageNamed:@"UI_checkbox_on"] forState:UIControlStateNormal];
    }
    
    cell.radioBt.tag = 1000+indexPath.row;
    
    if(!isFiltered) {
        if ([indexPath isEqual: indexPathSelected]) {
            [cell.radioBt setImage:[UIImage imageNamed:@"UI_checkbox_on"] forState:UIControlStateNormal];
        }
    }
    
    [addressSearchBar resignFirstResponder];
    [friendsSearchBar resignFirstResponder];
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    NSLog(@"indexPath %@", indexPath);
    NSLog(@"indexPath %ld", (long)indexPath.row);
    
    [addressSearchBar resignFirstResponder];
    [friendsSearchBar resignFirstResponder];
    
    lastIndexPath = indexPath;
    
    Person *person;
    if (isFiltered)
        person = [arrfiltered objectAtIndex:indexPath.row];
    else
        person = [self.tableData objectAtIndex:indexPath.row];
    
    //Person *person = [self.tableData objectAtIndex:indexPath.row];
    
    if(person.flag == 0){
        
        if(1<person.arrPhone.count){
            UIActionSheet *as = [[UIActionSheet alloc] init];
            as.delegate = self;
            as.title = @"連絡先が複数あります。";
            
            for(int i=0;i<person.arrPhone.count;i++){
                [as addButtonWithTitle:[(NSDictionary*)[person.arrPhone objectAtIndex:i] objectForKey:@"phone"]];
            }
            
            for(int j=0;j<person.arrEmail.count;j++){
                [as addButtonWithTitle:[(NSDictionary*)[person.arrEmail objectAtIndex:j] objectForKey:@"email"]];
            }
            
            [as addButtonWithTitle:@"キャンセル"];
            as.cancelButtonIndex = person.arrPhone.count;
            as.destructiveButtonIndex = 0;
            [as showInView:self.view];
        }else{
            person.flag = (NSInteger*)1;
            [[(ContactCell *)[self.addressList cellForRowAtIndexPath:indexPath] radioBt] setImage:[UIImage imageNamed:@"UI_checkbox_on"] forState :UIControlStateNormal];
            selectedcount++;
            shotBtn.enabled = YES;
            [selectedData setValue:person forKey:[NSString stringWithFormat:@"%d-%d", section, (int)indexPath.row]];
        }
    }else{
        person.flag = 0;
        [selectedData removeObjectForKey:[NSString stringWithFormat:@"%d-%d", section, (int)indexPath.row]];
        // 選択解除
        [[(ContactCell *)[self.addressList cellForRowAtIndexPath:indexPath] radioBt] setImage:[UIImage imageNamed:@"UI_checkbox_off"] forState :UIControlStateNormal];
        selectedcount--;
        if(selectedcount <= 0){
            shotBtn.enabled = NO;
        }
        
    }
}

#pragma mark SearchBar

-(void)searchBar:(UISearchBar*)searchBar textDidChange:(NSString*)text
{
    if(text.length == 0)
        isFiltered = FALSE;
    else
    {
        isFiltered = true;
        arrfiltered = [[NSMutableArray alloc] init];
        for (Person *person in tableData)
        {
            NSRange nameRange = [person.fullName rangeOfString:text options:NSCaseInsensitiveSearch];
            if(nameRange.location != NSNotFound)
                [arrfiltered addObject:person];
        }
    }
    [addressList reloadData];
}

- (void)searchBarSearchButtonClicked:(UISearchBar *)searchBar
{
    [friendsSearchBar resignFirstResponder];
    [addressSearchBar resignFirstResponder];
}

// アクションシートのボタンが押された時に呼ばれるデリゲート例文
-(void)actionSheet:(UIActionSheet*)actionSheet clickedButtonAtIndex:(NSInteger)buttonIndex {
    
    Person *person;
    
    if (isFiltered)
        person = [arrfiltered objectAtIndex:lastIndexPath.row];
    else
        person = [self.tableData objectAtIndex:lastIndexPath.row];
    
    
    if(buttonIndex != person.arrPhone.count){
        person.flag = (NSInteger*)buttonIndex + 1;
        selectedcount++;
        shotBtn.enabled = YES;
        [selectedData setValue:person forKey:[NSString stringWithFormat:@"%d-%d", section, (int)lastIndexPath.row]];
    }
    
    lastIndexPath = nil;
    
    [addressList reloadData];
    
}

/**
 * shotボタンタップイベント
 */
- (void)onPushShotButton:(UIButton *)btn
{
    [self toShot];
}

-(void)toShot
{
    [self.navigationController pushViewController:[[CameraViewController alloc] init:selectedData] animated:YES];
}

/*NSPredicate*/

@end
