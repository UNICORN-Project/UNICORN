//
//  Person.h
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Person : NSObject

@property (nonatomic, strong) NSData   *image;
@property (nonatomic, strong) NSString *firstName;
@property (nonatomic, strong) NSString *lastName;
@property (nonatomic, strong) NSString *fullName;
@property (nonatomic, strong) NSString *firstNamePhonetic;
@property (nonatomic, strong) NSString *lastNamePhonetic;
@property (nonatomic, strong) NSString *homeEmail;
@property (nonatomic, strong) NSString *workEmail;
@property (nonatomic, strong) NSString *mobile;
@property (nonatomic, strong) NSMutableArray *arrEmail;
@property (nonatomic, strong) NSMutableArray *arrPhone;
@property  NSInteger *flag;
@end
