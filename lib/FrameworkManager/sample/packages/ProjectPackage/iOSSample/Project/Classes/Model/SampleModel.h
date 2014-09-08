//
//  SampleModel.h
//
//  Created by saimushi on 2014/07/07.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "ModelBase.h"

@interface SampleModel : ModelBase
{
    NSString *owner_id;
    NSString *name;
    NSString *created;
    NSString *modified;
    NSString *available;
}

@property (strong, nonatomic) NSString *owner_id;
@property (strong, nonatomic) NSString *name;
@property (strong, nonatomic) NSString *created;
@property (strong, nonatomic) NSString *modified;
@property (strong, nonatomic) NSString *available;

@end
