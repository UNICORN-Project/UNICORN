//
//  ABInviteViewController.h
//  Withly
//
//  Created by ThinhHQ on 7/23/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "common.h"
#import "FriendModel.h"
#import "Person.h"

@interface ABInviteViewController : UIViewController<UITableViewDataSource, UITableViewDelegate, UISearchBarDelegate,UIActionSheetDelegate> {
    UISearchBar     *addressSearchBar;
    UISearchBar     *friendsSearchBar;
    NSMutableArray  *arrfiltered;
    //NSArray         *arrfbFriend;
    bool            isFiltered;
    bool 			isChoiceUser;
    NSInteger       intIDChoice;
    NSIndexPath    *indexPathSelected;
    NSString *albumID;
    BOOL isFlowGroupEdit;

    NSDate *viewStayStartTime;
    NSDate *viewStayEndTime;
}

@property (nonatomic) bool isFirstimeInvite;
@property (nonatomic, strong) NSMutableArray *tableData;
@property (nonatomic, strong) UITableView *addressList;
@property (nonatomic, strong) UITableView *friendList;
@property (nonatomic, strong) UISegmentedControl *segmentedControl;
@property (strong, nonatomic) NSString *albumID;
@property (nonatomic) BOOL isFlowGroupEdit;

- (void)setDisplayedFriends:(NSMutableArray *) listOfFriends;
- (void)onPushShotButton:(UIButton*)btn;

@end
