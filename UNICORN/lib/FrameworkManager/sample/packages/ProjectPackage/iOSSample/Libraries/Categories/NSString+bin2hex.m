@implementation NSString (HexStringConvert)

+(NSString*) stringHexWithData:(NSData*)data
{
	NSMutableString* hexString = [NSMutableString string];
	const unsigned char* bytedata = [data bytes];
	for(int i=0; i<[data length]; ++i)
		[hexString appendFormat:@"%02x", bytedata[i]];
	return hexString;
}

-(id) initHexWithData:(NSData*)data
{
	NSMutableString* hexString = [NSMutableString string];
	const unsigned char* bytedata = [data bytes];
	for(int i=0; i<[data length]; ++i)
		[hexString appendFormat:@"%02x", bytedata[i]];
    self = [self initWithString:hexString];
	if(self)
	{
	}
	return self;
}

@end
