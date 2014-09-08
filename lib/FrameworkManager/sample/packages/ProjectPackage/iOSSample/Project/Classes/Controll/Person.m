//
//  Person.m
//  Withly
//
//  Created by admin on 7/24/13.
//  Copyright (c) 2013 n00886. All rights reserved.
//

#import "Person.h"

@implementation Person
@synthesize firstName, lastName, fullName, firstNamePhonetic, lastNamePhonetic, homeEmail, workEmail, image, mobile, flag, arrEmail, arrPhone;
- (id)init
{
    self = [super init];
    if (self) {
        arrEmail = [NSMutableArray array];
        arrPhone = [NSMutableArray array];
    }
    return self;
}

@end
