//
//  ABCell.h
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import <UIKit/UIKit.h>
#define TABLE_VIEW_HEIGHT_FOR_ROW 65
#define SELECTED_FONT_COLOR       [UIColor colorWithRed:1.00 green:0.67 blue:0.00 alpha:1.0]

@interface ContactCell : UITableViewCell

@property (nonatomic, strong) UIImageView *imgUser;
@property (nonatomic, strong) UILabel *lblName;
@property (nonatomic, strong) UIButton *radioBt;
@end
