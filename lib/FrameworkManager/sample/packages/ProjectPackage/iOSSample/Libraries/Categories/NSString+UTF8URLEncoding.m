
#import "NSString+UTF8URLEncoding.h"

@implementation NSString (UTF8URLEncodingConvert)

+ (NSString*)UTF8URLEncoding:(NSString *)argStr
{
    return ((NSString*)CFBridgingRelease(CFURLCreateStringByAddingPercentEscapes(kCFAllocatorDefault,
                                                                (CFStringRef)argStr,
                                                                NULL,
                                                                (CFStringRef)@"!*'();:@&=+$,/?%#[]",
                                                                kCFStringEncodingUTF8)));
}

@end
