//
//  Person.h
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Room : NSObject

@property (nonatomic, strong) NSString *roomId;
@property (nonatomic, strong) NSString *roomName;
@property  NSInteger *badgeCount;
@property (nonatomic, strong) NSData   *image;

@end
