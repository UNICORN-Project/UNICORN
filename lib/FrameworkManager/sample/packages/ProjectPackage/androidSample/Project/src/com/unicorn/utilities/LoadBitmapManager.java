package com.unicorn.utilities;

import java.util.concurrent.BlockingQueue;
import java.util.concurrent.LinkedBlockingQueue;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.widget.ImageView;
import android.widget.ProgressBar;

/**
 * 画像ダウンローダークラス
 * 
 */
public class LoadBitmapManager {

	private static final int THREAD_MAX_NUM = 3;

	private static BlockingQueue<LoadBitmapItem> downloadQueue;
	private static Handler handler;
	private static Context mContext;

	/**
	 * 始めて使われるときに初期化される。
	 */
	static {
		/*
		 * 画像情報を貯めるためのキュー
		 */
		downloadQueue = new LinkedBlockingQueue<LoadBitmapItem>();

		/*
		 * スレッド最大数まで画像ダウンロードスレッドを作成
		 */
		for (int i = 0; i < THREAD_MAX_NUM; i++) {
			new Thread(new DownloadWorker()).start();
		}

		/*
		 * 画像ダウンロード後にメッセージを受信するハンドラーを作成
		 */
		handler = new Handler() {
			@Override
			public void handleMessage(Message msg) {
				/*
				 * 取得したメッセージから画像情報を取得
				 */
				LoadBitmapItem item = (LoadBitmapItem) msg.obj;

				/*
				 * 画像ダウンロードがうまくいっていた場合はイメージビューに設定
				 */
				if (item.getBitmap() != null) {
					item.getImgView().setImageBitmap(item.getBitmap());
					item.getImgView().setVisibility(View.VISIBLE);
				}

				// プログレスバーを隠し、取得した画像を表示
				if (null != item.getProgress()) {
					item.getProgress().setVisibility(View.GONE);
				}
			}
		};
	}

	public static void clearQueue() {
		downloadQueue.clear();
	}

	/**
	 * 引数として渡されたurlで画像をダウンロードしてImageViewに対して 画像を設定する。
	 * 
	 * @param imgView
	 * @param url
	 */
	public static void doDownloadBitmap(Context context, ImageView imgView, ProgressBar progress,
			String url, boolean isMask, int sideLength) {

		mContext = context;
		/*
		 * ダウンロードキューに入れる
		 */
		LoadBitmapItem item = new LoadBitmapItem();
		item.setImgView(imgView);
		item.setProgress(progress);
		item.setUrl(url);
		item.setMask(isMask);
		item.setSideLength(sideLength);
		downloadQueue.offer(item);

		return;
	}

	/**
	 * 実際に画像をダウンロードするワーカー
	 * 
	 * @author satohu20xx
	 */
	private static class DownloadWorker implements Runnable {

		@Override
		public void run() {

			/*
			 * 画像ダウンロードスレッドは常に動き続けるから無限ループ
			 */
			for (;;) {
				Bitmap bitmap = null;
				LoadBitmapItem item;

				try {
					/*
					 * キューに値が入ったら呼び出される nullの状態ではwaitしている
					 */
					item = downloadQueue.take();
				} catch (Exception ex) {
					Log.e("ERROR", "", ex);
					continue;
				}

				/*
				 * ダウンロード
				 */
				try {
					bitmap = ImageCache.getImage(item.getUrl());
					if (bitmap == null) {
						bitmap = BitmapFactory.decodeStream(HttpClientAgent.getBufferedHttpEntity(
								mContext, item.getUrl()).getContent());

						if (bitmap != null) {
							// 取得した画像データをキャッシュに保持
							ImageCache.setImage(item.getUrl(), bitmap);
						}
					}
				} catch (Exception e) {

				}

				item.setBitmap(bitmap);

				/*
				 * 取得した画像情報でメッセージを作って投げる
				 */
				Message msg = new Message();
				msg.obj = item;
				handler.sendMessage(msg);
			}
		}
	}
}