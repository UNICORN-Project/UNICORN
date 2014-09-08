@implementation NSData (HexStringConvert)

+(NSData*) dataWithHexString:(NSString*)string
{
	NSMutableData* data = [NSMutableData data];
	
	for(int i=0; i<[string length]/2; i++)
	{
		int size = 2;
		if(i*2+1==[string length]){
			size = 1;
        }

		unsigned int intData = 0;
		sscanf([[string substringWithRange:NSMakeRange(i*2, size)] UTF8String], "%02X", &intData);
		unsigned char charData = intData;
		[data appendBytes:&charData length:1];
	}
	
	return data;
}

-(id) initWithHexString:(NSString*)string
{
	self = [self initWithData:[NSData dataWithHexString:string]];
    if(self)
	{
	}
	return self;
}

@end
