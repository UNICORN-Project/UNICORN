//
//  MovieModel.h
//
//  Created by saimushi on 2014/06/17.
//  Copyright (c) 2014年 saimushi. All rights reserved.
//

#import "ModelBase.h"

@interface MovieModel : ModelBase
{
    NSString *thumbnail;
    NSString *url;
}

@property (strong, nonatomic) NSString *thumbnail;
@property (strong, nonatomic) NSString *url;

/* 独自実装 */
/* ローカルに書きだしたMP4をMovieモデルに保存する場合 */
- (BOOL)saveMovie:(NSURL *)argLocalMP4FileURL :(NSString *)argTimeLineID :(RequestCompletionHandler)argCompletionHandler;;
/* ローカルに書きだしたMP4のサムネイルをMovieモデルに保存する場合 */
- (BOOL)saveThumbnail:(NSURL *)argLocalImageFileURL :(NSString *)argTimeLineID :(RequestCompletionHandler)argCompletionHandler;;

@end
