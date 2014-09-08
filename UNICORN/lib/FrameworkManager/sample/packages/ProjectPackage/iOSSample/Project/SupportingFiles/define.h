//
//  define.h
//  Sample
//
//  Created by saimushi on 2014/06/03.
//  Copyright (c) 2014年 shuhei_ohono. All rights reserved.
//

#ifndef Sample_define_h
#define Sample_define_h

// 各種マクロ
#define APPDELEGATE ((AppDelegate*)[[UIApplication sharedApplication] delegate])
#define RGBA(r, g, b, a) [UIColor colorWithRed:r/255.0 green:g/255.0 blue:b/255.0 alpha:a]

// XXX ローカルテスト用フラグ
#define LOCAL 1

// Application URL setting
#ifdef DEBUG
#ifdef LOCAL
// XXX ローカルテスト用
# define PROTOCOL @"http"
# define DOMAIN_NAME @"localhost"
# define URL_BASE @"/workspace/UNICORN/src/lib/FrameworkManager/template/managedocs/"
#else
// XXX ステージング用
# define PROTOCOL @"http"
# define DOMAIN_NAME @"api.test.Sample.me"
# define URL_BASE @"/"
#endif
#else
// XXX 本番用(現在はステージング用)
# define PROTOCOL @"http"
# define DOMAIN_NAME @"api.test.Sample.me"
# define URL_BASE @"/"
#endif

// デフォルトタイムアウトは短めの20秒に設定しています。
#define TIMEOUT 20
#define COOKIE_TOKEN_NAME @"token"
#define SESSION_CRYPT_KEY               @"bdcc54fba7d9856c"
#define SESSION_CRYPT_IV                @"ccfd172a95aqqd9a"

// サイズ定義
#define navibar_title_size             16

#endif
