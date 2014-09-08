//
//  Created by 八幡 洋一 on 10/12/02.
//  Copyright 2010 __MyCompanyName__. All rights reserved.
//

@interface NSString (HexStringConvert)
+(NSString*) stringHexWithData:(NSData*)data;
-(id) initHexWithData:(NSData*)data;
@end
