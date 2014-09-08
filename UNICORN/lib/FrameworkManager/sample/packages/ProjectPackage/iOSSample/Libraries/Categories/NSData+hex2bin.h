//
//  Created by 八幡 洋一 on 10/12/02.
//  Copyright 2010 __MyCompanyName__. All rights reserved.
//

@interface NSData (HexStringConvert)
+(NSData*) dataWithHexString:(NSString*)string;
-(id) initWithHexString:(NSString*)string;
@end
